<?php
include 'db.config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $jenis_barang = $_POST['jenis_barang'];
    $stok = $_POST['stok'];
    $kondisi = $_POST['kondisi'];
    $foto = $_FILES['foto']['name'];
    $target = "uploads/" . basename($foto);

    // Ensure the uploads directory exists and is writable
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_alat (nama_barang, jenis_barang, stok, kondisi, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama_barang, $jenis_barang, $stok, $kondisi, $foto]);
                        // Redirect to dashboard page
                        header("Location: index.php");
                        exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Gagal mengupload foto.";
    }
}
?>
