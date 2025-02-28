<?php 
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Completed</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .completion-card {
            text-align: center;
            max-width: 500px;
            width: 90%;
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 1rem;
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .completion-card img {
            width: 80px;
            margin-bottom: 15px;
        }

        .completion-card h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .completion-card p {
            font-size: 1rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .urdu-text {
            font-family: 'Noto Nastaliq Urdu', serif;
            color: #ff4c4c;
            font-size: 1.3rem;
            font-weight: bold;
            border: 2px dashed #ff4c4c;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-logout {
            background: linear-gradient(45deg, #1976d2, #1e88e5);
            border: none;
            padding: 10px 25px;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-logout:hover {
            background: linear-gradient(45deg, #1565c0, #1976d2);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="completion-card">
        <img src="logo.webp" class="img-fluid" alt="logo">
        <h1>Quiz Attempted!</h1>
        <p>You are doing great! Results will be announced soon.</p>
        <p>Now just logout and go home.</p>
        <p class="urdu-text">براہ کرم بار بار کوئز کا نتیجہ پوچھ کر استاد کو تنگ مت کیجیے گا۔</p>
        <br>
        <a href="logout.php" class="btn btn-primary btn-logout">Logout</a>
    </div>
</body>
</html>

