<?php
// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task_manager";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificare conexiune
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Procesare date formular
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Folosește 'email' conform tabelului
    $password = $_POST['password'];

    // Verifică dacă emailul există în baza de date
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // 's' = tip string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Salvare sesiune și redirecționare
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Parolă incorectă!";
        }
    } else {
        echo "Nu există niciun cont cu acest email!";
    }

    $stmt->close();
}

$conn->close();

?>

