<?php
// Konfigurasi & Autentikasi User
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: sigin_in.php');
    exit();
}

// Ambil Data Pemesanan & Produk
$pemesanan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT p.*, pr.nama_produk, pr.harga 
          FROM pemesanan p 
          JOIN produk pr ON p.produk_id = pr.id 
          WHERE p.id='$pemesanan_id'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$pemesanan = mysqli_fetch_assoc($result);
if (!$pemesanan) {
    die("Data pemesanan tidak ditemukan.");
}
$total_harga = $pemesanan['jumlah'] * $pemesanan['harga'];

// Daftar Rekening / E-Wallet / COD
$rekening = [
    "BCA" => "3940164434 a.n. Ernawati",
    "Gopay/ShopeePay" => "081328139666",
    "COD" => "-"
];

// Proses Form Pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metode = $_POST['metode'];
    $rekening_tujuan = $rekening[$metode] ?? 'N/A';

    if ($metode === 'COD') {
        $nama_produk_safe = mysqli_real_escape_string($conn, $pemesanan['nama_produk']);
        $query = "UPDATE pemesanan SET 
                    metode = '$metode', 
                    rekening_tujuan = '$rekening_tujuan', 
                    bukti_pembayaran = NULL,
                    nama_produk = '$nama_produk_safe'
                  WHERE id = '$pemesanan_id'";
        $result_update = mysqli_query($conn, $query);
        if (!$result_update) {
            die("Error saat update data pembayaran: " . mysqli_error($conn));
        }
        header('Location: terima_kasih.php?id=' . $pemesanan_id);
        exit();
    } else {
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $file_tmp = $_FILES['bukti']['tmp_name'];
            $file_ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $file_name = 'bukti_pembayaran' . time() . '.' . $file_ext;

            if (move_uploaded_file($file_tmp, "bukti_bayar/$file_name")) {
                $pemesanan_id_safe = mysqli_real_escape_string($conn, $pemesanan_id);
                $metode_safe = mysqli_real_escape_string($conn, $metode);
                $rekening_tujuan_safe = mysqli_real_escape_string($conn, $rekening_tujuan);
                $file_name_safe = mysqli_real_escape_string($conn, $file_name);
                $nama_produk_safe = mysqli_real_escape_string($conn, $pemesanan['nama_produk']);

                $query = "UPDATE pemesanan SET 
                            metode = '$metode_safe', 
                            rekening_tujuan = '$rekening_tujuan_safe', 
                            bukti_pembayaran = '$file_name_safe',
                            nama_produk = '$nama_produk_safe'
                          WHERE id = '$pemesanan_id_safe'";
                $result_update = mysqli_query($conn, $query);
                if (!$result_update) {
                    die("Error saat update data pembayaran: " . mysqli_error($conn));
                }

                header('Location: terima_kasih.php?id=' . $pemesanan_id);
                exit();
            } else {
                $error = "Gagal memindahkan file bukti pembayaran.";
            }
        } else {
            $error = "Gagal mengupload bukti pembayaran!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Afacad&family=ADLaM+Display&family=Ebrima&display=swap" rel="stylesheet">
    <!-- Style untuk tampilan halaman login -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            position: relative;
            padding-bottom: 30px;
        }

        .logo {
            width: 140px;
            height: 75px;
            object-fit: contain;
            position: absolute;
            top: 7px;
            left: 40px;
        }

        .title {
            margin-top: 17px;
            font-size: 28px;
            font-weight: 600;
            font-family: 'Poppins', cursive;
            color: black;
        }

        .line {
            width: 100%;
            border-bottom: 8px solid #8A282A;
            margin-top: 33px;
        }

        .form-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .form-wrapper {
            margin: 30px auto;
            width: 700px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-top {
            font-size: 20px;
            color: black;
            margin-top: 15px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-content {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 40px;
        }

        .form-left, .form-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-col {
            margin-bottom: 25px;
        }

        .label {
            color: black;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .label img {
            width: 24px;
            height: 24px;
        }

        .input-box,
        .select-box {
            width: 291px;
            height: 50px;
            background: white;
            border: 1px solid #8A282A;
            border-radius: 15px;
            font-size: 16px;
            padding-left: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .rekening-info {
            font-size: 18px;
            color: black;
            margin-top: 25px;
            padding-left: 5px;
            margin-bottom: 10px;
        }

        .upload-input {
            width: 260px;
            height: 30px;
            border: 1px solid #8A282A;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 15px;
        }

        .submit-btn {
            position: absolute;
            top: 650px;
            left: 50%;
            transform: translateX(-50%);
            width: 240px;
            height: 50px;
            background: #8A282A;
            border-radius: 17px;
            color: white;
            font-size: 1rem;
            font-family: Poppins, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            outline: none;
        }

        .submit-btn:hover,
        .submit-btn:active {
            background-color: #9A1C1C;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
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
<div class="container">
    <img src="img/sketsa logo br.png" class="logo" alt="Logo">
    <div class="title">Pembayaran</div>
    <div class="alamat">Jl. Brigjend Katamso No. 250, RT. 03/RW. 34, Mojosongo, Kec. Jebres, Kota Surakarta, Jawa Tengah 57127</div>
    <div class="line"></div>

    <div class="form-container">
        <!-- Form Konfirmasi Pembayaran -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-wrapper">
                <div class="form-top">
                    ID Pemesanan: <?php echo htmlspecialchars($pemesanan['id']); ?>
                </div>

                <div class="form-content">
                    <!-- Kolom Kiri -->
                    <div class="form-left">
                        <div class="form-col">
                            <label class="label"><img src="img/image12.png" alt="Produk">Produk</label>
                            <div class="input-box"><?php echo htmlspecialchars($pemesanan['nama_produk']); ?></div>
                        </div>

                        <div class="form-col">
                            <label class="label"><img src="img/image13.png" alt="Jumlah">Jumlah</label>
                            <div class="input-box"><?php echo htmlspecialchars($pemesanan['jumlah']); ?></div>
                        </div>

                        <div class="form-col">
                            <label class="label"><img src="img/image14.png" alt="Harga">Total Harga</label>
                            <div class="input-box">Rp<?php echo number_format($total_harga, 0, ',', '.'); ?></div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="form-right">
                        <div class="form-col">
                            <label class="label"><img src="img/image16.png" alt="Metode">Metode Pembayaran</label>
                            <select name="metode" id="metode" class="select-box" required>
                                <option value="">Pilih Metode</option>
                                <?php foreach ($rekening as $key => $val) {
                                    echo "<option value=\"" . htmlspecialchars($key) . "\">" . htmlspecialchars($key) . "</option>";
                                } ?>
                            </select>
                        </div>

                        <div class="form-col">
                            <label class="label">Nomor Rekening / E-Wallet:</label>
                            <div class="rekening-info">
                                <strong><span id="rekeningTujuan">-</span></strong>
                            </div>
                        </div>

                        <div class="form-col">
                            <label class="label"><img src="img/image17.png" alt="Upload">Upload Bukti Pembayaran</label>
                            <input type="file" name="bukti" id="bukti" accept="image/*" required class="upload-input">
                            <?php if (isset($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
                        </div>
                    </div>
                </div>

                <div class="form-row" style="margin-top: 30px;">
                    <button type="submit" class="submit-btn">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Script Dynamic Rekening & Validasi Bukti
const rekeningData = <?php echo json_encode($rekening); ?>;
const metodeSelect = document.getElementById('metode');
const rekeningTujuanSpan = document.getElementById('rekeningTujuan');
const buktiInput = document.getElementById('bukti');

metodeSelect.addEventListener('change', function () {
    const metode = this.value;
    rekeningTujuanSpan.textContent = rekeningData[metode] || "-";
    if (metode === "COD") {
        buktiInput.required = false;
        buktiInput.disabled = true;
        buktiInput.value = "";
    } else {
        buktiInput.required = true;
        buktiInput.disabled = false;
    }
});
</script>
</body>
</html>
