<?php
// Menyertakan file konfigurasi database
include 'admin/db.config.php';

// Query untuk mengambil data dari tbl_alat
$sql = "SELECT * FROM tbl_alat";
$result = $conn->query($sql);

// Handle form submission untuk peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    $itemId = intval($_POST['itemId']);
    $name = trim($_POST['name']);
    $class = trim($_POST['class']);
    $borrowTime = date('Y-m-d H:i:s');

    // Validasi input
    if (empty($name) || empty($class)) {
        echo json_encode(['success' => false, 'message' => 'Nama dan kelas harus diisi.']);
        $conn->close();
        exit;
    }

    // Cek apakah item sudah dipinjam
    $stmt = $conn->prepare("SELECT status FROM tbl_alat WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status === 'Dipinjam') {
        echo json_encode(['success' => false, 'message' => 'Item sudah dipinjam.']);
        $conn->close();
        exit;
    }

    // Insert ke tbl_peminjam
    $stmt = $conn->prepare("INSERT INTO tbl_peminjam (nama, kelas, alat, waktu_peminjaman) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $class, $itemId, $borrowTime);

    if ($stmt->execute()) {
        // Update status item menjadi 'Dipinjam'
        $stmt = $conn->prepare("UPDATE tbl_alat SET status = 'Dipinjam' WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Peminjaman berhasil.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal melakukan peminjaman.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle form submission untuk pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return'])) {
    $itemId = intval($_POST['itemId']);
    $name = trim($_POST['name']);
    $class = trim($_POST['class']);
    $returnTime = date('Y-m-d H:i:s');

    // Validasi input
    if (empty($name) || empty($class)) {
        echo json_encode(['success' => false, 'message' => 'Nama dan kelas harus diisi.']);
        $conn->close();
        exit;
    }

    // Ambil data peminjaman terakhir
    $stmt = $conn->prepare("SELECT waktu_peminjaman FROM tbl_peminjam WHERE alat = ? AND nama = ? AND kelas = ? ORDER BY waktu_peminjaman DESC LIMIT 1");
    $stmt->bind_param("iss", $itemId, $name, $class);
    $stmt->execute();
    $stmt->bind_result($borrowTime);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Insert ke tbl_pengembalian
    $stmt = $conn->prepare("INSERT INTO tbl_pengembalian (nama, kelas, alat, waktu_peminjaman, waktu_pengembalian) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $class, $itemId, $borrowTime, $returnTime);

    if ($stmt->execute()) {
        // Update status item menjadi 'Tersedia'
        $stmt = $conn->prepare("UPDATE tbl_alat SET status = 'Tersedia' WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();

        // Hapus dari tbl_peminjam
        $stmt = $conn->prepare("DELETE FROM tbl_peminjam WHERE alat = ? AND nama = ? AND kelas = ?");
        $stmt->bind_param("iss", $itemId, $name, $class);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Pengembalian berhasil.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal melakukan pengembalian.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spesifikasi Alat</title>
    <style>
    :root {
        --primary-color: #007bff;
        --hover-color: #0056b3;
        --disabled-color: #ff8c00;
        --success-color: #28a745;
        --error-color: #dc3545;
        --bg-color: #f4f4f4;
        --white: #fff;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--bg-color);
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background-color: var(--white);
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
    }

    .grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .item {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #fafafa;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        padding: 15px;
        width: 220px;
        transition: transform 0.2s;
        overflow: hidden;
    }

    .item:hover {
        transform: scale(1.05);
    }

    .item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }

    .item-info {
        text-align: center;
        margin-top: 10px;
    }

    .item-info h2 {
        margin: 10px 0 5px 0;
        font-size: 1.2em;
    }

    .item-info p {
        margin: 5px 0;
        font-size: 0.95em;
    }

    .item-info button {
        padding: 8px 16px;
        background-color: #1d3557;
        color: var(--white);
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s, transform 0.2s;
        font-size: 0.9em;
    }

    .item-info button:hover {
        background-color: #457b9d;
        transform: translateY(-2px);
    }

    .item-info button.disabled {
        background-color: var(--disabled-color);
        cursor: not-allowed;
    }

    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.3s;
    }

    .popup-content {
        background-color: var(--white);
        border-radius: 8px;
        padding: 25px;
        width: 350px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .popup-content h2 {
        margin-top: 0;
        text-align: center;
    }

    .popup-content p {
        margin: 15px 0;
    }

    .popup-content input[type="text"],
    .popup-content input[type="hidden"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .popup-content button {
        padding: 10px 20px;
        background-color: var(--primary-color);
        color: var(--white);
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 10px 5px 0 0;
        transition: background-color 0.3s;
        font-size: 0.95em;
    }

    .popup-content button:hover {
        background-color: var(--hover-color);
    }

    .popup-content .close {
        background-color: #6c757d;
    }

    .popup-content .close:hover {
        background-color: #5a6268;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Spesifikasi Alat</h1>
        <div class="grid" id="itemGrid">
            <?php
            while($row = $result->fetch_assoc()) {
                $statusClass = ($row["status"] === 'Dipinjam') ? 'disabled' : '';
                $statusText = ($row["status"] === 'Dipinjam') ? 'Sedang Dipinjam' : 'Pinjam';
                $buttonColor = ($row["status"] === 'Dipinjam') ? '#ff8c00' : '#007bff';

                echo '<div class="item">';
                echo '<img src="img/' . htmlspecialchars($row["foto"]) . '" alt="' . htmlspecialchars($row["nama_barang"]) . '">';
                echo '<div class="item-info">';
                echo '<h2>' . htmlspecialchars($row["nama_barang"]) . '</h2>';
                echo '<p>Jenis Barang: ' . htmlspecialchars($row["jenis_barang"]) . '</p>';
                echo '<p>Stok: ' . htmlspecialchars($row["stok"]) . '</p>';
                echo '<p>Kondisi: ' . htmlspecialchars($row["kondisi"]) . '</p>';

                if ($row["status"] === 'Dipinjam') {
                    echo '<button class="disabled" data-id="' . htmlspecialchars($row["id"]) . '" onclick="openReturnPopup(' . htmlspecialchars($row["id"]) . ', \'' . addslashes(htmlspecialchars($row["nama_barang"])) . '\')">Pengembalian</button>';
                } else {
                    echo '<button class="' . $statusClass . '" data-id="' . htmlspecialchars($row["id"]) . '" onclick="openBorrowPopup(' . htmlspecialchars($row["id"]) . ')">' . $statusText . '</button>';
                }

                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Notifikasi -->
    <div class="notification" id="notification"></div>

    <!-- Popup Form untuk Peminjaman -->
    <div class="popup" id="borrowPopup">
        <div class="popup-content">
            <h2>Formulir Peminjaman</h2>
            <form id="borrowForm">
                <input type="hidden" id="borrowItemId" name="itemId">
                <p>
                    <label for="borrowName">Nama:</label>
                    <input type="text" id="borrowName" name="name" required>
                </p>
                <p>
                    <label for="borrowClass">Kelas:</label>
                    <input type="text" id="borrowClass" name="class" required>
                </p>
                <p>
                    <button type="submit">Kirim</button>
                    <button type="button" class="close" onclick="closePopup()">Tutup</button>
                </p>
            </form>
        </div>
    </div>

    <!-- Popup Form untuk Pengembalian -->
    <div class="popup" id="returnPopup">
        <div class="popup-content">
            <h2>Formulir Pengembalian</h2>
            <form id="returnForm">
                <input type="hidden" id="returnItemId" name="itemId">
                <p>
                    <label for="returnName">Nama:</label>
                    <input type="text" id="returnName" name="name" required>
                </p>
                <p>
                    <label for="returnClass">Kelas:</label>
                    <input type="text" id="returnClass" name="class" required>
                </p>
                <p>
                    <button type="submit">Kirim</button>
                    <button type="button" class="close" onclick="closePopup()">Tutup</button>
                </p>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, isError = false) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = 'notification' + (isError ? ' error' : '');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Fungsi untuk membuka popup peminjaman
        function openBorrowPopup(itemId) {
            document.getElementById('borrowPopup').style.display = 'flex';
            document.getElementById('borrowItemId').value = itemId;
            document.getElementById('borrowForm').reset();
        }

        // Fungsi untuk membuka popup pengembalian
        function openReturnPopup(itemId, name) {
            document.getElementById('returnPopup').style.display = 'flex';
            document.getElementById('returnItemId').value = itemId;
            document.getElementById('returnForm').reset();
        }

        function closePopup() {
            document.getElementById('borrowPopup').style.display = 'none';
            document.getElementById('returnPopup').style.display = 'none';
        }

        document.getElementById('borrowForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const itemId = document.getElementById('borrowItemId').value;
            const name = document.getElementById('borrowName').value.trim();
            const className = document.getElementById('borrowClass').value.trim();

            if (name === '' || className === '') {
                showNotification('Harap isi semua bidang.', true);
                return;
            }

            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Mengirim <span class="loader"></span>';

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'borrow': true,
                    'itemId': itemId,
                    'name': name,
                    'class': className
                })
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Kirim';
                if (data.success) {
                    showNotification(data.message);
                    const button = document.querySelector(`button[data-id="${itemId}"]`);
                    if (button) {
                        button.textContent = 'Sedang Dipinjam';
                        button.classList.add('disabled');
                        button.removeAttribute('onclick');
                        button.onclick = null;
                        button.style.backgroundColor = '#ff8c00';
                        button.disabled = true;
                    }
                } else {
                    showNotification(data.message, true);
                }
                closePopup();
            })
            .catch(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Kirim';
                showNotification('Terjadi kesalahan.', true);
            });
        });

        document.getElementById('returnForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const itemId = document.getElementById('returnItemId').value;
            const name = document.getElementById('returnName').value.trim();
            const className = document.getElementById('returnClass').value.trim();

            if (name === '' || className === '') {
                showNotification('Harap isi semua bidang.', true);
                return;
            }
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Mengirim <span class="loader"></span>';

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'return': true,
                    'itemId': itemId,
                    'name': name,
                    'class': className
                })
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Kirim';
                if (data.success) {
                    showNotification(data.message);
                    const button = document.querySelector(`button[data-id="${itemId}"]`);
                    if (button) {
                        button.textContent = 'Pinjam';
                        button.classList.remove('disabled');
                        button.style.backgroundColor = '#007bff';
                        button.disabled = false;
                        button.setAttribute('onclick', `openBorrowPopup(${itemId})`);
                    }
                } else {
                    showNotification(data.message, true);
                }
                closePopup();
            })
            .catch(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Kirim';
                showNotification('Terjadi kesalahan.', true);
            });
        });
    </script>
</body>
</html>
