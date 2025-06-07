<?php
require_once 'config/database.php';
include 'includes/header.php';

// Check if user is logged in and is faculty
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];
$assignment_id = isset($_GET['assignment_id']) ? (int)$_GET['assignment_id'] : 0;
$error = ''; // Initialize error variable
$success = ''; // Initialize success variable

// Verify faculty owns this assignment
try {
    $stmt = $conn->prepare("
        SELECT a.*, c.name as classroom_name, c.subject 
        FROM assignments a
        JOIN classrooms c ON a.classroom_id = c.id
        WHERE a.id = ? AND a.faculty_id = ?
    ");
    $stmt->execute([$assignment_id, $faculty_id]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        $error = "You don't have permission to view this assignment.";
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $assignment = null;
}

// Only show error if assignment is not found
if (!$assignment) {
    $error = "You don't have permission to view this assignment.";
}

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {
    $submission_id = (int)$_POST['submission_id'];
    $marks = (int)$_POST['marks'];
    $feedback = $_POST['feedback'];
    
    try {
        $stmt = $conn->prepare("
            UPDATE assignment_submissions 
            SET marks = ?, feedback = ? 
            WHERE id = ? AND assignment_id = ?
        ");
        $stmt->execute([$marks, $feedback, $submission_id, $assignment_id]);
        
        if ($stmt->rowCount() > 0) {
            $success = "Submission graded successfully!";
        } else {
            $error = "Failed to grade submission. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get submissions
try {
    $stmt = $conn->prepare("
        SELECT s.*, st.name as student_name, st.enrollment_no
        FROM assignment_submissions s
        JOIN students st ON s.student_id = st.id
        WHERE s.assignment_id = ?
        ORDER BY s.submitted_at DESC
    ");
    $stmt->execute([$assignment_id]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching submissions: " . $e->getMessage();
    $submissions = [];
}
?>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="faculty_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($assignment): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Assignment: <?php echo htmlspecialchars($assignment['title']); ?></h4>
                        <p class="mb-0">Classroom: <?php echo htmlspecialchars($assignment['classroom_name']); ?> - 
                        <?php echo htmlspecialchars($assignment['subject']); ?></p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5>Assignment Details</h5>
                            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                            <p><strong>Due Date:</strong> <?php echo date('M d, Y H:i', strtotime($assignment['due_date'])); ?></p>
                            <p><strong>Maximum Marks:</strong> <?php echo $assignment['max_marks']; ?></p>
                        </div>
                        
                        <h5>Student Submissions</h5>
                        <?php if (count($submissions) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Enrollment No.</th>
                                            <th>Submission</th>
                                            <th>File</th>
                                            <th>Submitted At</th>
                                            <th>Marks</th>
                                            <th>Feedback</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['enrollment_no']); ?></td>
                                                <td><?php echo nl2br(htmlspecialchars($submission['submission_text'])); ?></td>
                                                <td>
                                                    <?php if ($submission['file_path']): ?>
                                                        <a href="<?php echo htmlspecialchars($submission['file_path']); ?>" 
                                                           class="btn btn-sm btn-info" target="_blank">Download</a>
                                                    <?php else: ?>
                                                        No file
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                                                <td>
                                                    <?php if ($submission['marks'] !== null): ?>
                                                        <?php echo $submission['marks']; ?>/<?php echo $assignment['max_marks']; ?>
                                                    <?php else: ?>
                                                        Not graded
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo nl2br(htmlspecialchars($submission['feedback'] ?? '')); ?></td>
                                                <td>
                                                    <a href="grade_submission.php?id=<?php echo $submission['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-graduation-cap"></i> Grade
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No submissions found for this assignment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 