<?php
// Database connection
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        // Get task info for confirmation
        $stmt = $conn->prepare("SELECT task_name FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            header('Location: index.php');
            exit;
        }

        // Handle deletion
        if (isset($_POST['confirm'])) {
            $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->execute(['id' => $_GET['id']]);
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Task - Taskflow</title>
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
            background-color: #f5f6fa;
            min-height: 100vh;
            display: flex;
        }

        .delete-container {
            max-width: 500px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .warning-icon {
            font-size: 48px;
            color: #e53e3e;
            margin-bottom: 20px;
        }

        h1 {
            color: #2d3748;
            margin-bottom: 15px;
        }

        p {
            color: #718096;
            margin-bottom: 25px;
        }

        .task-name {
            font-weight: 500;
            color: #2d3748;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete {
            background-color: #e53e3e;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c53030;
        }

        .btn-cancel {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .btn-cancel:hover {
            background-color: #cbd5e0;
        }

        /* Animation */
        .delete-container {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <i class="fas fa-exclamation-triangle warning-icon"></i>
        <h1>Delete Task</h1>
        <p>Are you sure you want to delete the task:</p>
        <p class="task-name">"<?php echo htmlspecialchars($task['task_name']); ?>"?</p>
        <p>This action cannot be undone.</p>
        
        <form method="POST">
            <div class="buttons">
                <a href="index.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" name="confirm" class="btn btn-delete">Delete Task</button>
            </div>
        </form>
    </div>
</body>
</html>