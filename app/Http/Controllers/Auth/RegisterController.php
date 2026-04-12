<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Handle student registration request.
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'student_id' => ['nullable', 'string', 'max:50', 'unique:users,student_id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ], [], [
            'name' => 'Nama',
            'email' => 'Email',
            'student_id' => 'NIS',
            'class_id' => 'Kelas',
            'password' => 'Password',
        ]);

        $studentRole = Role::where('name', Role::STUDENT)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true, // Students are auto-approved
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Log the registration
        AuditLog::log(
            AuditLog::ACTION_CREATE,
            'Siswa baru terdaftar',
            User::class,
            $user->id
        );

        return redirect()->route('dashboard')->with('success', 'Selamat datang! Akun siswa Anda telah berhasil dibuat.');
    }

    /**
     * Handle teacher registration request.
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $teacherRole = Role::where('name', Role::TEACHER)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => false, // Teachers need admin approval
        ]);

        event(new Registered($user));

        // Log the registration
        AuditLog::log(
            AuditLog::ACTION_CREATE,
            'Guru baru mendaftar (menunggu persetujuan)',
            User::class,
            $user->id
        );

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan dari administrator. Anda akan dihubungi melalui email ketika akun sudah disetujui.');
    }
}
