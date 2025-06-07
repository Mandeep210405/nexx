<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Dashboard';

// Get statistics
$conn = getDBConnection();

// Get total subjects count
$stmt = $conn->query("SELECT COUNT(*) FROM subjects");
$total_subjects = $stmt->fetchColumn();

// Get total practicals count
$stmt = $conn->query("SELECT COUNT(*) FROM practicals");
$total_practicals = $stmt->fetchColumn();

// Get total study materials count
$stmt = $conn->query("SELECT COUNT(*) FROM study_materials");
$total_materials = $stmt->fetchColumn();

// Get total previous papers count
$stmt = $conn->query("SELECT COUNT(*) FROM previous_papers");
$total_papers = $stmt->fetchColumn();

// Get total users count
$stmt = $conn->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Get recent admin actions
$stmt = $conn->query("
    SELECT al.*, au.username
    FROM admin_logs al
    JOIN admin_users au ON al.admin_id = au.id
    ORDER BY al.created_at DESC
    LIMIT 5
");
$recent_actions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get subjects by branch
$stmt = $conn->query("
    SELECT branch, COUNT(*) as count
    FROM subjects
    GROUP BY branch
");
$subjects_by_branch = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get admin details
$admin = getAdminDetails($_SESSION['admin_id']);

// Get statistics
$stats = [
    'subjects' => $conn->query("SELECT COUNT(*) FROM subjects")->fetchColumn(),
    'practicals' => $conn->query("SELECT COUNT(*) FROM practicals")->fetchColumn(),
    'study_materials' => $conn->query("SELECT COUNT(*) FROM study_materials")->fetchColumn(),
    'previous_papers' => $conn->query("SELECT COUNT(*) FROM previous_papers")->fetchColumn(),
    'video_lectures' => $conn->query("SELECT COUNT(*) FROM video_lectures")->fetchColumn()
];

// Include header
$page_title = 'Dashboard';
include 'includes/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Welcome to NEXX Admin Panel</h2>
                    <p class="text-muted">Manage your educational content from this dashboard. Here's an overview of your site's content.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 gradient-bg me-3">
                            <i class="fas fa-book text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Subjects</h6>
                            <h3 class="mb-0"><?php echo $stats['subjects']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 gradient-bg me-3">
                            <i class="fas fa-code text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Practicals</h6>
                            <h3 class="mb-0"><?php echo $stats['practicals']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 gradient-bg me-3">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Study Materials</h6>
                            <h3 class="mb-0"><?php echo $stats['study_materials']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 gradient-bg me-3">
                            <i class="fas fa-video text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Video Lectures</h6>
                            <h3 class="mb-0"><?php echo $stats['video_lectures']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="subjects/manage.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-book me-2"></i> Manage Subjects
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="/nexx/index.php" target="_blank" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-external-link-alt me-2"></i> View Website
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Admin</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_actions)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No recent activity found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_actions as $action): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($action['username']); ?></td>
                                            <td><?php echo htmlspecialchars($action['action']); ?></td>
                                            <td><?php echo htmlspecialchars($action['details']); ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', strtotime($action['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';
?>