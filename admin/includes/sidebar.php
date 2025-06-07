<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="position-sticky pt-3">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="sidebar-heading mb-0">
                <i class="fas fa-graduation-cap me-2"></i>
                NEXX Admin
            </h5>
            <button id="sidebarToggle" class="btn btn-link text-white p-0 d-none d-md-block">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <div class="px-3 mb-4">
            <div class="gradient-bg rounded-3 p-3 text-white text-center">
                <div class="d-inline-block rounded-circle bg-white p-2 mb-2">
                    <i class="fas fa-user text-primary"></i>
                </div>
                <div>
                    <strong>Admin</strong>
                    <div class="small opacity-75">Administrator</div>
                </div>
            </div>
        </div>
        <ul class="nav flex-column mb-auto px-2">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>index.php">
                    <i class="fas fa-home me-2"></i>
                    <span class="nav-text">Home</span>
                </a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link <?php echo $current_page === 'manage.php' ? 'active' : ''; ?>" href="<?php echo $current_page === 'manage.php' ? '#' : $admin_root . 'subjects/manage.php'; ?>">
                    <i class="fas fa-book me-2"></i>
                    <span class="nav-text">Manage Subjects</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link" href="<?php echo $admin_root; ?>logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>