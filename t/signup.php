<?php
session_start();

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'todo_list',
    'username' => 'root',
    'password' => ''
];

// Database connection class
class Database {
    private $conn;

    public function __construct($config) {
        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['username'],
                $config['password']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// User registration class
class UserRegistration {
    private $db;
    private $error;
    private $success;

    public function __construct($database) {
        $this->db = $database;
        $this->error = '';
        $this->success = '';
    }

    public function register($userData) {
        if ($this->validateInput($userData)) {
            try {
                if ($this->isUsernameTaken($userData['username'])) {
                    $this->error = "Username already exists";
                    return false;
                }

                if ($this->isEmailTaken($userData['email'])) {
                    $this->error = "Email already exists";
                    return false;
                }

                if ($this->createUser($userData)) {
                    $this->success = "Account created successfully!";
                    return true;
                }
            } catch(PDOException $e) {
                $this->error = "Registration failed: " . $e->getMessage();
                return false;
            }
        }
        return false;
    }

    private function validateInput($data) {
        if (empty($data['username']) || empty($data['email']) || 
            empty($data['password']) || empty($data['retype_password'])) {
            $this->error = "All fields are required";
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error = "Invalid email format";
            return false;
        }

        if ($data['password'] !== $data['retype_password']) {
            $this->error = "Passwords do not match";
            return false;
        }

        if (strlen($data['password']) < 6) {
            $this->error = "Password must be at least 6 characters long";
            return false;
        }

        return true;
    }

    private function isUsernameTaken($username) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->rowCount() > 0;
    }

    private function isEmailTaken($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    private function createUser($data) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$data['username'], $data['email'], $hashed_password]);
    }

    public function getError() {
        return $this->error;
    }

    public function getSuccess() {
        return $this->success;
    }
}

// Initialize database connection
$database = new Database($config);
$registration = new UserRegistration($database->getConnection());

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($registration->register($_POST)) {
        header("refresh:2;url=login.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color:#7425F2            ;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 900px;
            max-width: 90%;
        }

        .left {
            flex: 1;
            padding: 40px;
            background-color:#7B2FF7            ;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .right {
            flex: 1;
            padding: 40px;
        }

        h2 {
            color: #2d3748;
            margin-bottom: 10px;
        }

        .divider {
            height: 4px;
            width: 60px;
            background-color: #4299e1;
            margin-bottom: 20px;
        }

        h3 {
            color: #4a5568;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            color: #4a5568;
            margin-bottom: 5px;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background-color: #4299e1;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #3182ce;
        }

        .social-btns {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .social-btn:hover {
            background-color: #edf2f7;
        }

        .links {
            text-align: center;
        }

        .links a {
            color: #4299e1;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: #e53e3e;
            background-color: #fed7d7;
            border: 1px solid #e53e3e;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .success-msg {
            color: #38a169;
            background-color: #c6f6d5;
            border: 1px solid #38a169;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
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
            <img alt="Illustration" src="6649594(1).png" width="300" height="300"/>
            <p>Create your account to start managing your tasks efficiently</p>
        </div>
        <div class="right">
            <h2>Create Account</h2>
            <div class="divider"></div>
            <h3>Register your account</h3>
            
            <?php if ($registration->getError()): ?>
                <div class="error-msg"><?php echo $registration->getError(); ?></div>
            <?php endif; ?>
            
            <?php if ($registration->getSuccess()): ?>
                <div class="success-msg"><?php echo $registration->getSuccess(); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="username">Username</label>
                <input 
                    id="username" 
                    name="username" 
                    placeholder="Username" 
                    type="text" 
                    required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                />
                
                <label for="email">Email</label>
                <input 
                    id="email" 
                    name="email" 
                    placeholder="Email" 
                    type="email" 
                    required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                />
                
                <label for="password">Password</label>
                <input 
                    id="password" 
                    name="password" 
                    placeholder="Password" 
                    type="password" 
                    required
                />
                
                <label for="retype_password">Retype Password</label>
                <input 
                    id="retype_password" 
                    name="retype_password" 
                    placeholder="Retype password" 
                    type="password" 
                    required
                />
                
                <button type="submit">Register</button>
            </form>

            <div class="social-btns">
                <button class="social-btn google-btn">
                    <i class="fab fa-google"></i>
                    Sign up with Google
                </button>
                <button class="social-btn github-btn">
                    <i class="fab fa-github"></i>
                    Sign up with GitHub
                </button>
            </div>

            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>
</qodoArtifact>

