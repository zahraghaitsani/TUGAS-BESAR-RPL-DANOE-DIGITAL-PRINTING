<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Import font dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=ADLaM+Display&display=swap" rel="stylesheet">

    <!-- Style untuk tata letak dan tampilan halaman -->
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .main-container {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: white;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: white;
        }

        .logo img {
            position: absolute;
            top: 7px;
            left: 40px;
            width: 140px;
            height: 75px;
            object-fit: contain;
        }

        .header-links {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            top: 16px;
        }

        .header-link {
            color: black;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }

        .divider {
            height: 20px;
            width: 2px;
            background-color: black;
        }

        .main-image {
            position: absolute;
            top: 92px;
            left: 0;
            width: 100%;
            height: 540px;
            object-fit: cover;
            box-shadow: 0px 4px 53px -66px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 768px) {
            .header-bar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .main-image {
                height: auto;
                max-height: 400px;
            }

            .logo img {
                width: 120px;
            }

            .header-link {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                width: 100px;
            }

            .header-link {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Membuat header yang berisi logo dan link navigasi Sign up & Sign in -->
        <div class="header-bar">
            <div class="logo">
                <img src="img/sketsa logo br.png" alt="Logo">
            </div>
            <div class="header-links">
                <a href="sign_up.php" class="header-link">Sign up</a>
                <div class="divider"></div>
                <a href="sign_in.php" class="header-link">Sign in</a>
            </div>
        </div>

        <!-- Gambar utama di bawah header -->
        <img src="img/bg.png" alt="Gambar utama" class="main-image">
    </div>
</body>

</html>
