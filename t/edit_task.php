<?php
// Database connection
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get task data if ID is provided
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$task) {
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = "UPDATE tasks SET 
                task_name = :task_name, 
                task_description = :task_description, 
                task_time = :task_time, 
                priority = :priority 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'id' => $_GET['id'],
            'task_name' => $_POST['task_name'],
            'task_description' => $_POST['task_description'],
            'task_time' => $_POST['task_time'],
            'priority' => $_POST['priority']
        ]);

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
    <title>Edit Task - Taskflow</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <!-- Same CSS as add_task.php -->
    <style>
        /* Copy all the CSS from add_task.php */
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="https://storage.googleapis.com/a1aa/image/sfTUn-sxnOmHf9sfXQQCRm88acAQFEkOml-YniXIQ-o.jpg" alt="Taskflow logo" width="40" height="40"/>
            <span class="text-2xl font-bold">Taskflow</span>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Edit Task</h1>
            <p>Update your task details</p>
        </div>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="task_name">Task Title</label>
                    <input 
                        type="text" 
                        id="task_name" 
                        name="task_name" 
                        required 
                        value="<?php echo htmlspecialchars($task['task_name']); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="task_description">Task Description</label>
                    <textarea 
                        id="task_description" 
                        name="task_description" 
                        rows="4" 
                        required
                    ><?php echo htmlspecialchars($task['task_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="task_time">Task Time</label>
                    <input 
                        type="time" 
                        id="task_time" 
                        name="task_time" 
                        required
                        value="<?php echo htmlspecialchars($task['task_time']); ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Priority Level</label>
                    <div class="priority-options">
                        <div class="priority-option">
                            <input type="radio" id="priority-high" name="priority" value="High" 
                                <?php echo $task['priority'] == 'High' ? 'checked' : ''; ?>>
                            <label for="priority-high">High</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="priority-medium" name="priority" value="Medium"
                                <?php echo $task['priority'] == 'Medium' ? 'checked' : ''; ?>>
                            <label for="priority-medium">Medium</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="priority-low" name="priority" value="Low"
                                <?php echo $task['priority'] == 'Low' ? 'checked' : ''; ?>>
                            <label for="priority-low">Low</label>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>