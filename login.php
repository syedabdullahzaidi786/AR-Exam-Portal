<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Portal - Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            font-family: 'Poppins', sans-serif;
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card img {
            width: 80px;
            margin-bottom: 15px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 1rem;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .btn-login {
            background: linear-gradient(45deg, #1976d2, #1e88e5);
            border: none;
            padding: 10px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
            font-weight: bold;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #1565c0, #1976d2);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .signup-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .signup-text a {
            color: #1e88e5;
            font-weight: bold;
            text-decoration: none;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }

        /* Powered By Section */
        .powered-by {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            font-size: 0.%rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .powered-by img {
            width: 60px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="logo.webp" alt="Logo">
        <h3>Student Login</h3>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                if ($user['is_admin'] == 1) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid email or password</div>";
            }
        }
        ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" name="email" class="form-control" placeholder="Student ID" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <p class="signup-text mt-3">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </p>
    </div>

    <!-- Powered By Section (Outside Card) -->
    <div class="powered-by">
        <img src="logo.jpg" alt="AR Developers Logo">
        <span>Powered By: <strong>AR Developers</strong></span>
    </div>
</body>
</html>
