<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock_id = isset($_POST['stock_id']) ? (int)$_POST['stock_id'] : 0;
    $nickname = trim($_POST['nickname'] ?? '');
    $password = $_POST['password'] ?? '';
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');

    if ($stock_id <= 0 || $nickname === '' || $password === '' || $title === '' || $content === '') {
        exit('모든 항목을 입력해주세요. <a href="javascript:history.back()">뒤로</a>');
    }

    $chk = $pdo->prepare("SELECT id FROM stocks WHERE id = :id");
    $chk->execute([':id' => $stock_id]);
    if (!$chk->fetch()) { exit('존재하지 않는 종목입니다.'); }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO posts (stock_id, nickname, password_hash, title, content) VALUES (:stock_id, :nickname, :hash, :title, :content)");
    $stmt->execute([':stock_id'=>$stock_id, ':nickname'=>$nickname, ':hash'=>$hash, ':title'=>$title, ':content'=>$content]);

    header("Location: view.php?id=" . $pdo->lastInsertId());
    exit;
}
$stocks = $pdo->query("SELECT id, name FROM stocks ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>글쓰기</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>📈 주식 토론 게시판</h1></header>
    <div class="wrap">
        <div class="nav"><a href="index.php">← 메인</a> <a href="list.php">목록</a></div>
        <h2>글쓰기</h2>
        <form method="post">
            <label>종목</label>
            <select name="stock_id" required>
                <option value="">종목 선택</option>
                <?php foreach ($stocks as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>닉네임</label>
            <input type="text" name="nickname" required>
            <label>비밀번호</label>
            <input type="password" name="password" required>
            <label>제목</label>
            <input type="text" name="title" required>
            <label>내용</label>
            <textarea name="content" rows="8" required></textarea>
            <br>
            <button type="submit">등록</button>
            <a href="list.php">취소</a>
        </form>
    </div>
</body>
</html>
