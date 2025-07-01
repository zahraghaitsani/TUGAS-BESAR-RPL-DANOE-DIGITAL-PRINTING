<?php 
include 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $foto_nama = '';
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === UPLOAD_ERR_OK && !empty($_FILES['foto_produk']['name'])) {
        $ext = pathinfo($_FILES['foto_produk']['name'], PATHINFO_EXTENSION);
        $foto_nama = uniqid('produk_', true) . '.' . $ext;
        move_uploaded_file($_FILES['foto_produk']['tmp_name'], 'katalog/' . $foto_nama);
    }

    $query = "INSERT INTO produk (nama_produk, harga, deskripsi, foto_produk)
              VALUES ('$nama_produk', $harga, '$deskripsi', '$foto_nama')";

    if (mysqli_query($conn, $query)) {
        $message = "<p class='message success'>Produk berhasil ditambahkan!</p>";
    } else {
        $message = "<p class='message error'>Gagal menambahkan produk!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tambah Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

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

        .back-link-icon {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 10px 20px;
        }

        .back-img {
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }

        .back-text {
            font-size: 18px;
            color: #000000;
            font-weight: bold;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 16px;
            color: #333;
            text-align: center;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 300px;
            padding: 10px 12px;
            border: 1px solid #8A282A;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            background-color: #8A282A;
            color: white;
            padding: 14px 25px;
            border: none;
            border-radius: 17px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 180px;
            height: 50px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 50px;
        }

        button:hover {
            background-color: #6c1e1f;
        }

        .message {
            text-align: center;
            font-weight: 700;
            font-size: 1.1em;
            margin-bottom: 20px;
            border-radius: 10px;
            padding: 12px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="header-container">
    <div class="title-wrapper">
        <img class="logo" src="img/sketsa logo br.png" alt="Logo" />
    </div>
    <div class="page-title">Tambah Produk</div>
    <a href="admin_konfirmasi.php" class="back-link-icon">
        <img src="img/image4.png" alt="Back Icon" class="back-img">
        <span class="back-text">Back</span>
    </a>
</div>

<main class="container">
    <?= $message ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="nama_produk">Nama Produk:</label>
        <input type="text" name="nama_produk" id="nama_produk" required>

        <label for="harga">Harga:</label>
        <input type="number" name="harga" id="harga" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" required></textarea>

        <label for="foto_produk">Foto Produk:</label>
        <input type="file" name="foto_produk" id="foto_produk" accept="image/*" required>

        <button type="submit">Tambah Produk</button>
    </form>
</main>

</body>
</html>
