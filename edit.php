<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { exit('잘못된 접근입니다.'); }

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { exit('글을 찾을 수 없습니다.'); }

$stocks = $pdo->query("SELECT id, name FROM stocks ORDER BY name")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (($_POST['action'] ?? '') === 'verify') {
        if (!password_verify($password, $post['password_hash'])) { $error = '비밀번호가 일치하지 않습니다.'; }
        else { $verified = true; }
    }
    if (($_POST['action'] ?? '') === 'update') {
        if (!password_verify($password, $post['password_hash'])) { exit('비밀번호가 일치하지 않습니다.'); }
        $stock_id = (int)($_POST['stock_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($stock_id <= 0 || $title === '' || $content === '') {
            exit('모든 항목을 입력해주세요. <a href="javascript:history.back()">뒤로</a>');
        }
        $upd = $pdo->prepare("UPDATE posts SET stock_id=:stock_id, title=:title, content=:content WHERE id=:id");
        $upd->execute([':stock_id'=>$stock_id, ':title'=>$title, ':content'=>$content, ':id'=>$id]);
        header("Location: view.php?id=" . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>글 수정</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>📈 주식 토론 게시판</h1></header>
    <div class="wrap">
        <div class="nav"><a href="view.php?id=<?= $id ?>">← 글로</a></div>
        <h2>글 수정</h2>
        <?php if (!empty($verified)): ?>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="password" value="<?= htmlspecialchars($password) ?>">
                <label>종목</label>
                <select name="stock_id" required>
                    <?php foreach ($stocks as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $post['stock_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>제목</label>
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                <label>내용</label>
                <textarea name="content" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
                <br>
                <button type="submit">수정 완료</button>
                <a href="view.php?id=<?= $id ?>">취소</a>
            </form>
        <?php else: ?>
            <p>"<?= htmlspecialchars($post['title']) ?>" 글을 수정하려면 비밀번호를 입력하세요.</p>
            <?php if ($error): ?><p style="color:#f85149;"><?= $error ?></p><?php endif; ?>
            <form method="post">
                <input type="hidden" name="action" value="verify">
                <input type="password" name="password" placeholder="비밀번호" required>
                <button type="submit">확인</button>
                <a href="view.php?id=<?= $id ?>">취소</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
