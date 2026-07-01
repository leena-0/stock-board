<?php
require_once 'config.php';

$stocks = $pdo->query("
    SELECT s.id, s.name, s.code, s.price,
           COUNT(p.id) AS post_count
    FROM stocks s
    LEFT JOIN posts p ON p.stock_id = s.id
    GROUP BY s.id, s.name, s.code, s.price
    ORDER BY s.name
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>주식 토론 게시판</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📈 주식 토론 게시판</h1>
        <p>종목을 선택해 토론방에 입장하세요</p>
    </header>

    <div class="wrap">
        <div class="grid">
            <?php foreach ($stocks as $s): ?>
                <a class="card" href="list.php?stock_id=<?= $s['id'] ?>">
                    <div class="name"><?= htmlspecialchars($s['name']) ?></div>
                    <div class="code"><?= htmlspecialchars($s['code']) ?></div>
                    <div class="price"><?= number_format($s['price']) ?>원</div>
                    <div class="count">💬 글 <?= $s['post_count'] ?>개</div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
