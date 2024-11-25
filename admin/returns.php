<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.config.php';

// Proses penghapusan catatan pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_return'])) {
    $pengembalianId = $_POST['pengembalian_id'];
    
    // Hapus catatan pengembalian
    $stmt = $pdo->prepare("DELETE FROM tbl_pengembalian WHERE id = ?");
    $stmt->execute([$pengembalianId]);

    // Redirect setelah berhasil menghapus
    header("Location: returns.php?success=1");
    exit();
}

// Proses ekspor ke CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=pengembalian.csv');

    $stmt = $pdo->query("SELECT * FROM tbl_pengembalian");
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nama', 'Kelas', 'Alat yang Dipinjam', 'Waktu Peminjaman', 'Waktu Pengembalian']);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [$row['nama'], $row['kelas'], $row['alat'], $row['waktu_peminjaman'], $row['waktu_pengembalian']]);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian</title>
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
            <h1>Pengembalian</h1>

            <?php if (isset($_GET['success'])): ?>
                <p style="color:green;">Catatan pengembalian berhasil dihapus.</p>
            <?php endif; ?>

            <a href="returns.php?export=csv" class="btn-export">Ekspor ke Excel (CSV)</a>

            <?php
            
            $stmt = $pdo->query("SELECT * FROM tbl_pengembalian");
            echo "<table border='1' cellpadding='10'>
                <tr>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Alat yang Dipinjam</th>
                    <th>Waktu Peminjaman</th>
                    <th>Waktu Pengembalian</th>
                    <th>Aksi</th>
                </tr>";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                    <td>{$row['nama']}</td>
                    <td>{$row['kelas']}</td>
                    <td>{$row['alat']}</td>
                    <td>{$row['waktu_peminjaman']}</td>
                    <td>{$row['waktu_pengembalian']}</td>
                    <td>
                        <form method='POST' action=''>
                            <input type='hidden' name='pengembalian_id' value='{$row['id']}'>
                            <button type='submit' name='delete_return'>Hapus</button>
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
