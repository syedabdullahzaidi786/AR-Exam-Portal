<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Portal - Welcome</title>
    
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
            text-align: center;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 450px;
            width: 100%;
        }

        .welcome-card img {
            width: 100px;
            margin-bottom: 15px;
        }

        .btn-start {
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

        .btn-start:hover {
            background: linear-gradient(45deg, #1565c0, #1976d2);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
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
    <div class="welcome-card">
        <img src="logo.webp" alt="Logo">
        <h2>Welcome to Quiz Portal</h2>
        <p>Your gateway to interactive and exciting quizzes!</p>
        <button onclick="window.location.href='login.php'" class="btn btn-start">Get Started</button>
    </div>

    <!-- Powered By Section (Outside Card) -->
    <div class="powered-by">
        <img src="logo.jpg" alt="AR Developers Logo">
        <span>Powered By: <strong>AR Developers</strong></span>
    </div>
</body>
</html>
