<?php
// Mulai session & include koneksi DB
include 'config.php';
session_start();

// Cek jika user belum login, redirect ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}

// Ambil semua data produk
$query_produk = "SELECT * FROM produk";
$result_produk = mysqli_query($conn, $query_produk);
if (!$result_produk) {
    die("Query Error: " . mysqli_error($conn));
}

// Ambil nama user yang login dengan prepared statement
$user_id = $_SESSION['user_id'];
$query_user = "SELECT nama FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $query_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($result_user);
$nama_pengguna = $user['nama'] ?? 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        /* Reset & base style */
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        /* Header & logo */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100px;
            padding: 0 40px;
            background: white;
            max-width: 1440px;
            margin: 0 auto 30px;
            border-bottom: 8px solid #8A282A;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 7px;
            left: 40px;
            width: 140px;
            height: 75px;
            object-fit: contain;
        }

        .judul {
            font-size: 28px;
            color: black;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            top: 18px;
            white-space: nowrap;
            font-weight: bold;
        }

        .welcome {
            margin: 20px 40px;
            font-size: 24px;
            font-family: 'Poppins', cursive;
            font-weight: bold;
        }

        /* Katalog produk container */
        .produk-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .produk-box {
            width: 250px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 10px;
        }

        .produk-item {
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.94);
            border-radius: 16px;
            border: 4px solid #8A282A;
            overflow: hidden;
        }

        .produk-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .produk-img:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }

        .produk-nama,
        .produk-harga {
            width: 100%;
            margin-top: 20px;
            font-weight: bold;
            text-align: left;
        }

        .produk-deskripsi {
            margin-top: 10px;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        .produk-btn {
            display: inline-block;
            background-color: #7D0A0A;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 17px;
            font-size: 1rem;
            cursor: pointer;
            width: 95px;
            height: 50px;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
        }

        .produk-btn:hover,
        .produk-btn:active {
            background-color: #9A1C1C;
        }

        @media screen and (max-width: 768px) {
            .judul {
                font-size: 24px;
            }
            .produk-item {
                width: 90%;
            }
        }

        /* Hamburger menu & dropdown */
        .hamburger-menu {
            position: absolute;
            right: 40px;
            top: 35px;
        }

        .hamburger-icon {
            width: 30px;
            height: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            position: absolute
            z-index: 999;
            margin-left: 40px;
        }

        .hamburger-icon span {
            height: 4px;
            background-color: #8A282A;
            border-radius: 2px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 30px;
            right: 1px;
            background-color: white;
            min-width: 240px;
            width: 240px;
            border: #ccc;
            z-index: 998;
            overflow: hidden;
            border-radius: 10px;
        }

        .dropdown-content a {
            color: #000;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s;
        }

        .dropdown-content a:hover {
            background-color: rgba(138, 40, 42, 0.55);
        }

        /* Modal tampilan gambar */
        #imageModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }

        #imageModal img {
            max-width: 90%;
            max-height: 90%;
            border: 4px solid white;
            border-radius: 10px;
        }

        #imageModal .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
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

<!-- Header dan menu -->
<header>
    <img src="img/sketsa logo br.png" alt="Logo" class="logo"
         onerror="this.onerror=null;this.src='https://placehold.co/246x100';">
    <div class="judul">Produk Tersedia</div>
    <div class="alamat">Jl. Brigjend Katamso No. 250, RT. 03/RW. 34, Mojosongo, Kec. Jebres, Kota Surakarta, Jawa Tengah 57127</div>
    <div class="hamburger-menu">
        <div class="hamburger-icon" onclick="toggleDropdown()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div id="dropdown" class="dropdown-content">
            <a href="history.php?from=katalog.php">Lihat Riwayat Pemesanan</a>
            <a href="dashboard.php">Sign out</a>
        </div>
    </div>
</header>

<div class="welcome">Halo, <?php echo htmlspecialchars($nama_pengguna); ?>!</div>

<!-- Daftar produk -->
<div class="produk-container">
    <?php while ($row = mysqli_fetch_assoc($result_produk)) {
        $gambar = !empty($row['foto_produk']) ? $row['foto_produk'] : "default.png";
        $gambar_path = "katalog/" . $gambar;
    ?>
    <div class="produk-box">
        <div class="produk-item">
            <img src="<?php echo htmlspecialchars($gambar_path); ?>" alt="Gambar Produk"
                 class="produk-img"
                 onclick="showImage('<?php echo htmlspecialchars($gambar_path); ?>')"
                 onerror="this.onerror=null; this.src='katalog/Brosur.png';">
        </div>
        <div class="produk-nama"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
        <div class="produk-deskripsi"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
        <div class="produk-harga">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
        <a href="detail_pemesanan.php?id=<?php echo $row['id']; ?>" class="produk-btn">Pesan</a>
    </div>
    <?php } ?>
</div>

<!-- Modal gambar zoom -->
<div id="imageModal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img id="modalImage" src="">
</div>

<!-- Script interaksi dropdown & modal -->
<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    window.addEventListener("click", function(event) {
        const modal = document.getElementById("imageModal");
        const dropdown = document.getElementById("dropdown");

        if (dropdown && !event.target.closest('.hamburger-menu')) {
            dropdown.style.display = "none";
        }

        if (modal.style.display === "flex" && event.target === modal) {
            closeModal();
        }
    });

    function showImage(src) {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        modal.style.display = "flex";
        modalImg.src = src;
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }
</script>
</body>
</html>
