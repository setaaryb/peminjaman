<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo-container">
                <img src="../img/logo1.png" alt="Logo" class="logo">
            </div>
            <a href="index.php">Dashboard</a>
            <a href="add_item.php">Tambah Barang</a>
            <a href="borrowers.php">Peminjam</a>
            <a href="returns.php">Pengembalian</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="content">
            <h1>Tambah Barang</h1>
            <form action="add_item_process.php" method="post" enctype="multipart/form-data">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" id="nama_barang" name="nama_barang" required>
                <label for="jenis_barang">Jenis Barang:</label>
                <input type="text" id="jenis_barang" name="jenis_barang" required>
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>
                <label for="stok">Kondisi:</label>
                <input type="text" id="kondisi" name="kondisi" required>
                <label for="foto">Foto:</label>
                <input type="file" id="foto" name="foto" required>
                <input type="submit" value="Tambah Barang">
            </form>
            <br>
            <a href="index.php">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
