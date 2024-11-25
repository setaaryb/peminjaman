<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_return'])) {
    $alatId = $_POST['alat_id'];
    
    $stmt = $pdo->prepare("DELETE FROM tbl_alat WHERE id = ?");
    $stmt->execute([$alatId]);

    // Redirect setelah berhasil menghapus
    header("Location: index.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        <!-- Konten -->
        <div class="content">
            <h1>Dashboard</h1>

            <?php
            $stmt = $pdo->query("SELECT * FROM tbl_alat");
            echo "<table border='1' cellpadding='10'>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Stok</th>
                    <th>Kondisi</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                    <td>{$row['nama_barang']}</td>
                    <td>{$row['jenis_barang']}</td>
                    <td>{$row['stok']}</td>
                    <td>{$row['kondisi']}</td>
                    <td><img src='uploads/{$row['foto']}' alt='{$row['nama_barang']}' width='100'></td>
                    <td>
                        <form method='POST' action=''>
                            <input type='hidden' name='alat_id' value='{$row['id']}'>
                            <button type='submit' name='delete_return'>Hapus</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
            ?>
        </div>
    </div>
</body>
</html>
