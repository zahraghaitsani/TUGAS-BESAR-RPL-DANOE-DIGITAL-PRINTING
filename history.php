<?php
// Mulai session dan include koneksi
session_start();
include 'config.php';

// Cek session user login
if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil nama user dari database
$queryUser = "SELECT nama FROM users WHERE id = '$user_id' LIMIT 1";
$resultUser = mysqli_query($conn, $queryUser);
$nama_user = ($resultUser && mysqli_num_rows($resultUser) > 0) ? mysqli_fetch_assoc($resultUser)['nama'] : "User";

// Ambil semua pesanan user
$query = "SELECT * FROM pemesanan WHERE user_id = '$user_id' ORDER BY id ASC";
$result = mysqli_query($conn, $query);

// Tentukan link kembali (back)
$fromPage = isset($_GET['from']) ? $_GET['from'] : '';
if ($fromPage === 'katalog.php') {
    $backLink = 'katalog.php';
} elseif ($fromPage === 'terima_kasih.php') {
    $backLink = 'terima_kasih.php';
} else {
    $backLink = 'Dashboard.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Riwayat Pesanan Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <!-- Styling untuk seluruh halaman -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100px;
            padding: 0 40px;
            background: white;
            max-width: 1440px;
            margin: 0 auto 30px;
            box-sizing: border-box;
            border-bottom: 8px solid #8A282A;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 14px;
            left: 40px;
            max-width: 140px;
            max-height: 75px;
            object-fit: contain;
        }

        .page-title {
            font-size: 28px;
            color: black;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            white-space: nowrap;
        }

        .signout a {
            display: flex;
            align-items: center;
            font-size: 18px;
            color: black;
            text-decoration: none;
            font-weight: bold;
        }

        .signout img {
            margin-right: 8px;
            width: 24px;
            height: 24px;
            border: none;
            outline: none;
            box-shadow: none;
            background: transparent;
        }

        h3 {
            margin-left: 40px;
        }

        .table-container {
            width: 98%;
            overflow-x: auto;
            margin: 20px auto 50px;
            box-sizing: border-box;
            max-width: 1440px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
            word-break: break-word;
            overflow: hidden;
        }

        th {
            background: #8A282A;
            color: white;
        }

        th:nth-child(1), td:nth-child(1) { width: 80px; }
        th:nth-child(2), td:nth-child(2) { width: 90px; }
        th:nth-child(3), td:nth-child(3) { width: 140px; }
        th:nth-child(4), td:nth-child(4) { width: 70px; }
        th:nth-child(5), td:nth-child(5) { width: 200px; }
        th:nth-child(6), td:nth-child(6) { width: 120px; }
        th:nth-child(7), td:nth-child(7) { width: 130px; }
        th:nth-child(8), td:nth-child(8) { width: 140px; }
        th:nth-child(9), td:nth-child(9) { width: 100px; }
        
        img {
            max-width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: none;
        }

        p.no-orders {
            text-align: center;
            margin-top: 40px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>

<!-- Header & Navigasi -->
<div class="header-container">
    <div class="title-wrapper">
        <img class="logo" src="img/sketsa logo br.png" alt="Logo" />
    </div>
    <h1 class="page-title">Riwayat Pesanan Saya</h1>
    <div class="signout">
        <a href="<?= htmlspecialchars($backLink) ?>">
            <img src="img/image4.png" alt="Back" />
            Back
        </a>
    </div>
</div>

<h3>Halo, <?= htmlspecialchars($nama_user) ?>!</h3>

<!-- Tabel atau pesan kosong -->
<?php if (mysqli_num_rows($result) == 0): ?>
    <p class="no-orders">Anda belum pernah melakukan pemesanan.</p>
<?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>ID Produk</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Alamat</th>
                    <th>Desain</th>
                    <th>Bukti Pembayaran</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['produk_id']) ?></td>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                    <td><?= htmlspecialchars($row['alamat_pengiriman']) ?></td>
                    <td>
                        <?php if (!empty($row['desain'])): ?>
                            <a href="desain_pelanggan/<?= htmlspecialchars($row['desain']) ?>" target="_blank">
                                <img src="desain_pelanggan/<?= htmlspecialchars($row['desain']) ?>" alt="Desain" />
                            </a>
                        <?php else: ?>
                            Tidak ada
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($row['bukti_pembayaran']) && $row['bukti_pembayaran'] !== "NULL"): ?>
                            <a href="bukti_bayar/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" target="_blank">
                                <img src="bukti_bayar/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" alt="Bukti" />
                            </a>
                        <?php else: ?>
                            Tidak ada
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['metode']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>
