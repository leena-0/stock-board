<?php require_once 'config.php';

$search = isset($_GET['q'])? $_GET['q'] : '';
$stockId = isset($_GET['stock_id'])? (int)$_GET['stock_id'] : 0;

$sql = "
    SELECT p.*, s.name AS stock_name, s.price
    FROM posts p
    JOIN stocks s ON p.stock_id = s.id
    WHERE 1=1
";
$params = [];

if ($stockId) {
    $sql.= " AND p.stock_id = :stock_id";
    $params[':stock_id'] = $stockId;
}
if ($search) {
    $sql.= " AND p.title LIKE :search";
    $params[':search'] = "%$search%";
}
$sql.= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll();

$stocks = $pdo->query("SELECT id, name FROM stocks ORDER BY name")->fetchAll();
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
        <p>종목별 토론 목록</p>
    </header>

    <div class="wrap">
        <div class="nav">
            <a href="index.php">← 메인</a>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <form method="get">
                <select name="stock_id">
                    <option value="0">전체 종목</option>
                    <?php foreach ($stocks as $stock): ?>
                        <option value="<?= $stock['id'] ?>" <?= $stockId == $stock['id']? 'selected' : '' ?>>
                            <?= htmlspecialchars($stock['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="q" placeholder="제목 검색..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">필터</button>
            </form>
            <button onclick="location.href='write.php'">글쓰기</button>
        </div>

        <?php if ($result): ?>
            <table>
                <thead>
                    <tr><th>종목</th><th>종가</th><th>제목</th><th>작성자</th><th>작성일</th><th>조회수</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['stock_name']) ?></td>
                            <td class="price-up"><?= number_format($row['price']) ?>원</td>
                            <td><a href="view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                            <td><?= htmlspecialchars($row['nickname']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= $row['views'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#8b949e;">검색 결과가 없습니다</p>
        <?php endif; ?>
    </div>
</body>
</html>
