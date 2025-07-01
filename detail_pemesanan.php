<?php
// Koneksi database dan mulai session
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php');
    exit();
}

// Mengambil detail produk berdasarkan ID di URL
$produk_id = $_GET['id'] ?? 0;
$query_produk = "SELECT * FROM produk WHERE id='$produk_id'";
$result_produk = mysqli_query($conn, $query_produk);
$produk = mysqli_fetch_assoc($result_produk);

// Inisialisasi variabel pesan error & sukses
$error_message = '';
$success_message = '';

// Menangani proses form submit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah = (int)$_POST['jumlah'];
    $catatan = $_POST['catatan'] ?? '';
    $alamat_pengiriman = trim($_POST['alamat_pengiriman'] ?? '');
    $nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
    $metode_pengiriman = $_POST['metode_pengiriman'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (!preg_match('/^\d{10,13}$/', $nomor_telepon)) {
        $error_message = "Nomor telepon harus terdiri dari 10 hingga 23 digit angka!";
    }

    if (empty($alamat_pengiriman) || empty($nomor_telepon) || empty($metode_pengiriman)) {
        $error_message = "Alamat pengiriman, nomor telepon, dan metode pengiriman wajib diisi!";
    }

    // Proses upload file desain
    $desain_nama = "";
    if (!empty($_FILES['upload_desain']['name'])) {
        $desain_nama = basename($_FILES['upload_desain']['name']);
        $desain_tmp = $_FILES['upload_desain']['tmp_name'];
        $upload_dir = "desain_pelanggan/";

        $desain_path = $upload_dir . $desain_nama;
        if (!move_uploaded_file($desain_tmp, $desain_path)) {
            $error_message = "Gagal mengunggah desain!";
        }
    }

    // Insert ke tabel pemesanan jika valid
    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO pemesanan (user_id, produk_id, jumlah, catatan, alamat_pengiriman, nomor_telepon, desain, metode_pengiriman) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("iiisssss", $user_id, $produk_id, $jumlah, $catatan, $alamat_pengiriman, $nomor_telepon, $desain_nama, $metode_pengiriman);
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
            header("Location: pembayaran.php?id=$last_id");
            exit();
        } else {
            $error_message = "Gagal menyimpan data!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Pemesanan</title>

    <!-- Import font Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Afacad&family=ADLaM+Display&family=Ebrima&display=swap" rel="stylesheet">

    <!-- Styling untuk layout form dan tampilan -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: stretch;
        }

        .form-grid>div {
            display: flex;
            flex-direction: column;
        }

        .box1 {
            background: white;
            border: 1px solid #8A282A;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 16px;
            height: 100px;
            overflow-y: auto;
            resize: vertical;
        }

        header {
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

        .DetailPemesanan {
            font-size: 28px;
            color: black;
            top: 18px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            white-space: nowrap;
            font-weight: bold;
        }

        .SketsaLogoBr {
            position: absolute;
            top: 7px;
            left: 40px;
            width: 140px;
            height: 75px;
            object-fit: contain;
        }

        .label {
            font-family: 'Afacad', sans-serif;
            font-size: 18px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .box,
        select,
        input[type="file"] {
            background: white;
            border: 1px solid #8A282A;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 16px;
            height: 45px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            vertical-align: middle;
        }

        .btn-container {
            display: inline-block;
            background: #8A282A;
            color: white;
            padding: 12px 30px;
            border-radius: 17px;
            text-decoration: none;
            border: none;
            font-family: 'Poppins', sans-serif;
            position: relative;
            top: -87px;
            left: 84%;
        }

        .btn-container:hover {
            background-color: #9A1C1C;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-family: 'Afacad', sans-serif;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alamat {
            position: absolute;
            top: 65px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #333;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <!-- Bagian Header: Logo & Judul -->
    <header>
        <img src="img/sketsa logo br.png" alt="Logo" class="SketsaLogoBr">
        <div class="DetailPemesanan">Detail Pemesanan</div>
        <div class="alamat">Jl. Brigjend Katamso No. 250, RT. 03/RW. 34, Mojosongo, Kec. Jebres, Kota Surakarta, Jawa Tengah 57127</div>
    </header>

    <!-- Class Utama Form Pemesanan -->
    <div class="container">

        <!-- Menampilkan pesan error / sukses -->
        <?php if (!empty($success_message)) : ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php elseif (!empty($error_message)) : ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Form Input Pemesanan -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div>
                    <div class="label">
                        <img src="img/image5.png" alt="Produk" width="24" style="margin-right:8px;">
                        Produk
                    </div>
                    <input type="text" class="box" value="<?= htmlspecialchars($produk['nama_produk']) ?>" readonly>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image7.png" alt="Jumlah" width="24" style="margin-right:8px;">
                        Jumlah
                    </div>
                    <input type="number" name="jumlah" class="box" required>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image10.png" alt="Catatan" width="24" style="margin-right:8px;">
                        Catatan (opsional)
                    </div>
                    <textarea name="catatan" class="box1" rows="3"></textarea>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image6.png" alt="Harga" width="24" style="margin-right:8px;">
                        Harga
                    </div>
                    <input type="text" class="box" value="<?= number_format($produk['harga'], 0, ',', '.') ?>" readonly>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image8.png" alt="Nomor Telepon" width="24" style="margin-right:8px;">
                        Nomor Telepon
                    </div>
                    <input type="text" name="nomor_telepon" class="box" required pattern="\d{10,23}" maxlength="23" inputmode="numeric"
                           title="Nomor telepon harus terdiri dari 10-23 angka!">
                </div>

                <div>
                    <div class="label">
                        <img src="img/image11.png" alt="Alamat Pengiriman" width="24" style="margin-right:8px;">
                        Alamat Pengiriman
                    </div>
                    <textarea name="alamat_pengiriman" class="box1" rows="3" required></textarea>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image18.png" alt="Metode Pengiriman" width="24" style="margin-right:8px;">
                        Metode Pengiriman
                    </div>
                    <select name="metode_pengiriman" class="box" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="GoSend">GoSend</option>
                        <option value="Ambil ke Rumah">Ambil ke Rumah</option>
                    </select>
                </div>

                <div>
                    <div class="label">
                        <img src="img/image9.png" alt="Upload Desain" width="24" style="margin-right:8px;">
                        Upload Desain (max 2MB)
                    </div>
                    <input type="file" name="upload_desain" accept=".jpg,.jpeg,.png,.pdf" class="box">
                </div>
            </div>

            <div style="margin-top: 40px;">
                <button type="submit" class="btn-container">Selanjutnya</button>
            </div>
        </form>
    </div>
</body>

</html>
