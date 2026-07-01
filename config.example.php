<?php
// config.php 예시 (실제 값을 채워 config.php로 저장해서 사용)
$host    = 'DB_호스트_주소';
$port    = '3306';
$dbname  = 'stock_board';
$charset = 'utf8mb4';
$user    = 'DB_사용자명';
$pass    = 'DB_비밀번호';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('데이터베이스 연결에 실패했습니다.');
}
