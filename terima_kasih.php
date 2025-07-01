<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Terima Kasih - Danoe Digital Printing</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            overflow-y: hidden;
        }

        .SketsaLogoBr {
            position: absolute;
            top: 7px;
            left: 40px;
            width: 140px;
            height: 75px;
            object-fit: contain;
        }

        .Bg1 {
            position: absolute;
            top: 92px;
            left: 0;
            width: 100%;
            height: 540px;
            object-fit: cover;
            box-shadow: 0px 4px 53px -66px rgba(0, 0, 0, 0.25);
        }

        .Rectangle21 {
            width: 650px;
            max-width: 90vw;
            height: 250px;
            background: rgba(138, 40, 42, 0.55);
            border-radius: 20px;
            position: absolute;
            left: 50%;
            top: 250px;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px 40px;
            box-sizing: border-box;
            color: white;
            text-align: center;
        }

        .TerimaKasih {
            font-size: 2rem;
            font-weight: 400;
            user-select: none;
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .PesananAndaTelahDiterimaKamiAkanSegeraMengonfirmasiPembayaranAnda {
            width: 100%;
            font-size: 1.5rem;
            font-weight: 400;
            line-height: 1;
            user-select: none;
            white-space: pre-line;
        }

        @media (max-width: 768px) {
            .Rectangle21 {
                width: 90vw;
                height: auto;
                padding: 30px 20px;
            }

            .TerimaKasih {
                font-size: 2rem;
            }

            .PesananAndaTelahDiterimaKamiAkanSegeraMengonfirmasiPembayaranAnda {
                font-size: 1.3rem;
            }

            .btn {
                top: calc(50% + 200px);
                font-size: 1.2rem;
                padding: 12px 30px;
            }
        }

        .hamburger-icon {
            position: absolute;
            top: 35px;
            right: 30px;
            width: 30px;
            height: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            z-index: 999;
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
            top: 70px;
            right: 30px;
            background-color: white;
            min-width: 180px;
            z-index: 998;
            border-radius: 8px;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .dropdown-content a {
            padding: 12px 16px;
            display: block;
            color: #000000;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .dropdown-content a:hover {
            background-color: rgba(138, 40, 42, 0.55);
        }
    </style>
</head>
<body>
    <div class="TerimaKasihPage">
        <img src="img/sketsa logo br.png" alt="Logo" class="SketsaLogoBr" />

        <img src="img/bg.png" alt="Background" class="Bg1" />

        <div class="hamburger-icon" onclick="toggleDropdown()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div id="dropdown" class="dropdown-content">
            <a href="history.php?from=terima_kasih.php">Lihat Riwayat Pemesanan</a>
            <a href="dashboard.php">Sign out</a>
        </div>

        <div class="Rectangle21">
            <div class="TerimaKasih">Terima Kasih!</div>
            <div class="PesananAndaTelahDiterimaKamiAkanSegeraMengonfirmasiPembayaranAnda">
                Pesanan Anda telah diterima.<br />
                Kami akan segera mengonfirmasi pembayaran Anda.
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown");
            dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.hamburger-icon') && !event.target.closest('.hamburger-icon')) {
                var dropdown = document.getElementById("dropdown");
                if (dropdown && dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        };
    </script>
</body>
</html>
