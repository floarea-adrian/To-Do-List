<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task_manager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);
    $sql = "SELECT * FROM tasks WHERE id = $task_id AND user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        die("Task not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = intval($_POST['task_id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $sql = "UPDATE tasks SET title = '$title', description = '$description', due_date = '$due_date' WHERE id = $task_id AND user_id = {$_SESSION['user_id']}";

    if ($conn->query($sql)) {
        header("Location: dashboard.php");
    } else {
        echo "Error updating task: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task</h1>
    <form action="edit_task.php" method="POST">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $task['title']; ?>" required><br>
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $task['description']; ?>" required><br>
        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>" required><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
