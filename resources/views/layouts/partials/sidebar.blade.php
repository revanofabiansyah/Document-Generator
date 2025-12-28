<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <!-- Brand/Logo Section -->
            <div class="sb-sidenav-menu-heading" href="{{ route('admin.dashboard') }}" style="padding: 12px 15px 10px 15px; border-bottom: 2px solid #3d3d3d; margin: 0;">
                <div style="text-align: center; margin-top: -9px">
                    <div style="font-size: 30px; font-weight: 800; margin-bottom: -3px; letter-spacing: -1px;">DOC GEN</div>
                    <div style="font-size: 20px; color: #7a8a99; font-weight: 600; letter-spacing: 1px;">ADMIN DASHBOARD</div>
                </div>
            </div>

            <!-- Dashboard -->
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                href="{{ route('admin.dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Admin</span>
            </a>

            <!-- Input Document -->
            <a class="nav-link {{ request()->routeIs('documents.input') ? 'active' : '' }}" 
                href="{{ route('documents.input') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                <span>Input Document</span>
            </a>

            <!-- Edit Document -->
            <a class="nav-link {{ request()->routeIs('documents.edit', 'documents.editor') ? 'active' : '' }}" 
                href="{{ route('documents.edit') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                <span>Edit Document</span>
            </a>

            <!-- Management User -->
            <a class="nav-link {{ request()->routeIs('admin.users.management') ? 'active' : '' }}" 
                href="{{ route('admin.users.management') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                <span>Managemen User</span>
            </a>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="nav-item">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: 12px 20px; display: flex; align-items: center; color: #adb5bd;">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<style>
    .sb-sidenav-menu-heading {
        padding: 12px 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
        color: #6c757d;
        margin: 15px 0 5px 0;
    }

    .sb-sidenav-menu-heading:first-child {
        margin-top: 0;
        margin-bottom: 0;
    }

    .sb-sidenav .nav-link {
        color: #adb5bd;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
        margin: 0;
    }

    .sb-sidenav .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.08);
        border-left-color: #0d6efd;
    }

    .sb-sidenav .nav-link.active {
        color: white;
        background-color: rgba(13, 110, 253, 0.15);
        border-left-color: #0d6efd;
        font-weight: 600;
    }

    .sb-nav-link-icon {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    .sb-sidenav .nav-link span {
        display: inline-block;
    }
</style>