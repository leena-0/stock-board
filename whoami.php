<?php
$ip = $_SERVER['SERVER_ADDR'] ?? gethostname();
echo "<h1 style='font-family:sans-serif'>응답한 서버: " . htmlspecialchars($ip) . "</h1>";
echo "<p>새로고침할 때마다 IP가 바뀌면 로드밸런서가 분산하는 것입니다.</p>";
