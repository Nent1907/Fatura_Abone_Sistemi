<?php
// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "fatura_abone");
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// GET parametreleri
$abone_id = $_GET['abone_id'];
$kategori_id = 1; // Elektrik için kategori_id 1

// Fatura bilgilerini çek
$fatura_sorgu = $conn->prepare("
    SELECT fh.odenecek, a.bakiye 
    FROM fatura_hareket AS fh
    JOIN aboneler AS a ON fh.abone_id = a.abone_no
    WHERE fh.abone_id = ? AND fh.kategori_id = ? AND fh.odenecek > 0
");
$fatura_sorgu->bind_param("ii", $abone_id, $kategori_id);
$fatura_sorgu->execute();
$fatura_sorgu->bind_result($miktar, $bakiye);
$fatura_sorgu->fetch();
$fatura_sorgu->close();

// Eğer fatura bulunamadıysa
if (!$miktar) {
    echo "
    <div class='container'>
        <div class='no-bill'>
            <h1>💡 Elektrik Faturası Bulunamadı!</h1>
            <p>Şu anda ödenecek bir elektrik faturanız bulunmamaktadır.</p>
            <p>Eğer başka işlemler yapmak istiyorsanız, <a href='main_page.php'>ana sayfaya</a> dönebilirsiniz.</p>
        </div>
    </div>
    ";
    exit;
}

// Fatura ödeme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bakiye >= $miktar) {
        // Bakiye düşür ve faturayı sıfırla
        $conn->begin_transaction();
        try {
            $bakiye_guncelle = $conn->prepare("UPDATE aboneler SET bakiye = bakiye - ? WHERE abone_no = ?");
            $bakiye_guncelle->bind_param("di", $miktar, $abone_id);
            $bakiye_guncelle->execute();

            $fatura_guncelle = $conn->prepare("UPDATE fatura_hareket SET odenecek = 0 WHERE abone_id = ? AND kategori_id = ?");
            $fatura_guncelle->bind_param("ii", $abone_id, $kategori_id);
            $fatura_guncelle->execute();

            $conn->commit();
            echo "<div class='success'>Ödeme başarılı! Ana sayfaya yönlendiriliyorsunuz...</div>";
            header("Refresh: 3; url=main_page.php");
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert'>Ödeme sırasında bir hata oluştu: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert'>Bakiyeniz yetersiz! Ana sayfaya dönüyorsunuz...</div>";
        header("Refresh: 3; url=main_page.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elektrik Faturası Öde</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .no-bill {
        background: #e9f7fe;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 400px;
        margin: 0 auto;
        }
        .no-bill h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }
        .no-bill p {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }
        .no-bill a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .no-bill a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Elektrik Faturası Ödeme</h1>
        <p>Ödenecek Tutar: <?= number_format($miktar, 2) ?> TL</p>
        <p>Mevcut Bakiye: <?= number_format($bakiye, 2) ?> TL</p>
        <form method="post">
            <button type="submit">Öde</button>
        </form>
    </div>
</body>
</html>