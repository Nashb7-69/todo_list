<?php
session_start();

// Clear session to ensure login is required every time
session_unset();
session_destroy();
session_start();

// Database connection
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Get user from database
            $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to index.php
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Taskflow

    </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #7b2ff7, #5a00e0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }
        .left {
            background: #7b2ff7;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
        }
        .left img {
            width: 300px;
            height: 300px;
            margin-bottom: 16px;
        }
        .left p {
            color: white;
            text-align: center;
        }
        .right {
            padding: 40px;
            flex: 1;
        }
        .right h2 {
            color: #7b2ff7;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .right .divider {
            width: 50px;
            border-bottom: 2px solid #7b2ff7;
            margin-bottom: 24px;
        }
        .right h3 {
            color: #4a4a4a;
            font-size: 20px;
            margin-bottom: 24px;
        }
        .right form {
            display: flex;
            flex-direction: column;
        }
        .right form label {
            color: #4a4a4a;
            margin-bottom: 8px;
        }
        .right form input {
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 16px;
            outline: none;
            font-size: 16px;
        }
        .right form input:focus {
            border-color: #7b2ff7;
            box-shadow: 0 0 0 2px rgba(123, 47, 247, 0.2);
        }
        .right form button {
            background: #7b2ff7;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 16px;
            font-weight: 500;
        }
        .right form button:hover {
            background: #5a00e0;
        }
        .social-btns {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
            color: white;
        }
        .google-btn {
            background: #4285F4;
        }
        .google-btn:hover {
            background: #357ae8;
        }
        .github-btn {
            background: #333;
        }
        .github-btn:hover {
            background: #24292e;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .links a {
            color: #7b2ff7;
            text-decoration: none;
            font-size: 14px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .error-msg {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 0;
                border-radius: 0;
            }
            .left {
                padding: 20px;
            }
            .left img {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img alt="Login illustration" src="login.png"/>
            <p>Welcome back! Log in to access your tasks and stay organized.</p>
        </div>
        <div class="right">
            <h2>Welcome back</h2>
            <div class="divider"></div>
            <h3>Login to your account</h3>

            <?php if ($error): ?>
                <div class="error-msg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="username">Username</label>
                <input 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username" 
                    type="text"
                    required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                />
                
                <label for="password">Password</label>
                <input 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    type="password"
                    required
                />
                
                <button type="submit">Login</button>
            </form>

            <div class="social-btns">
                <button class="social-btn google-btn">
                    <i class="fab fa-google"></i>
                    Sign in with Google
                </button>
                <button class="social-btn github-btn">
                    <i class="fab fa-github"></i>
                    Sign in with GitHub
                </button>
            </div>

            <div class="links">1
                <a href="signup.php">Create Account</a>
                <a href="#">Forgot Password?</a>
            </div>
        </div>
    </div>
</body>
</html>