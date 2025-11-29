<?php
session_start();

// --- PREVENT UNAUTHORIZED ACCESS ---
if (!isset($_SESSION['user_username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['user_username'];

// --- DATABASE CONNECTION ---
$conn = new mysqli("localhost", "root", "", "blogblock");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// --- HANDLE BLOG SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === "" || $content === "") {
        $message = "Please fill out all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user_blog_posts (username, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $title, $content);

        if ($stmt->execute()) {
            echo "<script>alert('Blog posted successfully!'); window.location.href='user_dashboard.php';</script>";
            exit;
        } else {
            $message = "Error posting blog.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Blog - BlogBlock</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 700px;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .subtitle {
            color: #718096;
            font-size: 15px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            margin-top: 20px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            height: 200px;
            resize: vertical;
            line-height: 1.6;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #ffffff;
            padding: 16px;
            margin-top: 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #764ba2;
            transform: translateX(-3px);
        }

        .back-btn::before {
            content: "←";
            margin-right: 8px;
            font-size: 18px;
        }

        .error-msg {
            background: #fed7d7;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c53030;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 30px 25px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create New Blog Post</h2>
        <p class="subtitle">Share your thoughts with the world</p>

        <?php if ($message != ""): ?>
            <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" placeholder="Enter an engaging blog title" required>

            <label for="content">Content</label>
            <textarea id="content" name="content" placeholder="Write your blog content here..." required></textarea>

            <button type="submit" class="btn-submit">Publish Blog Post</button>
        </form>

        <a href="user_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>