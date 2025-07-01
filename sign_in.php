<?php
session_start();

// Konfigurasi koneksi database dengan PDO
$host = 'localhost';
$dbname = 'percetakan';
$username = 'root';
$password = '';

try {
    // Membuat objek PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Mengatur mode error PDO ke Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error dan hentikan eksekusi
    exit("Connection failed: " . $e->getMessage());
}

// Ambil error message dari session jika ada
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Jika request method POST, berarti user submit form sign in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input email dan ambil password input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password_input = $_POST['password'];

    // Query ambil data user berdasarkan email dengan prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika user tidak ditemukan
if (!$user) {
    $_SESSION['error_message'] = 'Akun Anda belum tersedia. Silakan <a href="sign_up.php"><strong>Sign Up</strong></a>';
    header("Location: sign_in.php");
    exit();
}

// Verifikasi password hash
if (password_verify($password_input, $user['password'])) {
    // Password benar
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        header("Location: admin_konfirmasi.php");
    } else {
        header("Location: katalog.php");
    }
    exit();
} else {
    // Password salah
    $_SESSION['error_message'] = "Password salah.";
    header("Location: sign_in.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in- Danoe Digital Printing</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=ADLaM+Display&display=swap" rel="stylesheet">

    <!-- Style untuk tampilan halaman sign in -->
    <style>
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
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 3rem;
    }

    .form-container {
        width: 100%;
        max-width: 350px;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .input-group {
        margin-bottom: 60px;
        width: 100%;
        max-width: 400px;
    }

    .input-group label {
        display: block;
        margin-bottom: 10px;
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

    .login-btn {
        background-color: #7D0A0A;
        color: white;
        border: none;
        border-radius: 20px;
        font-size: 1rem;
        cursor: pointer;
        width: 120px;
        height: 45px;
        left: 925px;
        top: 510px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        transition: transform 0.2s ease, background-color 0.3s ease;
    }

    .login-btn:hover,
    .login-btn:active {
        background-color: #9A1C1C;
    }

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

        .login-btn {
            position: static;
            margin: 20px auto;
            width: 100%;
        }

        .login-text {
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

    <!-- Class bagian kanan halaman-->
    <div class="right">
        <div class="title">Sign in</div>
        <?php if (!empty($error_message)) {
           echo "<p style='color:red; margin-top: 15px;'>" . $error_message . "</p>";
        } ?>
        <form method="POST" action="">
            <div class="form-container">
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
                <button class="login-btn" type="submit">Sign in</button>
            </div>
        </form>
    </div>
</body>
</html>
