<?php
$host = 'localhost';  // Veritabanı host adı (localhost)
$dbname = 'fatura_abone';  // Veritabanı adı
$username = 'root';  // MySQL kullanıcı adı
$password = '';  // MySQL şifresi, XAMPP'yi varsayılan olarak şifresiz kullanabilirsiniz

try {
    // PDO ile veritabanına bağlanma
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası durumunda mesaj gösterme
    echo "Bağlantı hatası: " . $e->getMessage();
}
?>
