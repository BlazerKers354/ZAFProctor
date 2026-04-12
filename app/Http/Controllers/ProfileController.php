<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);

            $emailChanged = $validated['email'] !== $user->email;

            $user->fill($validated);

            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            $user->save();

            $status = 'profile-updated';

            if ($emailChanged) {
                $user->sendEmailVerificationNotification();
                $status = 'profile-updated-verification-sent';
            }

            return redirect()->route('profile.edit')
                ->with('status', $status);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
            ]);

            return back()->withErrors(['error' => 'Gagal memperbarui profil. Silakan coba lagi.']);
        }
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'avatar' => ['required', 'image', 'max:2048'],
            ]);

            $user = $request->user();

            // Delete old avatar if exists
            if ($user->avatar) {
                try {
                    Storage::disk('public')->delete($user->avatar);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old avatar: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'avatar_path' => $user->avatar,
                    ]);
                }
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            
            if (!$path) {
                throw new \Exception('Failed to store avatar file');
            }

            $user->update(['avatar' => $path]);

            return redirect()->route('profile.edit')
                ->with('status', 'avatar-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update avatar: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
            ]);

            return back()->withErrors(['avatar' => 'Gagal mengupload avatar. Silakan coba lagi.']);
        }
    }
}
