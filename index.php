<?php
include "db.php";

if (isset($_POST["post_add"])) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $author_id = $_POST["author_id"];

    mysqli_query($conn,
    "INSERT INTO posts(title, content, author_id)
    VALUES('$title', '$content', $author_id)");
}

if (isset($_POST["post_edit"])) {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    mysqli_query($conn,
    "UPDATE posts
    SET title='$title', content='$content'
    WHERE id=$id");
}

//ai의 도움을 받은 부분
if (isset($_POST["post_delete"])) {
    $id = $_POST["id"];

    mysqli_query($conn,
    "DELETE FROM comments
    WHERE post_id=$id");

    mysqli_query($conn,
    "DELETE FROM posts
    WHERE id=$id");
}

// 이 부분도 어려워서 ai의 도움을 받았습니다.
$posts = mysqli_query($conn,
"SELECT posts.id,
posts.title,
posts.content,
users.username
FROM posts
JOIN users
ON posts.author_id = users.id
ORDER BY posts.id DESC");
?>

<h1>게시판</h1>

<form method="post">
제목 <input name="title">
내용 <input name="content">

<select name="author_id">
<option value="1">jordan</option>
<option value="2">kobe</option>
<option value="3">lebron</option>
</select>

<button name="post_add">게시글 작성</button>
</form>

<hr>

<?php while($post = mysqli_fetch_assoc($posts)) { ?>

<h3><?php echo $post["title"]; ?></h3>

<p>
<?php echo $post["username"]; ?>
:
<?php echo $post["content"]; ?>
</p>

<form method="post">
<input type="hidden" name="id" value="<?php echo $post['id']; ?>">
제목 <input name="title" value="<?php echo $post['title']; ?>">
내용 <input name="content" value="<?php echo $post['content']; ?>">
<button name="post_edit">게시글 수정</button>
</form>

<form action="comment.php" method="post">
<input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

<select name="author_id">
<option value="1">jordan</option>
<option value="2">kobe</option>
<option value="3">lebron</option>
</select>

<input name="content">
<button name="comment_add">댓글 작성</button>
</form>

<?php
// 이 부분도 같이 도움을 받았습니다.
$comments = mysqli_query($conn,
"SELECT comments.id,
comments.content,
users.username
FROM comments
JOIN users
ON comments.author_id = users.id
WHERE comments.post_id = {$post['id']}");

while($comment = mysqli_fetch_assoc($comments)) {
?>

<p>
ㄴ <?php echo $comment["username"]; ?>
:
<?php echo $comment["content"]; ?>
</p>

<form action="comment.php" method="post">
<input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
<input name="content" value="<?php echo $comment['content']; ?>">
<button name="comment_edit">댓글 수정</button>
</form>

<form action="comment.php" method="post">
<input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
<button name="comment_delete">댓글 삭제</button>
</form>

<?php } ?>

<form method="post">

<input type="hidden"
name="id"
value="<?php echo $post['id']; ?>">

<button name="post_delete">
게시글 삭제
</button>

</form>

<hr>

<?php } ?>
