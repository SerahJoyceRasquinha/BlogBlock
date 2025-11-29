<?php
session_start();

// --- PREVENT UNAUTHORIZED ACCESS ---
if (!isset($_SESSION['user_username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['user_username'];

// --- DATABASE CONNECTION ---
$conn = new mysqli('localhost', 'root', '', 'blogblock');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- FETCH USER BLOGS ---
$stmt = $conn->prepare("SELECT id, title, content, created_at FROM user_blog_posts WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Blog History</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 35px;
            color: #2d3748;
            font-size: 32px;
            font-weight: 600;
        }

        .blog-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .blog-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .blog-card h3 {
            margin-bottom: 12px;
            color: #1a202c;
            font-size: 22px;
            font-weight: 600;
        }

        .blog-card p {
            color: #4a5568;
            line-height: 1.7;
            font-size: 15px;
        }

        .date {
            font-size: 13px;
            color: #a0aec0;
            margin-bottom: 12px;
            font-weight: 500;
            display: inline-block;
            background: #f7fafc;
            padding: 4px 12px;
            border-radius: 6px;
        }

        .back-btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .no-blogs {
            text-align: center;
            color: #718096;
            padding: 60px 20px;
            font-size: 16px;
        }

        .no-blogs::before {
            content: "📝";
            display: block;
            font-size: 48px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 24px;
            }

            h2 {
                font-size: 26px;
            }

            .blog-card {
                padding: 18px;
            }

            .blog-card h3 {
                font-size: 19px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Blog History</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="blog-card">
                    <div class="date">Posted on: <?php echo date('F j, Y', strtotime($row['created_at'])); ?></div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-blogs">You haven't posted any blogs yet.</p>
        <?php endif; ?>

        <?php
        $stmt->close();
        $conn->close();
        ?>

        <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>