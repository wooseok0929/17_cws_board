```php
<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$board_type = "normal";

if (isset($_POST["board_type"])) {
    $board_type = $_POST["board_type"];
}

// 댓글 작성
if (isset($_POST["comment_add"])) {
    $post_id = $_POST["post_id"];
    $content = $_POST["content"];
    $author_id = $_SESSION["user_id"];

    // 댓글을 comments 테이블에 저장(조금 헷갈려서 ai의 도움을 받았습니다)
    mysqli_query($conn,
    "INSERT INTO comments(post_id, author_id, content)
    VALUES($post_id, $author_id, '$content')");
}

// 댓글 수정
if (isset($_POST["comment_edit"])) {
    $id = $_POST["id"];
    $content = $_POST["content"];

    mysqli_query($conn,
    "UPDATE comments
    SET content='$content'
    WHERE id=$id
    AND author_id={$_SESSION['user_id']}");
}

// 댓글 삭제
if (isset($_POST["comment_delete"])) {
    $id = $_POST["id"];

    mysqli_query($conn,
    "DELETE FROM comments
    WHERE id=$id
    AND author_id={$_SESSION['user_id']}");
}

header("Location: index.php?board=$board_type");
?>
```
