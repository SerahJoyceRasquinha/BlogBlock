<?php
session_start();

// --- PREVENT UNAUTHORIZED ACCESS ---
if (!isset($_SESSION['manager_username'])) {
    header("Location: index.php");
    exit;
}

// DB connection
$conn = new mysqli('localhost', 'root', '', 'blogblock');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- DELETE USER OPERATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userToDelete = $_POST['delete_user'];

    // Delete posts first (foreign data)
    $stmt1 = $conn->prepare("DELETE FROM user_blog_posts WHERE username = ?");
    $stmt1->bind_param("s", $userToDelete);
    $stmt1->execute();
    $stmt1->close();

    // Delete user credentials
    $stmt2 = $conn->prepare("DELETE FROM user_credentials WHERE username = ?");
    $stmt2->bind_param("s", $userToDelete);
    $stmt2->execute();
    $stmt2->close();

    echo "<script>alert('User deleted successfully.'); window.location.href='manage_users.php';</script>";
}

// --- FETCH ALL USERS + POST COUNT ---
$query = "
    SELECT u.username, COUNT(b.id) AS post_count
    FROM user_credentials u
    LEFT JOIN user_blog_posts b ON u.username = b.username
    GROUP BY u.username
    ORDER BY u.username ASC
";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - BlogBlock</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 24px 32px;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .back-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #5568d3;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .content {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #0aa504ff 0%, #0addc1ff 100%);
        }

        th {
            padding: 18px 24px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s ease;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        td {
            padding: 18px 24px;
            font-size: 15px;
            color: #374151;
        }

        .username {
            font-weight: 600;
            color: #1f2937;
        }

        .post-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            background: #f3f4f6;
            border-radius: 6px;
            font-weight: 600;
            color: #6b7280;
            padding: 0 12px;
        }

        .delete-form {
            margin: 0;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        }

        .delete-btn:active {
            transform: translateY(0);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
                width: 100%;
            }

            th, td {
                padding: 12px 16px;
                font-size: 14px;
            }

            .back-btn {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Users</h1>
            <button class="back-btn" onclick="window.location.href='manager_dashboard.php'">
                ← Back to Dashboard
            </button>
        </div>

        <div class="content">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Posts</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="username"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td>
                                        <span class="post-count"><?php echo $row['post_count']; ?></span>
                                    </td>
                                    <td>
                                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user and all their posts?');">
                                            <input type="hidden" name="delete_user" value="<?php echo htmlspecialchars($row['username']); ?>">
                                            <button class="delete-btn" type="submit">Delete User</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">
                                    <div>No users found</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>