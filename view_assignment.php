<?php
require_once 'config/database.php';
include 'includes/header.php';

// Check if user is logged in and is student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Get assignment details
try {
    $stmt = $conn->prepare("
        SELECT a.*, c.name as classroom_name, c.subject, f.name as faculty_name,
               (SELECT COUNT(*) FROM assignment_submissions 
                WHERE assignment_id = a.id AND student_id = ?) as submission_count
        FROM assignments a
        JOIN classrooms c ON a.classroom_id = c.id
        JOIN faculty f ON a.faculty_id = f.id
        JOIN classroom_students cs ON c.id = cs.classroom_id
        WHERE a.id = ? AND cs.student_id = ?
    ");
    $stmt->execute([$student_id, $assignment_id, $student_id]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        $error = "Assignment not found or you don't have access to it.";
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $assignment = null;
}

// Get student's submission if exists
try {
    $stmt = $conn->prepare("
        SELECT *, 
               CASE 
                   WHEN marks IS NOT NULL AND marks > 0 THEN 'accepted'
                   WHEN feedback IS NOT NULL AND marks IS NULL THEN 'rejected'
                   ELSE 'pending'
               END as status
        FROM assignment_submissions 
        WHERE assignment_id = ? AND student_id = ?
        ORDER BY submitted_at DESC
        LIMIT 1
    ");
    $stmt->execute([$assignment_id, $student_id]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching submission: " . $e->getMessage();
    $submission = null;
}
?>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="student_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'resubmitted'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Assignment resubmitted successfully! Your submission is pending for faculty verification.
        </div>
    <?php endif; ?>

    <?php if ($assignment): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Assignment: <?php echo htmlspecialchars($assignment['title']); ?></h4>
                        <p class="mb-0">Classroom: <?php echo htmlspecialchars($assignment['classroom_name']); ?> - 
                        <?php echo htmlspecialchars($assignment['subject']); ?></p>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Assignment Details</h5>
                            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                            <p><strong>Due Date:</strong> <?php echo date('M d, Y H:i', strtotime($assignment['due_date'])); ?></p>
                            <p><strong>Maximum Marks:</strong> <?php echo $assignment['max_marks']; ?></p>
                            <?php if ($assignment['assignment_file']): ?>
                                <p><strong>Assignment File:</strong> 
                                    <a href="<?php echo htmlspecialchars($assignment['assignment_file']); ?>" 
                                       class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Submission Section -->
                        <div class="mb-4">
                            <h5>Your Submission</h5>
                            <?php if ($submission): ?>
                                <div class="alert <?php echo $submission['status'] === 'rejected' ? 'alert-danger' : 'alert-info'; ?>">
                                    <p><strong>Submission Status:</strong> 
                                        <?php if ($submission['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger text-white">Rejected</span>
                                        <?php elseif ($submission['status'] === 'accepted'): ?>
                                            <span class="badge bg-success text-white">Accepted</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-white">Pending Review</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Submitted At:</strong> <?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></p>
                                    <?php if ($submission['file_path']): ?>
                                        <p><strong>Submitted File:</strong> 
                                            <a href="<?php echo htmlspecialchars($submission['file_path']); ?>" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($submission['marks'] !== null): ?>
                                        <p><strong>Marks Obtained:</strong> <?php echo $submission['marks']; ?>/<?php echo $assignment['max_marks']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($submission['feedback']): ?>
                                        <p><strong>Feedback:</strong> <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($submission['status'] === 'rejected'): ?>
                                        <div class="mt-3">
                                            <a href="submit_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-warning">
                                                <i class="fas fa-redo"></i> Resubmit Assignment
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>You haven't submitted this assignment yet.</p>
                                    <a href="submit_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Submit Assignment
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 