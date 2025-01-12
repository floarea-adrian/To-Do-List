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
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validare câmpuri (opțional)
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo "All fields are required!";
        exit();
    }

    // Verificare dacă emailul există deja
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "This email is already registered!";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Hash parola
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserare utilizator în baza de date
    $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Redirecționare după succes
        echo "Account created successfully! Redirecting to Log in page...";
        header("Refresh: 3; url=index.html#login-section");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
