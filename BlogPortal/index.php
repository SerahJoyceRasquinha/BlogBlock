<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'blogblock');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // --- USER SIGNUP ---
    if (isset($_POST['user_signup'])) {
        $username = $_POST['signup_username'];
        $password = $_POST['signup_password'];
        $confirmPassword = $_POST['signup_confirm_password'];

        if ($password !== $confirmPassword) {
            echo "<script>alert('Passwords do not match!');</script>";
        } else {
            $checkStmt = $conn->prepare("SELECT * FROM user_credentials WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                echo "<script>alert('This username already exists. Choose a different one.');</script>";
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $insertStmt = $conn->prepare("INSERT INTO user_credentials (username, password) VALUES (?, ?)");
                $insertStmt->bind_param("ss", $username, $passwordHash);

                if ($insertStmt->execute()) {
                    echo "<script>alert('User registered successfully!'); window.location.href='index.php';</script>";
                } else {
                    echo "<script>alert('Registration failed.');</script>";
                }
                $insertStmt->close();
            }
            $checkStmt->close();
        }
    }

    // --- USER LOGIN ---
    if (isset($_POST['user_login'])) {
        $username = $_POST['login_username'];
        $password = $_POST['login_password'];

        $stmt = $conn->prepare("SELECT password FROM user_credentials WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($dbPassword);
        $stmt->fetch();

        if ($dbPassword && password_verify($password, $dbPassword)) {
            $_SESSION['user_username'] = $username;
            header("Location: user_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid credentials!');</script>";
        }
        $stmt->close();
    }

    // --- MANAGER LOGIN ---
    if (isset($_POST['manager_login'])) {
        $username = $_POST['manager_username'];
        $password = $_POST['manager_password'];

        $stmt = $conn->prepare("SELECT password FROM manager_credentials WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($dbPassword);
        $stmt->fetch();

        if ($dbPassword && password_verify($password, $dbPassword)) {
            $_SESSION['manager_username'] = $username;
            header("Location: manager_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid credentials!');</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Portal</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.container {
    background: white;
    padding: 50px 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
}

.container h1 {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin-bottom: 40px;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.container h1 img {
    height: 80px;
    width: auto;
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.btn-primary {
    padding: 14px 30px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    border: 2px solid #667eea;
    border-radius: 8px;
    background-color: white;
    color: #667eea;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 40px;
    border-radius: 12px;
    width: 90%;
    max-width: 420px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 28px;
    font-weight: 300;
    cursor: pointer;
    color: #999;
    transition: color 0.3s ease;
}

.close:hover {
    color: #333;
}

.modal-content h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.tab-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
}

.tab-btn {
    flex: 1;
    padding: 12px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: white;
    color: #666;
    transition: all 0.3s ease;
}

.tab-btn.active {
    background-color: #667eea;
    color: white;
    border-color: #667eea;
}

.tab-btn:hover:not(.active) {
    background-color: #f5f5f5;
}

.form-section {
    display: none;
}

.form-section.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    font-size: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
}

.btn-submit {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    font-size: 16px;
    font-weight: 600;
    background-color: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    background-color: #5568d3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit:active {
    transform: translateY(0);
}
</style>
</head>
<body>

<div class="container">
    <h1><img src = "logo.png"> BLOCKS OF BLOGS</h1>
    <div class="button-group">
        <button class="btn-primary" onclick="openModal('managerModal')">Manager Login</button>
        <button class="btn-primary" onclick="openModal('userModal')">User Access</button>
    </div>
</div>

<!-- Manager Modal -->
<div id="managerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('managerModal')">&times;</span>
        <h2>Manager Login</h2>
        <form method="POST">
            <div class="form-group">
                <label for="manager_username">Username</label>
                <input type="text" id="manager_username" name="manager_username" required>
            </div>
            <div class="form-group">
                <label for="manager_password">Password</label>
                <input type="password" id="manager_password" name="manager_password" required>
            </div>
            <button type="submit" name="manager_login" class="btn-submit">Login</button>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('userModal')">&times;</span>
        <h2>User Access</h2>
        
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="showUserForm('login')">Login</button>
            <button class="tab-btn" onclick="showUserForm('signup')">Sign Up</button>
        </div>

        <!-- Login Form -->
        <div id="userLoginForm" class="form-section active">
            <form method="POST">
                <div class="form-group">
                    <label for="login_username">Username</label>
                    <input type="text" id="login_username" name="login_username" required>
                </div>
                <div class="form-group">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="login_password" required>
                </div>
                <button type="submit" name="user_login" class="btn-submit">Login</button>
            </form>
        </div>

        <!-- Sign Up Form -->
        <div id="userSignupForm" class="form-section">
            <form method="POST">
                <div class="form-group">
                    <label for="signup_username">Username</label>
                    <input type="text" id="signup_username" name="signup_username" required>
                </div>
                <div class="form-group">
                    <label for="signup_password">Password</label>
                    <input type="password" id="signup_password" name="signup_password" required>
                </div>
                <div class="form-group">
                    <label for="signup_confirm_password">Confirm Password</label>
                    <input type="password" id="signup_confirm_password" name="signup_confirm_password" required>
                </div>
                <button type="submit" name="user_signup" class="btn-submit">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function showUserForm(type) {
    const loginForm = document.getElementById('userLoginForm');
    const signupForm = document.getElementById('userSignupForm');
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    loginForm.classList.remove('active');
    signupForm.classList.remove('active');
    
    tabButtons.forEach(btn => btn.classList.remove('active'));
    
    if(type === 'signup') {
        signupForm.classList.add('active');
        tabButtons[1].classList.add('active');
    } else {
        loginForm.classList.add('active');
        tabButtons[0].classList.add('active');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

</body>
</html>