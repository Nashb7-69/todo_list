<?php
// Database connection
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all tasks
    $stmt = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskflow Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
        }

        .user-info img {
            border-radius: 50%;
        }

        nav ul {
            list-style: none;
        }

        nav a {
            display: flex;
            align-items: center;
            padding: 10px;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        nav a.active {
            background-color: #fff;
            color: #6b46c1;
        }

        nav a i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f6fa;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .welcome {
            font-size: 24px;
            font-weight: 500;
        }

        .search {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search input {
            padding: 8px 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            width: 250px;
        }

        .date {
            text-align: right;
            color: #666;
        }

        /* Task Styles */
        .content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .todo {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .task {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .task:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .task.priority-High { border-left-color: #e53e3e; }
        .task.priority-Medium { border-left-color: #d69e2e; }
        .task.priority-Low { border-left-color: #38a169; }

        .task-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .task-title {
            font-weight: 500;
            color: #2d3748;
        }

        .task-time {
            color: #718096;
        }

        .task-body {
            color: #4a5568;
            margin-bottom: 15px;
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-priority {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .priority-High { background: #fed7d7; color: #e53e3e; }
        .priority-Medium { background: #fefcbf; color: #d69e2e; }
        .priority-Low { background: #c6f6d5; color: #38a169; }

        .task-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn-primary {
            background: #6b46c1;
        }

        .btn-primary:hover {
            background: #553c9a;
        }

        .btn-edit {
            background: #3182ce;
        }

        .btn-edit:hover {
            background: #2c5282;
        }

        .btn-delete {
            background: #e53e3e;
        }

        .btn-delete:hover {
            background: #c53030;
        }

        .wave {
            animation: wave 1s infinite;
            display: inline-block;
        }

        @keyframes wave {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(14deg); }
            100% { transform: rotate(0deg); }
        }

        /* Status Section Styles */
        .status {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .status-label {
            color: #4a5568;
            font-weight: 500;
        }

        .status-value {
            font-weight: 600;
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="taskflow-high-resolution-logo.png" alt="Taskflow logo" width="100" height="100"/>
                <span class="text-2xl font-bold"></span>
            </div>
            <div class="user-info">
                <img src="https://storage.googleapis.com/a1aa/image/aKC4BJRZuldwrlxTphxZdoBqYvoIMhX6SSdgcoLXzww.jpg" alt="User avatar" width="60" height="60"/>
                <div>
                    <div class="font-bold">hamzah</div>
                    <div class="text-sm">huhu@gmail.com</div>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-tasks"></i>Vital Task</a></li>
                    <li><a href="#"><i class="fas fa-check-circle"></i>My Task</a></li>
                    <li><a href="#"><i class="fas fa-list-alt"></i>Task Categories</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i>Settings</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome">
                    Welcome back, Hamzah
                    <span class="wave">👋</span>
                </div>
                <div class="search">
                    <input placeholder="Search your task here..." type="text" id="searchInput" onkeyup="searchTasks()"/>
                    <i class="fas fa-search"></i>
                    <i class="fas fa-calendar-alt"></i>
                    <i class="fas fa-bell"></i>
                    <div class="date">
                        <div id="currentDay"></div>
                        <div id="currentDate"></div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="todo">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2>Tasks</h2>
                        <a href="add_task.php" class="btn btn-primary">Add Task</a>
                    </div>
                    <div id="tasksContainer">
                        <?php foreach ($tasks as $task): ?>
                            <div class="task priority-<?php echo htmlspecialchars($task['priority']); ?>" data-task-name="<?php echo htmlspecialchars($task['task_name']); ?>">
                                <div class="task-header">
                                    <div class="task-title"><?php echo htmlspecialchars($task['task_name']); ?></div>
                                    <div class="task-time"><?php echo htmlspecialchars($task['task_time']); ?></div>
                                </div>
                                <div class="task-body">
                                    <?php echo htmlspecialchars($task['task_description']); ?>
                                </div>
                                <div class="task-footer">
                                    <span class="task-priority priority-<?php echo htmlspecialchars($task['priority']); ?>">
                                        <?php echo htmlspecialchars($task['priority']); ?>
                                    </span>
                                    <div class="task-actions">
                                        <button onclick="editTask(<?php echo $task['id']; ?>)" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="deleteTask(<?php echo $task['id']; ?>)" class="btn btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="status">
                    <div class="status-header">
                        <h3>Task Overview</h3>
                    </div>
                    <?php
                        $total = count($tasks);
                        $completed = array_reduce($tasks, function($carry, $task) {
                            return $carry + ($task['is_completed'] ? 1 : 0);
                        }, 0);
                        $pending = $total - $completed;
                        
                        $highPriority = array_reduce($tasks, function($carry, $task) {
                            return $carry + ($task['priority'] == 'High' ? 1 : 0);
                        }, 0);
                    ?>
                    <div class="status-item">
                        <span class="status-label">Total Tasks</span>
                        <span class="status-value"><?php echo $total; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Completed</span>
                        <span class="status-value"><?php echo $completed; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Pending</span>
                        <span class="status-value"><?php echo $pending; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">High Priority</span>
                        <span class="status-value"><?php echo $highPriority; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update date
        function updateDate() {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const now = new Date();
            document.getElementById('currentDay').textContent = days[now.getDay()];
            document.getElementById('currentDate').textContent = now.toLocaleDateString();
        }
        updateDate();

        // Search functionality
        function searchTasks() {
            const searchInput = document.getElementById('searchInput');
            const filter = searchInput.value.toLowerCase();
            const tasks = document.getElementsByClassName('task');

            for (let task of tasks) {
                const taskName = task.getAttribute('data-task-name').toLowerCase();
                if (taskName.includes(filter)) {
                    task.style.display = "";
                } else {
                    task.style.display = "none";
                }
            }
        }

        // Task operations
        function editTask(taskId) {
            window.location.href = `edit_task.php?id=${taskId}`;
        }

        function deleteTask(taskId) {
            window.location.href = `delete_task.php?id=${taskId}`;
        }
    </script>
</body>
</html>