<?php
require 'db.php';
session_start();

// Verificare autentificare
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html#login-section");
    exit();
}

$user_id = $_SESSION['user_id'];

// Adăugare sarcină
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $sql = "INSERT INTO tasks (user_id, title, description, due_date) VALUES ('$user_id', '$title', '$description', '$due_date')";
    if ($conn->query($sql)) {
        $_SESSION['message'] = "Task added successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
    }
}

// Ștergere sau marcarea sarcinii ca finalizată
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_task'])) {
        $task_id = intval($_POST['task_id']);
        $sql = "DELETE FROM tasks WHERE id = $task_id AND user_id = {$_SESSION['user_id']}";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Task deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting task: " . $conn->error;
        }
        header("Location: dashboard.php");
        exit();
    } elseif (isset($_POST['complete_task'])) {
        $task_id = intval($_POST['task_id']);
        $sql = "UPDATE tasks SET completed = 1 WHERE id = $task_id AND user_id = {$_SESSION['user_id']}";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Task marked as completed!";
        } else {
            $_SESSION['message'] = "Error marking task as completed: " . $conn->error;
        }
        header("Location: dashboard.php");
        exit();
    }
}

// Afișare sarcini
$sql = "SELECT * FROM tasks WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Manager</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="overflow:visible;">
    <?php
    // Afișare mesaj din sesiune
    if (isset($_SESSION['message'])) {
        echo "<p style='color: green; position:absolute; left:45%; background-color:#1C2833; padding:5px;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']); // Șterge mesajul după afișare
        echo '<meta http-equiv="refresh" content="2;url=dashboard.php">';
    }
    
    ?>

    <!-- Header -->
    <header>
        <img src=".//to-do-list.png" alt="logo">
        <h1>Welcome to Your Dashboard</h1>
        <p>Manage your tasks efficiently.</p>
        <a href="logout.php" class="btn">Log Out</a>
    </header>

    <!-- Adăugare sarcină -->
    <section id="add-task-section">
        <h2>Add a New Task</h2> <br>
        <form action="dashboard.php" method="POST">
            <label for="title">Task Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" required>

            <button type="submit" name="add_task">Add Task</button>
        </form>
    </section>

    <!-- Afișare sarcini -->
    <section id="tasks-section">
        <h2>Your Tasks</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($task = $result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($task['title']); ?></strong> - 
                        <?php echo htmlspecialchars($task['description']); ?> 
                        (Due: <?php echo htmlspecialchars($task['due_date']); ?>) - 
                        <?php echo $task['completed'] ? "✔️ Completed" : "❌ Not Completed"; ?>

                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" name="delete_task" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                        </form>

                        <form action="edit_task.php" method="GET" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit">Edit</button>
                        </form>

                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" name="complete_task">Mark as Completed</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No tasks found. Add your first task above!</p>
        <?php endif; ?>
    </section>
</body>
</html>
