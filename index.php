<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$board_type = "normal";

if (isset($_GET["board"])) {
    $board_type = $_GET["board"];
}

if (isset($_POST["board_type"])) {
    $board_type = $_POST["board_type"];
}

// 게시글 작성
if (isset($_POST["post_add"])) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $author_id = $_SESSION["user_id"];

    mysqli_query($conn,
    "INSERT INTO posts(title, content, author_id, board_type)
    VALUES('$title', '$content', $author_id, '$board_type')");

    $post_id = mysqli_insert_id($conn);

    // 파일 처리하는 부분이 너무 어려워서 ai의 도움을 받았습니다
    if ($_FILES["file"]["name"] != "") {
        $filename = $_FILES["file"]["name"];
        $filepath = "upload/" . $filename;

        move_uploaded_file($_FILES["file"]["tmp_name"], $filepath);

        mysqli_query($conn,
        "INSERT INTO attachments(post_id, filename, filepath)
        VALUES($post_id, '$filename', '$filepath')");
    }
}

// 게시글 수정
if (isset($_POST["post_edit"])) {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    mysqli_query($conn,
    "UPDATE posts
    SET title='$title', content='$content'
    WHERE id=$id
    AND author_id={$_SESSION['user_id']}");

    // 새 파일을 선택하면 기존 파일 정보 교체
    if ($_FILES["file"]["name"] != "") {
        $filename = $_FILES["file"]["name"];
        $filepath = "upload/" . $filename;

        move_uploaded_file($_FILES["file"]["tmp_name"], $filepath);

        mysqli_query($conn,
        "DELETE FROM attachments
        WHERE post_id=$id");

        mysqli_query($conn,
        "INSERT INTO attachments(post_id, filename, filepath)
        VALUES($id, '$filename', '$filepath')");
    }
}

//ai의 도움을 받은 부분
if (isset($_POST["post_delete"])) {
    $id = $_POST["id"];

    $check = mysqli_query($conn,
    "SELECT id FROM posts
    WHERE id=$id
    AND author_id={$_SESSION['user_id']}");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn,
        "DELETE FROM comments
        WHERE post_id=$id");

        mysqli_query($conn,
        "DELETE FROM attachments
        WHERE post_id=$id");

        mysqli_query($conn,
        "DELETE FROM posts
        WHERE id=$id");
    }
}

// 검색과 정렬
$search = "";
$user_search = "";
$order = "DESC";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

if (isset($_GET["user_search"])) {
    $user_search = $_GET["user_search"];
}

if (isset($_GET["order"]) && $_GET["order"] == "old") {
    $order = "ASC";
}

// 유저 검색
$users = mysqli_query($conn,
"SELECT username
FROM users
WHERE username LIKE '%$user_search%'
ORDER BY username ASC");

// 너무 여러 조건이 들어가서 ai의 도움을 받았습니다.
$posts = mysqli_query($conn,
"SELECT posts.id, posts.title, posts.content,
posts.author_id, users.username
FROM posts
JOIN users ON posts.author_id = users.id
WHERE posts.board_type='$board_type'
AND (
posts.title LIKE '%$search%'
OR posts.content LIKE '%$search%'
)
ORDER BY posts.id $order");
?>

<h1>게시판</h1>

<p>
로그인 사용자: <?php echo $_SESSION["username"]; ?>
<a href="logout.php">로그아웃</a>
</p>

<p>
<a href="index.php?board=normal">일반 게시판</a>
|
<a href="index.php?board=basketball">농구 게시판</a>
</p>

<?php if ($board_type == "basketball") { ?>
<h2>농구 게시판</h2>
<?php } else { ?>
<h2>일반 게시판</h2>
<?php } ?>

<form method="get">
    <input type="hidden"
    name="board"
    value="<?php echo $board_type; ?>">

    게시물 검색
    <input name="search"
    value="<?php echo $search; ?>">

    <select name="order">
        <option value="new">최신순</option>
        <option value="old">오래된순</option>
    </select>

    <button>검색</button>
</form>

<form method="get">
    <input type="hidden"
    name="board"
    value="<?php echo $board_type; ?>">

    유저 검색
    <input name="user_search"
    value="<?php echo $user_search; ?>">

    <button>검색</button>
</form>

<?php if ($user_search != "") { ?>

<h3>유저 검색 결과</h3>

<?php while ($user = mysqli_fetch_assoc($users)) { ?>
<p><?php echo $user["username"]; ?></p>
<?php } ?>

<?php } ?>

<hr>

<form method="post" enctype="multipart/form-data">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    제목 <input name="title">
    내용 <input name="content">
    파일 <input type="file" name="file">

    <button name="post_add">게시글 작성</button>
</form>

<hr>

<?php while ($post = mysqli_fetch_assoc($posts)) { ?>

<h3><?php echo $post["title"]; ?></h3>

<p>
<?php echo $post["username"]; ?> :
<?php echo $post["content"]; ?>
</p>

<?php

// 현재 게시글 번호와 같은 첨부파일을 가져오는 부분
$files = mysqli_query($conn,
"SELECT *
FROM attachments
WHERE post_id={$post['id']}");

while ($file = mysqli_fetch_assoc($files)) {
?>

<p>
첨부파일:
<a href="<?php echo $file["filepath"]; ?>" download>
<?php echo $file["filename"]; ?>
</a>
</p>

<?php } ?>

<?php if ($_SESSION["user_id"] == $post["author_id"]) { ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    <input type="hidden"
    name="id"
    value="<?php echo $post["id"]; ?>">

    제목
    <input name="title"
    value="<?php echo $post["title"]; ?>">

    내용
    <input name="content"
    value="<?php echo $post["content"]; ?>">

    파일
    <input type="file" name="file">

    <button name="post_edit">
    게시글 수정
    </button>
</form>

<?php } ?>

<form action="comment.php" method="post">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    <input type="hidden"
    name="post_id"
    value="<?php echo $post["id"]; ?>">

    <input name="content">

    <button name="comment_add">
    댓글 작성
    </button>
</form>

<?php

// 이 부분도 같이 도움을 받았습니다.
// 현재 게시글 번호와 같은 댓글만 가져오는 부분
$comments = mysqli_query($conn,
"SELECT comments.id, comments.content,
comments.author_id, users.username
FROM comments
JOIN users ON comments.author_id = users.id
WHERE comments.post_id={$post['id']}");

while ($comment = mysqli_fetch_assoc($comments)) {
?>

<p>
ㄴ <?php echo $comment["username"]; ?> :
<?php echo $comment["content"]; ?>
</p>

<?php if ($_SESSION["user_id"] == $comment["author_id"]) { ?>

<form action="comment.php" method="post">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    <input type="hidden"
    name="id"
    value="<?php echo $comment["id"]; ?>">

    <input name="content"
    value="<?php echo $comment["content"]; ?>">

    <button name="comment_edit">
    댓글 수정
    </button>
</form>

<form action="comment.php" method="post">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    <input type="hidden"
    name="id"
    value="<?php echo $comment["id"]; ?>">

    <button name="comment_delete">
    댓글 삭제
    </button>
</form>

<?php } ?>

<?php } ?>

<?php if ($_SESSION["user_id"] == $post["author_id"]) { ?>

<form method="post">
    <input type="hidden"
    name="board_type"
    value="<?php echo $board_type; ?>">

    <input type="hidden"
    name="id"
    value="<?php echo $post["id"]; ?>">

    <button name="post_delete">
    게시글 삭제
    </button>
</form>

<?php } ?>

<hr>

<?php } ?>
