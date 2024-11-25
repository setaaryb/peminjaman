<?php
session_start();
require 'db.config.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unique_number = $_POST['unique_number'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Admins WHERE unique_number = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $unique_number, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['unique_number'] = $unique_number;
        header('Location: index.php');
        exit();
    } else {
        $error_message = 'Nomor unik atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroTik Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>PEMINJAMAN ALAT</h1>
        <nav>
            <a href="#">Home</a>
            <a href="#">Support</a>
            <a href="#">Contact</a>
        </nav>
    </header>
    <main>
        <div class="login-box">
            <img src="../img/logo.png" alt="Logo Smk" class="logo">
            <form method="POST">
                <input type="text" id="unique_number" name="unique_number" placeholder="No Unik" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <?php if ($error_message): ?>
                    <p id="error-message"><?= htmlspecialchars($error_message) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </main>
    <div id="particles-js"></div>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
