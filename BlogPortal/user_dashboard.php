<?php
session_start();

// --- PREVENT UNAUTHORIZED ACCESS ---
if (!isset($_SESSION['user_username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['user_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: #f5f5f7;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.dashboard-wrapper {
    width: 100%;
    max-width: 1200px;
    height: 90vh;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 30px rgba(0, 0, 0, 0.06);
}

.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 28px 40px;
    color: white;
}

.header h2 {
    font-size: 26px;
    font-weight: 600;
    letter-spacing: -0.5px;
}

.main-container {
    display: flex;
    height: calc(100% - 84px);
}

.sidebar {
    background: #fafafa;
    width: 220px;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border-right: 1px solid #e5e5e5;
}

.sidebar-btn {
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.3px;
}

.post-btn {
    background: #667eea;
    color: white;
}

.post-btn:hover {
    background: #5568d3;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.logout-btn {
    background: white;
    color: #6b7280;
    border: 1px solid #e5e5e5;
    margin-top: auto;
}

.logout-btn:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}

.content-area {
    flex: 1;
    background: white;
    padding: 40px;
    overflow-y: auto;
}

/* Custom scrollbar */
.content-area::-webkit-scrollbar {
    width: 8px;
}

.content-area::-webkit-scrollbar-track {
    background: transparent;
}

.content-area::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.content-area::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.demo-content {
    max-width: 800px;
}

.demo-content h3 {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.demo-content p {
    color: #6b7280;
    line-height: 1.7;
    font-size: 15px;
    margin-bottom: 12px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.stat-card {
    background: #f9fafb;
    padding: 24px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.stat-card h4 {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-card p {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}
</style>
</head>

<body>

<div class="dashboard-wrapper">
    <!-- Header -->
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <button class="sidebar-btn post-btn" onclick="window.location.href='post_blog.php'">
                Post Blog
            </button>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="sidebar-btn logout-btn" name="logout">
                    Logout
                </button>
            </form>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="demo-content">
                <h3>Dashboard Overview</h3>
                <p>Welcome to your personal dashboard. Here you can manage your blog posts and view your activity.</p>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Posts</h4>
                        <p>0</p>
                    </div>
                    <div class="stat-card">
                        <h4>Views</h4>
                        <p>0</p>
                    </div>
                    <div class="stat-card">
                        <h4>Comments</h4>
                        <p>0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>