<?php
session_start();
include('db.php');

// Formun gönderilip gönderilmediğini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST verilerini al
    $aboneNo = isset($_POST['abone_no']) ? trim($_POST['abone_no']) : null;
    $password = isset($_POST['sifre']) ? trim($_POST['sifre']) : null;

    // Eksik bilgi varsa hata döndür
    if (empty($aboneNo) || empty($password)) {
        echo "Abone numarası ve şifre boş olamaz!";
        exit;
    }

    try {
        // Abone numarası ve şifreyi kontrol et
        $stmt = $pdo->prepare("SELECT * FROM Aboneler WHERE abone_no = :aboneNo AND password = :password");
        $stmt->bindParam(':aboneNo', $aboneNo);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kullanıcıyı oturuma kaydet
            $_SESSION['aboneNo'] = $user['abone_no'];
            header('Location: main_page.php');
            exit;
        } else {
            echo "Hatalı abone numarası veya şifre!";
        }
    } catch (PDOException $e) {
        echo "Veritabanı hatası: " . $e->getMessage();
    }
} else {
    // Eğer form gönderilmemişse
    echo "Bu sayfaya doğrudan erişim yapılamaz.";
    exit;
}
?>
