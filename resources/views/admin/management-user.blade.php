@extends('layouts.admin')

@section('content')

<div class="mb-4">
    <h2 class="h4 font-weight-bold text-gray-800">Management User</h2>
    <p class="text-muted">Kelola user, role, dan permission</p>
</div>

<!-- Alert untuk Password Reset Success -->
@if(session('new_password'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="alert-heading mb-2">
                <i class="fas fa-check-circle me-2"></i>Password Berhasil Di-Reset!
            </h6>
            <p class="mb-2">
                <strong>User:</strong> {{ session('user_name') }}<br>
                <strong>Password Baru:</strong> 
                <code id="newPwdCode" style="background: #f0f0f0; padding: 5px 10px; border-radius: 4px; font-family: monospace;">{{ session('new_password') }}</code>
                <button class="btn btn-sm btn-outline-success ms-2" onclick="copyPassword()">
                    <i class="fas fa-copy me-1"></i>Copy
                </button>
            </p>
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Berikan password ini kepada user. Password di-encrypt di database.
            </small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

<!-- Role Management Section -->
<div class="card mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-tag me-2"></i>Role Management
        </h6>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h6 class="mb-3">Tambah Role Baru</h6>
                <form action="{{ route('admin.roles.store') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="role_name" class="form-control" placeholder="Nama Role" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add
                    </button>
                </form>
                @if($errors->has('role_name'))
                    <div class="alert alert-danger mt-2 mb-0">{{ $errors->first('role_name') }}</div>
                @endif
            </div>
        </div>

        <hr>

        <h6 class="mb-3">Daftar Role</h6>
        <div class="row">
            @forelse($roles as $role)
                <div class="col-md-3 mb-3">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="m-0">{{ $role }}</h6>
                                @if($role !== 'user' && $role !== 'admin') <!-- Jangan hapus role default -->
                                    <form action="{{ route('admin.roles.delete', $role) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus role ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Default</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">Belum ada role</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- User Profile Management Section -->
<div class="card">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-users me-2"></i>User Profile
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2" style="width: 32px; height: 32px; background: #0d6efd; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <input type="text" value="{{ $user->name }}" class="form-control form-control-sm" disabled style="width: 150px;">
                                </div>
                            </td>
                            <td>
                                <input type="email" value="{{ $user->email }}" class="form-control form-control-sm" disabled style="width: 200px;">
                            </td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="password" id="pwd_{{ $user->id }}" class="form-control" value="••••••••" disabled>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword({{ $user->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline; margin-top: 5px;">
                                    @csrf
                                    <input type="hidden" name="new_password" id="newPwd_{{ $user->id }}" value="">
                                    <button type="button" class="btn btn-xs btn-warning mt-1" onclick="resetPassword({{ $user->id }})" title="Reset password user">
                                        <i class="fas fa-redo-alt me-1"></i>Reset
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 120px;">
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.users.delete', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox me-2"></i>Belum ada user
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->count() > 0)
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info mt-4" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Info:</strong>
    <ul class="mb-0 mt-2">
        <li>Klik icon mata untuk lihat/sembunyikan password</li>
        <li>Pilih role di dropdown untuk mengubah role user</li>
        <li>Klik icon trash untuk menghapus user</li>
        <li>Role default (User, Admin) tidak bisa dihapus</li>
    </ul>
</div>

<script>
function togglePassword(userId) {
    const input = document.getElementById('pwd_' + userId);
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        input.value = generatePassword();
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        input.value = '••••••••';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function generatePassword() {
    // Generate random password 12 characters
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

function resetPassword(userId) {
    const newPassword = generatePassword();
    const confirmReset = confirm('Password akan di-reset ke:\n\n' + newPassword + '\n\nLanjutkan?');
    
    if (confirmReset) {
        document.getElementById('newPwd_' + userId).value = newPassword;
        const form = event.target.closest('form');
        form.submit();
    }
}

function copyPassword() {
    const passwordCode = document.getElementById('newPwdCode');
    const password = passwordCode.innerText;
    
    navigator.clipboard.writeText(password).then(() => {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.add('btn-outline-success');
            btn.classList.remove('btn-success');
        }, 2000);
    });
}
</script>

@endsection
