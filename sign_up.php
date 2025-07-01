<?php
session_start();

// Konfigurasi koneksi database
$host = 'localhost';
$dbname = 'percetakan';
$username = 'root';
$password = '';

try {
    // Membuat koneksi ke database menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Koneksi gagal
    die("Connection failed: " . $e->getMessage());
}

// Ambil pesan error dari session (jika ada) dan kemudian hapus dari session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']);

// Proses form ketika ada request POST (user submit form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bersihkan input dari user
    $nama = trim(htmlspecialchars($_POST['nama']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password_input = $_POST['password'];

    // Cek apakah email sudah terdaftar
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // Jika email sudah ada, set pesan error dan redirect ke halaman login
        $_SESSION['error_message'] = 'Email sudah terdaftar. Silakan <a href="sign_in.php"><strong>Sign in</strong></a>';
        header("Location: sign_up.php");
        exit;
    }

    // Jika email belum terdaftar, hash password dan simpan data ke database
    $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)");
    $stmt->execute([
        'nama' => $nama,
        'email' => $email,
        'password' => $hashed_password
    ]);

    // Redirect ke halaman login setelah berhasil sign up
    header("Location: sign_in.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign up - Danoe Digital Printing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=ADLaM+Display&display=swap" rel="stylesheet">
    <!-- Style untuk tampilan halaman up -->
    <style>
        /* Reset dan font dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            flex-direction: row;
        }
        /* Bagian kiri (gambar) */
        .left {
            flex: 1;
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .left img {
            height: 632.5px;
            width: 680px;
            object-fit: cover;
        }
        /* Bagian kanan (form) */
        .right {
            flex: 1;
            background-color: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            max-width: 600px;
            margin: auto;
        }
        .title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 75px;
            font-weight: bold;
        }
        .input-group {
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            pointer-events: none;
        }
        .input-wrapper input {
            padding-left: 40px;
            height: 50px;
            width: 100%;
            border: 1px solid #8A282A;
            border-radius: 15px;
            background-color: #FFFFFF;
            font-size: 1rem;
            color: black;
        }
        .signup-btn {
            margin-top: 30px;
            background-color: #7D0A0A;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
            width: 120px;
            height: 45px;
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: 440px;
            left: 925px;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        .signup-btn:hover {
            background-color: #9A1C1C;
        }
        .signup-text {
            text-align: center;
            font-size: 1rem;
            margin-top: 100px;
        }
        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: auto;
            }
            .left, .right {
                flex: unset;
                width: 100%;
                padding: 1rem;
            }
            .left img {
                width: 100%;
                height: auto;
            }
            .input-group {
                width: 100%;
            }
            .signup-btn {
                position: static;
                margin: 20px auto;
                width: 100%;
            }
            .signup-text {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Class bagian kiri halaman -->
    <div class="left">
        <img src="img/bg.png" alt="Gambar produk" />
    </div>

    <!-- Class bagian kanan halaman -->
    <div class="right">
        <div class="title">Sign Up</div>

        <!-- Tampilkan pesan error jika ada -->
        <?php if (!empty($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        } ?>

        <!-- Formulir sign up -->
        <form method="POST" action="">
            <div class="input-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-wrapper">
                    <img src="img/image1.png" class="input-icon" alt="Nama Icon">
                    <input type="text" name="nama" id="nama" required />
                </div>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <img src="img/image2.png" class="input-icon" alt="Email Icon">
                    <input type="email" name="email" id="email" required />
                </div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <img src="img/image3.png" class="input-icon" alt="Password Icon">
                    <input type="password" name="password" id="password" required />
                </div>
            </div>
            <button class="signup-btn" type="submit">Sign Up</button>
        </form>

        <!-- Link ke halaman login -->
        <div class="signup-text">
            Sudah memiliki akun? <a href="sign_in.php"><strong>Sign in</strong></a>
        </div>
    </div>
</body>
</html>
