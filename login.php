<?php
require_once 'config/database.php';
include 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'faculty') {
        header("Location: faculty_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    if ($user_type === 'faculty') {
        $stmt = $conn->prepare("SELECT * FROM faculty WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    }
    
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_type'] = $user_type;
        
        if ($user_type === 'faculty') {
            header("Location: faculty_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Login</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success">Registration successful! Please login.</div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Login As</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="faculty" value="faculty" checked>
                                <label class="form-check-label" for="faculty">Faculty</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="student" value="student">
                                <label class="form-check-label" for="student">Student</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 