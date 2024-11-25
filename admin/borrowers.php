<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.config.php';

// Proses pengembalian barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_item'])) {
    $peminjamId = $_POST['peminjam_id'];
    $itemId = $_POST['item_id'];

    // Ambil waktu peminjaman untuk diinput ke tbl_pengembalian
    $stmt = $pdo->prepare("SELECT * FROM tbl_peminjam WHERE id = ?");
    $stmt->execute([$peminjamId]);
    $peminjam = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($peminjam) {
        // Insert ke tbl_pengembalian
        $stmt = $pdo->prepare("INSERT INTO tbl_pengembalian (nama, kelas, alat, waktu_peminjaman, waktu_pengembalian) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $peminjam['nama'], 
            $peminjam['kelas'], 
            $peminjam['alat'], 
            $peminjam['waktu_peminjaman'], 
            date('Y-m-d H:i:s')
        ]);

        // Hapus dari tbl_peminjam
        $stmt = $pdo->prepare("DELETE FROM tbl_peminjam WHERE id = ?");
        $stmt->execute([$peminjamId]);

        // Update status alat menjadi 'Tersedia'
        $stmt = $pdo->prepare("UPDATE tbl_alat SET status = 'Tersedia' WHERE id = ?");
        $stmt->execute([$itemId]);

        // Redirect ke halaman peminjam setelah proses pengembalian berhasil
        header("Location: borrowers.php?success=1");
        exit();
    } else {
        echo "Gagal mengembalikan barang.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjam</title>
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
        <h1>Peminjam</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color:green;">Barang berhasil dikembalikan.</p>
        <?php endif; ?>

        <?php
        $stmt = $pdo->query("SELECT p.id AS peminjam_id, p.nama, p.kelas, p.alat, p.waktu_peminjaman, a.id AS alat_id FROM tbl_peminjam p JOIN tbl_alat a ON p.alat = a.id");
        echo "<table border='1' cellpadding='10'>
            <tr>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Alat yang Dipinjam</th>
                <th>Waktu Peminjaman</th>
                <th>Aksi</th>
            </tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                <td>{$row['nama']}</td>
                <td>{$row['kelas']}</td>
                <td>{$row['alat']}</td>
                <td>{$row['waktu_peminjaman']}</td>
                <td>
                    <form method='POST' action=''>
                        <input type='hidden' name='peminjam_id' value='{$row['peminjam_id']}'>
                        <input type='hidden' name='item_id' value='{$row['alat_id']}'>
                        <button type='submit' name='return_item'>Barang Dikembalikan</button>
                    </form>
                </td>
            </tr>";
        }
        echo "</table>";
        ?>
        <br>
        <a href="index.php">Kembali ke Dashboard</a> | <a href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>
