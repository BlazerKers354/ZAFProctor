<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        try {
            $query = User::with('role');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                });
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->where('role_id', $request->role);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $users = $query->latest()->paginate(15)->withQueryString();
            $roles = Role::all();

            return view('admin.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Log::error('Failed to load users list: ' . $e->getMessage());
            
            return view('admin.users.index', [
                'users' => collect(),
                'roles' => collect(),
            ])->with('error', 'Gagal memuat daftar pengguna.');
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'student_id' => ['nullable', 'string', 'max:50', 'unique:users'],
                'phone' => ['nullable', 'string', 'max:20'],
                'role_id' => ['required', 'exists:roles,id'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'is_active' => ['boolean'],
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['is_approved'] = true; // Admin-created users are auto-approved

            User::create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dibuat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal membuat user. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        try {
            $user->load(['role', 'enrolledCourses', 'taughtCourses', 'examAttempts.exam']);

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('Failed to load user details: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return view('admin.users.show', compact('user'))
                ->with('error', 'Beberapa data tidak dapat dimuat.');
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'student_id' => ['nullable', 'string', 'max:50', 'unique:users,student_id,' . $user->id],
                'phone' => ['nullable', 'string', 'max:20'],
                'role_id' => ['required', 'exists:roles,id'],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'is_active' => ['boolean'],
            ];

            // If password is being changed, require current password
            if ($request->filled('password')) {
                $rules['current_password'] = ['required', 'string'];
            }

            $validated = $request->validate($rules);

            // Verify current password if changing password
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors([
                        'current_password' => 'Password lama tidak sesuai.'
                    ])->withInput();
                }
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Remove current_password from validated data
            unset($validated['current_password']);

            $validated['is_active'] = $request->boolean('is_active', true);

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui user. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return back()->with('error', 'Gagal menghapus user. Silakan coba lagi.');
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "User berhasil {$status}.");
        } catch (\Exception $e) {
            Log::error('Failed to toggle user status: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return back()->with('error', 'Gagal mengubah status user.');
        }
    }

    /**
     * Approve a pending teacher account.
     */
    public function approve(User $user): RedirectResponse
    {
        try {
            if ($user->is_approved) {
                return back()->with('error', 'User sudah disetujui sebelumnya.');
            }

            $user->approve();

            // TODO: Send approval notification email

            return back()->with('success', "Akun guru {$user->name} berhasil disetujui.");
        } catch (\Exception $e) {
            Log::error('Failed to approve user: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return back()->with('error', 'Gagal menyetujui akun. Silakan coba lagi.');
        }
    }

    /**
     * Reject a pending teacher account.
     */
    public function reject(User $user): RedirectResponse
    {
        try {
            if ($user->is_approved) {
                return back()->with('error', 'User sudah disetujui, tidak bisa ditolak.');
            }

            $user->delete();

            // TODO: Send rejection notification email

            return back()->with('success', "Pendaftaran guru berhasil ditolak dan akun dihapus.");
        } catch (\Exception $e) {
            Log::error('Failed to reject user: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            return back()->with('error', 'Gagal menolak pendaftaran. Silakan coba lagi.');
        }
    }

    /**
     * Display pending approval users.
     */
    public function pendingApproval(): View
    {
        try {
            $pendingUsers = User::with('role')
                ->pendingApproval()
                ->latest()
                ->paginate(15);

            return view('admin.users.pending', compact('pendingUsers'));
        } catch (\Exception $e) {
            Log::error('Failed to load pending users: ' . $e->getMessage());
            
            return view('admin.users.pending', ['pendingUsers' => collect()])
                ->with('error', 'Gagal memuat daftar pengguna pending.');
        }
    }
}
