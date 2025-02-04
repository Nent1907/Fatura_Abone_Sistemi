<?php
session_start();

// Eğer oturumda abone numarası yoksa giriş sayfasına yönlendir
if (!isset($_SESSION['aboneNo'])) {
    header('Location: giris.php');
    exit;
}

$aboneNo = $_SESSION['aboneNo']; // Giriş yapan abone numarasını al

// Veritabanı bağlantısı
include('db.php');

// Kullanıcı bakiyesi bilgilerini çek
try {
    $stmt = $pdo->prepare("SELECT * FROM Aboneler WHERE abone_no = :aboneNo");
    $stmt->bindParam(':aboneNo', $aboneNo);
    $stmt->execute();
    
    // Kullanıcı bilgilerini al
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kullanıcı var mı?
    if ($user) {
        $bakiye = isset($user['bakiye']) ? $user['bakiye'] : 0; // Bakiye varsa al, yoksa 0
    } else {
        $bakiye = 0;
    }

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        header {
            background-color: #004ba0;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .balance {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .balance span {
            font-size: 1.2em;
            font-weight: 500;
        }

        .balance button {
            margin-top: 15px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        .balance button:hover {
            background: #0056b3;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 50px;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 1.2em;
            margin: 10px 0 0;
        }

        .card button {
            margin-top: 10px;
            padding: 10px 15px;
            background: #004ba0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .card button:hover {
            background: #003a82;
        }
        .logout-btn {
            display: block; /* Butonu blok seviyesinde yaparak ortalanmasını sağla */
            width: auto; /* Buton genişliğini içeriğe göre ayarla */
            padding: 8px 16px; /* Küçük padding */
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem; /* Küçük font boyutu */
            cursor: pointer;
            margin: 20px auto 0 auto; /* Üstten 20px boşluk, yatayda ortala */
            text-align: center;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        iframe {
            width: 100%;
            border: none;
            height: 100px; /* Yüksekliği ayarlayabilirsiniz */
            margin-top: 20px;
        }


    
    </style>
</head>
<body>
    <header>
        <h1>Fatura Ödeme Sistemi</h1>
    </header>
    <div class="container">
        <div class="balance">
            <p>Abone Numaranız: <span id="aboneNo"><?= $aboneNo ?></span></p>
            <p>Mevcut Bakiye: <span id="bakiye"><?= number_format($bakiye, 2) ?> TL</span></p>
            <button onclick="window.location.href='bakiye_ekle.php'">Bakiye Ekle</button>
        </div>

        <div class="grid">
            <div class="card">
                <img src="https://img.icons8.com/ios-filled/50/004ba0/electricity.png" alt="Elektrik">
                <h3>Elektrik Faturası</h3>
                <form action="elektrik_ode.php" method="get">
                    <input type="hidden" name="abone_id" value="<?= $aboneNo ?>"> <!-- Dinamik Abone -->
                    <button type="submit">Öde</button>
                </form>
            </div>
            <div class="card">
                <img src="https://img.icons8.com/ios-filled/50/004ba0/water.png" alt="Su">
                <h3>Su Faturası</h3>
                <form action="su_ode.php" method="get">
                    <input type="hidden" name="abone_id" value="<?= $aboneNo ?>"> <!-- Dinamik Abone -->
                    <button type="submit">Öde</button>
                </form>
            </div>
            <div class="card">
                <img src="https://img.icons8.com/ios-filled/50/004ba0/fire-element.png" alt="Doğalgaz">
                <h3>Doğalgaz Faturası</h3>
                <form action="dogalgaz_ode.php" method="get">
                    <input type="hidden" name="abone_id" value="<?= $aboneNo ?>"> <!-- Dinamik Abone -->
                    <button type="submit">Öde</button>
                </form>
            </div>
            <div class="card">
                <img src="https://img.icons8.com/ios-filled/50/004ba0/phone.png" alt="Telefon">
                <h3>Telefon Faturası</h3>
                <form action="telefon_ode.php" method="get">
                    <input type="hidden" name="abone_id" value="<?= $aboneNo ?>"> <!-- Dinamik Abone -->
                    <button type="submit">Öde</button>
                </form>
            </div>
        </div>

        <!-- Çıkış Butonu -->
        <button class="logout-btn" onclick="window.location.href='giris.html'; sessionStorage.clear();">Çıkış Yap</button>
        <iframe srcdoc="
            <div style='text-align:center; font-family:Roboto, sans-serif; color: #555; font-size: 14px;'>
                <p>Hakkında | Basın | Telif hakkı</p>
                <p>Bize ulaşın</p>
                <p>Şartlar | Gizlilik | Politika ve Güvenlik</p>
                <p>© 2025 Google LLC</p>
            </div>
        "></iframe>
    </div>
</body>
</html>