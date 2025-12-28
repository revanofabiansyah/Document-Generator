<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Tampilkan halaman management user
     */
    public function index()
    {
        // Get all users
        $users = User::paginate(10);
        
        // Get available roles (dari database atau hardcoded)
        $roles = ['user', 'admin', 'superadmin'];

        return view('admin.management-user', compact('users', 'roles'));
    }

    /**
     * Tambah role baru
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|min:3|max:50|unique:roles,name',
        ], [
            'role_name.required' => 'Nama role harus diisi',
            'role_name.unique' => 'Role sudah ada',
            'role_name.min' => 'Nama role minimal 3 karakter',
        ]);

        // Store role ke database atau session
        // Untuk sekarang, bisa disimpan di database atau session
        
        return redirect()->route('admin.users.management')
            ->with('success', 'Role berhasil ditambahkan!');
    }

    /**
     * Hapus role
     */
    public function deleteRole($role)
    {
        // Cegah menghapus role default
        if (in_array($role, ['user', 'admin', 'superadmin'])) {
            return redirect()->route('admin.users.management')
                ->with('error', 'Role default tidak bisa dihapus!');
        }

        // Delete logic
        
        return redirect()->route('admin.users.management')
            ->with('success', 'Role berhasil dihapus!');
    }

    /**
     * Update role user
     */
    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin,superadmin',
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.management')
            ->with('success', 'Role user berhasil diubah!');
    }

    /**
     * Hapus user
     */
    public function deleteUser(User $user)
    {
        // Cegah menghapus user sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.management')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.management')
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Edit user (nama, email)
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.management')
            ->with('success', 'User berhasil diubah!');
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $newPassword = $validated['new_password'];
        
        // Hash password sebelum save
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Return dengan password dalam plain text (hanya untuk ditampilkan saat reset)
        return redirect()->route('admin.users.management')
            ->with('success', 'Password user "' . $user->name . '" berhasil di-reset!')
            ->with('new_password', $newPassword)
            ->with('user_name', $user->name);
    }
}
