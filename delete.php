<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { exit('잘못된 접근입니다.'); }

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { exit('글을 찾을 수 없습니다.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (!password_verify($password, $post['password_hash'])) {
        exit('비밀번호가 일치하지 않습니다. <a href="javascript:history.back()">뒤로</a>');
    }
    $pdo->prepare("DELETE FROM posts WHERE id = :id")->execute([':id' => $id]);
    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>글 삭제</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>📈 주식 토론 게시판</h1></header>
    <div class="wrap">
        <div class="nav"><a href="view.php?id=<?= $id ?>">← 글로</a></div>
        <h2>글 삭제</h2>
        <p>"<?= htmlspecialchars($post['title']) ?>" 글을 삭제하시겠습니까?</p>
        <form method="post">
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit">삭제</button>
            <a href="view.php?id=<?= $id ?>">취소</a>
        </form>
    </div>
</body>
</html>
