<?php
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');

// เริ่มต้น MySQLi แบบรองรับ SSL
$conn = mysqli_init();
if (!$conn) {
    die(json_encode(["error" => "mysqli_init failed"]));
}

// ตั้งค่า SSL (ไม่ต้องระบุไฟล์ cert ก็ได้สำหรับ TiDB Cloud ส่วนใหญ่)
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL); 

// เชื่อมต่อจริง
if (!$conn->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die(json_encode(["error" => "Connect Error: " . mysqli_connect_error()]));
}

$conn->set_charset("utf8");
?>
