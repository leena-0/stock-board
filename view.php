<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { exit('잘못된 접근입니다.'); }

$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = :id")->execute([':id' => $id]);

$stmt = $pdo->prepare("
    SELECT p.*, s.name AS stock_name, s.price
    FROM posts p JOIN stocks s ON p.stock_id = s.id
    WHERE p.id = :id
");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { exit('글을 찾을 수 없습니다.'); }

$cstmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = :id ORDER BY created_at ASC");
$cstmt->execute([':id' => $id]);
$comments = $cstmt->fetchAll();

$parents = [];
$children = [];
foreach ($comments as $c) {
    if ($c['parent_id'] === null) { $parents[] = $c; }
    else { $children[$c['parent_id']][] = $c; }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📈 주식 토론 게시판</h1>
    </header>

    <div class="wrap">
        <div class="nav">
            <a href="index.php">← 메인</a>
            <a href="list.php">목록</a>
        </div>

        <div class="post-head">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <div class="meta">
                종목: <?= htmlspecialchars($post['stock_name']) ?>
                (<span class="price-up"><?= number_format($post['price']) ?>원</span>) |
                작성자: <?= htmlspecialchars($post['nickname']) ?> |
                <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?> |
                조회수: <?= $post['views'] ?>
            </div>
        </div>

        <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

        <div class="nav">
            <a href="edit.php?id=<?= $post['id'] ?>">수정</a>
            <a href="delete.php?id=<?= $post['id'] ?>">삭제</a>
        </div>

        <hr style="border-color:#30363d;">
        <h3>댓글 <?= count($comments) ?>개</h3>

        <?php foreach ($parents as $p): ?>
            <div class="comment">
                <div class="cmeta"><?= htmlspecialchars($p['nickname']) ?> · <?= date('Y-m-d H:i', strtotime($p['created_at'])) ?></div>
                <div><?= nl2br(htmlspecialchars($p['content'])) ?></div>

                <?php if (!empty($children[$p['id']])): ?>
                    <?php foreach ($children[$p['id']] as $ch): ?>
                        <div class="comment reply">
                            <div class="cmeta">↳ <?= htmlspecialchars($ch['nickname']) ?> · <?= date('Y-m-d H:i', strtotime($ch['created_at'])) ?></div>
                            <div><?= nl2br(htmlspecialchars($ch['content'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form action="comment.php" method="post" style="margin-top:6px;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input type="hidden" name="parent_id" value="<?= $p['id'] ?>">
                    <input type="text" name="nickname" placeholder="닉네임" required>
                    <input type="password" name="password" placeholder="비밀번호" required>
                    <input type="text" name="content" placeholder="답글..." required>
                    <button type="submit">답글</button>
                </form>
            </div>
        <?php endforeach; ?>

        <hr style="border-color:#30363d;">
        <h4>댓글 작성</h4>
        <form action="comment.php" method="post">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <input type="text" name="nickname" placeholder="닉네임" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <br>
            <textarea name="content" rows="3" cols="50" placeholder="댓글 내용" required></textarea>
            <br>
            <button type="submit">댓글 등록</button>
        </form>
    </div>
</body>
</html>
