<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $query_update = "UPDATE pemesanan SET status = 'Telah Dikonfirmasi' WHERE id = '$id' AND status='Belum Dikonfirmasi'";
    if (mysqli_query($conn, $query_update)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "error";
    }
    exit();
}

$query = "SELECT * FROM pemesanan ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konfirmasi Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0; padding: 0; background: white;
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

        .title-wrapper {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 140px;
            height: 75px;
            object-fit: contain;
            margin-right: 20px;
        }

        .page-title {
            font-size: 28px;
            color: black;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            white-space: nowrap;
            font-weight: bold;
        }

        .hamburger-menu {
            position: relative;
            display: inline-block;
        }

        .hamburger-icon {
            width: 30px;
            height: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
        }

        .hamburger-icon div {
            height: 4px;
            background-color: #8A282A;
            border-radius: 2px;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 30px;
            right: 1px;
            background-color: white;
            min-width: 170px;
            width: 170px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 998;
            overflow: hidden;
        }

        .dropdown a {
            display: block;
            padding: 10px 16px;
            color: black;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .dropdown a:hover {
            background-color: rgba(138, 40, 42, 0.55);
        }

        .table-container {
            width: 98%;
            overflow-x: auto;
            margin: 0 auto 50px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            word-break: break-word;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            background: #8A282A;
            color: white;
        }

        td {
            background: white;
            color: black;
        }

        th:nth-child(-n+4), td:nth-child(-n+4) {
            width: 60px;
        }

        th:nth-child(n+5), td:nth-child(n+5) {
            width: 100px;
        }

        img {
            width: 90px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        button {
            padding: 6px 12px;
            background-color: #8A282A;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        button:hover {
            background-color: #6c1e1f;
        }
    </style>
</head>
<body>

<script>
    $(document).ready(function() {
        $('#hamburgerToggle').click(function(e) {
            e.stopPropagation();
            $('.dropdown').toggle();
        });

        $(document).click(function() {
            $('.dropdown').hide();
        });

        $('.dropdown').click(function(e) {
            e.stopPropagation();
        });

        $('.btn-konfirmasi').click(function() {
            var btn = $(this);
            var id = btn.data('id');

            $.post(window.location.href, { id: id }, function(response) {
                if (response === "success") {
                    btn.replaceWith('✅');
                    var row = btn.closest('tr');
                    row.find('td').eq(12).text('Telah Dikonfirmasi');
                } else {
                    alert('Gagal mengkonfirmasi pesanan. Silakan coba lagi.');
                }
            }).fail(function() {
                alert('Gagal mengkonfirmasi pesanan. Silakan coba lagi.');
            });
        });
    });
</script>

<div class="header-container">
    <div class="title-wrapper">
        <img class="logo" src="img/sketsa logo br.png" alt="Logo" />
    </div>
    <div class="page-title">Daftar Pesanan</div>
    <div class="hamburger-menu">
        <div class="hamburger-icon" id="hamburgerToggle">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="dropdown">
            <a href="admin_tambah_produk.php">Tambah Produk</a>
            <a href="dashboard.php">Sign out</a>
        </div>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID Pemesanan</th>
                <th>User ID</th>
                <th>Produk ID</th>
                <th>Jumlah</th>
                <th>Alamat Pengiriman</th>
                <th>Catatan</th>
                <th>Desain</th>
                <th>Bukti Pembayaran</th>
                <th>Nomor Telepon</th>
                <th>Metode Pembayaran</th>
                <th>Rekening Tujuan</th>
                <th>Pengiriman</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['produk_id']) ?></td>
                <td><?= htmlspecialchars($row['jumlah']) ?></td>
                <td><?= htmlspecialchars($row['alamat_pengiriman']) ?></td>
                <td><?= htmlspecialchars($row['catatan']) ?></td>
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
                        Belum Ada
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['nomor_telepon']) ?></td>
                <td><?= htmlspecialchars($row['metode']) ?></td>
                <td><?= htmlspecialchars($row['rekening_tujuan']) ?></td>
                <td><?= htmlspecialchars($row['metode_pengiriman']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] === "Belum Dikonfirmasi"): ?>
                        <button class="btn-konfirmasi" data-id="<?= htmlspecialchars($row['id']) ?>">Konfirmasi</button>
                    <?php else: ?>
                        ✅
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
