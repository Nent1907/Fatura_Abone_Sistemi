<?php
// Veritabanı bağlantısı
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler
    $aboneNo = $_POST['registerAboneNo'] ?? '';
    $password = $_POST['registerPassword'] ?? '';  // sifre yerine password kullanıyoruz
    $confirmPassword = $_POST['confirmRegisterPassword'] ?? '';
    $address = $_POST['address'] ?? '';

    // Alanların boş olup olmadığını kontrol et
    if (empty($aboneNo) || empty($password) || empty($confirmPassword) || empty($address)) {
        echo "Lütfen tüm alanları doldurun.";
        exit;
    }

    // Şifrelerin eşleşip eşleşmediğini kontrol et
    if ($password !== $confirmPassword) {
        echo "Şifreler birbirini tutmuyor.";
        exit;
    }

    try {
        // Abone numarasının zaten var olup olmadığını kontrol et
        $stmt = $pdo->prepare("SELECT * FROM Aboneler WHERE abone_no = :aboneNo");
        $stmt->bindParam(':aboneNo', $aboneNo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Bu abone numarası zaten kayıtlı.";
            exit;
        }

        // Kullanıcıyı veritabanına ekle
        $stmt = $pdo->prepare("INSERT INTO Aboneler (abone_no, password, adres, bakiye) VALUES (:aboneNo, :password, :adres, 0)");  // sifre yerine password kullanıyoruz
        $stmt->bindParam(':aboneNo', $aboneNo);
        $stmt->bindParam(':password', $password);  // sifre yerine password kullanıyoruz
        $stmt->bindParam(':adres', $address);
        $stmt->execute();

        echo "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz.";
        header("Refresh: 2; url=giris.html");
        exit;
    } catch (PDOException $e) {
        echo "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        h1 {
            margin-bottom: 20px;
            color: #333333;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .radio-group {
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }
        .radio-group label {
            font-weight: normal;
            margin: 0;
        }
        .radio-group input {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kayıt Ol</h1>
        <form method="POST" action="kayit.php">
            <label for="registerAboneNo">Abonelik Numarası:</label>
            <input type="text" id="registerAboneNo" name="registerAboneNo" required>
            <label for="registerPassword">Şifre:</label>
            <input type="password" id="registerPassword" name="registerPassword" required>
            <label for="confirmRegisterPassword">Şifre (Tekrar):</label>
            <input type="password" id="confirmRegisterPassword" name="confirmRegisterPassword" required>
            <label for="address">Adres:</label>
            <textarea id="address" name="address" rows="4" required></textarea>
            <label>Cinsiyet:</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="kız" required> Kız</label>
                <label><input type="radio" name="gender" value="erkek"> Erkek</label>
            </div>
            <button type="submit">Kayıt Ol</button>
        </form>
    </div>
</body>
</html>

