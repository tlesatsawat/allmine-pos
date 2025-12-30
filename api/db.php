<?php
// ดึงค่าจาก Vercel Environment Variables (ไม่ต้องแก้ตรงนี้)
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname, $port);
$conn->set_charset("utf8");

// เช็ค Error
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
?>
