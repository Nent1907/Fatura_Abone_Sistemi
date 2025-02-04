<?php
// db.php dosyasını dahil et
include('db.php');

// Oturum kontrolü, oturum başlamış mı kontrol et
session_start();
if (!isset($_SESSION['aboneNo'])) {
    header("Location: giris.php");  // Eğer oturum açılmamışsa, giriş sayfasına yönlendir
    exit();
}

// Oturumdaki abone numarasını al
$aboneNo = $_SESSION['aboneNo'];

// Eğer form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan alınan bakiye bilgisi
    $eklenenBakiye = $_POST['eklenenBakiye'];

    // Veritabanında mevcut bakiyeyi al
    $stmt = $pdo->prepare("SELECT bakiye FROM Aboneler WHERE abone_no = :aboneNo");
    $stmt->bindParam(':aboneNo', $aboneNo);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Mevcut bakiyeyi güncelle
        $yeniBakiye = $user['bakiye'] + $eklenenBakiye;

        // Veritabanını güncelle
        $updateStmt = $pdo->prepare("UPDATE Aboneler SET bakiye = :yeniBakiye WHERE abone_no = :aboneNo");
        $updateStmt->bindParam(':yeniBakiye', $yeniBakiye);
        $updateStmt->bindParam(':aboneNo', $aboneNo);
        $updateStmt->execute();

        // Oturumda bakiyeyi güncelle
        $_SESSION['bakiye'] = $yeniBakiye;

        $message = "Bakiye başarıyla eklendi.";
    } else {
        $error = "Böyle bir abone bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakiye Ekle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        h1 {
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 22px;
            color: #555;
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-size: 18px;
            color: #444;
            margin: 15px 0 10px;
        }
        input[type="number"], select {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }

        /* Style for the return button */
        .return-button {
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .return-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fatura Ödeme Sistemi - Bakiye Ekleme</h1>
        
        <form method="POST" action="bakiye_ekle.php">
            <label for="eklenenBakiye">Eklenecek Bakiye:</label>
            <input type="number" id="eklenenBakiye" name="eklenenBakiye" required>
            
            <label for="banka">Kullanılacak Kart:</label>
            <select id="banka" name="banka" required>
                <option value="" disabled selected>Bir banka seçin</option>
                <option value="ziraat-mastercard">Ziraat Bankası - Mastercard</option>
                <option value="denizbank-mastercard">Denizbank - Mastercard</option>
                <option value="yapikredi-visa">Yapıkredi - Visa</option>
                <option value="qnb-mastercard">QNB - Mastercard</option>
                <option value="halkbank-mastercard">Halkbank - Mastercard</option>
                <option value="halkbank-mastercard">İş Bankası - Visa</option>
            </select>
            
            <button type="submit">Bakiye Ekle</button>
        </form>

        <?php
        if (isset($message)) {
            echo "<p style='color:green;'>$message</p>";
        }

        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>

        <!-- Return to Main Page Button -->
        <a href="main_page.php" class="return-button">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
