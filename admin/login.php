<?php
require_once 'includes/functions.php';

// Check if already logged in
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            $conn = getDBConnection();
            $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Log the login action
                logAdminAction($admin['id'], 'login', 'Admin logged in successfully');

                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - NEXX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8C4AEA;
            --primary-dark: #764ba2;
            --primary-light: #667eea;
            --dark-bg: #0E0B1A;
            --dark-bg-light: #1E1B2A;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid rgba(118, 75, 162, 0.2);
        }

        .login-header {
            background-color: var(--dark-bg);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid var(--primary-dark);
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .login-header .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            font-weight: bold;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid rgba(118, 75, 162, 0.2);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(140, 74, 234, 0.25);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(118, 75, 162, 0.3);
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            padding: 10px 15px;
            border-radius: 10px;
        }

        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-site a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-to-site a:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <div class="logo gradient-bg">
                    <span>N</span>
                </div>
                <h1>NEXX Admin</h1>
                <p class="mb-0">Enter your credentials to access the admin panel</p>
            </div>

            <div class="login-body">
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </button>

                    <div class="back-to-site mt-4">
                        <a href="/nexx/index.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Main Site
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>