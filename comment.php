<?php
require_once 'config.php';

// POST로 들어온 값만 처리
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('잘못된 접근입니다.');
}

// 입력값 받기
$post_id   = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
             ? (int)$_POST['parent_id'] : null;
$nickname  = trim($_POST['nickname'] ?? '');
$password  = $_POST['password'] ?? '';
$content   = trim($_POST['content'] ?? '');

// 빈 입력 검증 (오류 처리)
if ($post_id <= 0 || $nickname === '' || $password === '' || $content === '') {
    exit('모든 항목을 입력해주세요. <a href="javascript:history.back()">뒤로</a>');
}

// 글이 실제로 존재하는지 확인
$check = $pdo->prepare("SELECT id FROM posts WHERE id = :id");
$check->execute([':id' => $post_id]);
if (!$check->fetch()) {
    exit('존재하지 않는 글입니다.');
}

// 비밀번호 해시 처리
$hash = password_hash($password, PASSWORD_DEFAULT);

// 댓글 저장 (prepared statement)
$stmt = $pdo->prepare("
    INSERT INTO comments (post_id, parent_id, nickname, password_hash, content)
    VALUES (:post_id, :parent_id, :nickname, :hash, :content)
");
$stmt->execute([
    ':post_id'   => $post_id,
    ':parent_id' => $parent_id,
    ':nickname'  => $nickname,
    ':hash'      => $hash,
    ':content'   => $content,
]);

// 원래 글로 돌아가기
header("Location: view.php?id=" . $post_id);
exit;
