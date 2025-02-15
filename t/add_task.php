<?php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $sql = "INSERT INTO tasks (task_name, task_description, task_time, priority) 
                VALUES (:task_name, :task_description, :task_time, :priority)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'task_name' => $_POST['task_name'],
            'task_description' => $_POST['task_description'],
            'task_time' => $_POST['task_time'],
            'priority' => $_POST['priority']
        ]);
        
        header('Location: index.php');
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Task - Taskflow</title>
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

        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #e9ecef;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .header p {
            color: #718096;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #6b46c1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.2);
        }

        .priority-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .priority-option {
            flex: 1;
            position: relative;
        }

        .priority-option input {
            display: none;
        }

        .priority-option label {
            display: block;
            padding: 12px;
            text-align: center;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .priority-option input:checked + label {
            border-color: #6b46c1;
            background-color: #6b46c1;
            color: white;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
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

        .btn-primary {
            background-color: #6b46c1;
            color: white;
        }

        .btn-primary:hover {
            background-color: #553c9a;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background-color: #cbd5e0;
        }

        .error-message {
            color: #e53e3e;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Animation for form elements */
        .form-group {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation delay for form groups */
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="https://storage.googleapis.com/a1aa/image/sfTUn-sxnOmHf9sfXQQCRm88acAQFEkOml-YniXIQ-o.jpg" alt="Taskflow logo" width="40" height="40"/>
            <span class="text-2xl font-bold">Taskflow</span>
        </div>
        <!-- You can add sidebar navigation here if needed -->
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Create New Task</h1>
            <p>Add a new task to your workflow</p>
        </div>

        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="add_task.php">
                <div class="form-group">
                    <label for="task_name">Task Title</label>
                    <input 
                        type="text" 
                        id="task_name" 
                        name="task_name" 
                        required 
                        placeholder="Enter task title"
                    >
                </div>

                <div class="form-group">
                    <label for="task_description">Task Description</label>
                    <textarea 
                        id="task_description" 
                        name="task_description" 
                        rows="4" 
                        required 
                        placeholder="Describe your task"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="task_time">Task Time</label>
                    <input 
                        type="time" 
                        id="task_time" 
                        name="task_time" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Priority Level</label>
                    <div class="priority-options">
                        <div class="priority-option">
                            <input type="radio" id="priority-high" name="priority" value="High">
                            <label for="priority-high">High</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="priority-medium" name="priority" value="Medium" checked>
                            <label for="priority-medium">Medium</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="priority-low" name="priority" value="Low">
                            <label for="priority-low">Low</label>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add animation class to form groups
        document.querySelectorAll('.form-group').forEach((group, index) => {
            group.style.animationDelay = `${index * 0.1}s`;
        });
    </script>
</body>
</html>