<?php

// db.php 파일을 불러와 DB 연결 사용하기
include "db.php";

// 게시글 작성 버튼을 눌렀는지 확인하기
if (isset($_POST["post_add"])) {

    // 게시글 작성 폼에서 입력한 값 가져오기
    $title = $_POST["title"];
    $content = $_POST["content"];
    $author_id = $_POST["author_id"];

    // posts 테이블에 게시글 저장하기
    mysqli_query($conn,
    "INSERT INTO posts(title, content, author_id)
    VALUES('$title', '$content', $author_id)");
}

// 게시글 수정 버튼을 눌렀는지 확인하기
if (isset($_POST["post_edit"])) {

    // 수정할 게시글 번호와 수정 내용 가져오기
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    // 해당 게시글 수정하기
    mysqli_query($conn,
    "UPDATE posts
    SET title='$title', content='$content'
    WHERE id=$id");
}

//ai의 도움을 받은 부분
if (isset($_POST["post_delete"])) {

    // 삭제할 게시글 번호 가져오기
    $id = $_POST["id"];

    // 게시글 삭제 전에 해당 게시글의 댓글 먼저 삭제하기
    // 댓글이 게시글 번호(post_id)를 참조하고 있기 때문에
    mysqli_query($conn,
    "DELETE FROM comments
    WHERE post_id=$id");

    // 해당 게시글 삭제하기
    mysqli_query($conn,
    "DELETE FROM posts
    WHERE id=$id");
}

// 이 부분도 어려워서 ai의 도움을 받았습니다.
// posts 테이블과 users 테이블을 JOIN해서
// 게시글 정보와 작성자 이름을 함께 가져오는 부분
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

<!-- 게시글 작성 폼 -->
<form method="post">

<!-- 제목 입력 칸 -->
제목 <input name="title">

<!-- 내용 입력 칸 -->
내용 <input name="content">

<!-- 작성자 선택 -->
<select name="author_id">
<option value="1">jordan</option>
<option value="2">kobe</option>
<option value="3">lebron</option>
</select>

<!-- 게시글 작성 버튼 -->
<button name="post_add">
게시글 작성
</button>

</form>

<hr>

<?php

// 게시글 목록 반복 출력
while($post = mysqli_fetch_assoc($posts)) {

?>

<!-- 게시글 제목 출력 -->
<h3>
<?php echo $post["title"]; ?>
</h3>

<!-- 게시글 작성자와 내용 출력 -->
<p>
<?php echo $post["username"]; ?>
:
<?php echo $post["content"]; ?>
</p>

<!-- 게시글 수정 폼 -->
<form method="post">

<!-- 수정할 게시글 번호 숨겨서 전달 -->
<input type="hidden"
name="id"
value="<?php echo $post['id']; ?>">

<!-- 수정할 제목 입력 -->
제목
<input name="title"
value="<?php echo $post['title']; ?>">

<!-- 수정할 내용 입력 -->
내용
<input name="content"
value="<?php echo $post['content']; ?>">

<!-- 게시글 수정 버튼 -->
<button name="post_edit">
게시글 수정
</button>

</form>

<!-- 댓글 작성 폼 -->
<form action="comment.php" method="post">

<!-- 현재 게시글 번호 전달 -->
<input type="hidden"
name="post_id"
value="<?php echo $post['id']; ?>">

<!-- 댓글 작성자 선택 -->
<select name="author_id">
<option value="1">jordan</option>
<option value="2">kobe</option>
<option value="3">lebron</option>
</select>

<!-- 댓글 입력 칸 -->
<input name="content">

<!-- 댓글 작성 버튼 -->
<button name="comment_add">
댓글 작성
</button>

</form>

<?php

// 이 부분도 같이 도움을 받았습니다.
// 현재 게시글 번호와 같은 댓글만 가져오는 부분
$comments = mysqli_query($conn,
"SELECT comments.id,
comments.content,
users.username
FROM comments
JOIN users
ON comments.author_id = users.id
WHERE comments.post_id = {$post['id']}");

// 댓글 목록 반복 출력
while($comment = mysqli_fetch_assoc($comments)) {

?>

<!-- 댓글 작성자와 내용 출력 -->
<p>
ㄴ <?php echo $comment["username"]; ?>
:
<?php echo $comment["content"]; ?>
</p>

<!-- 댓글 수정 폼 -->
<form action="comment.php" method="post">

<!-- 수정할 댓글 번호 전달 -->
<input type="hidden"
name="id"
value="<?php echo $comment['id']; ?>">

<!-- 수정할 댓글 내용 입력 -->
<input name="content"
value="<?php echo $comment['content']; ?>">

<!-- 댓글 수정 버튼 -->
<button name="comment_edit">
댓글 수정
</button>

</form>

<!-- 댓글 삭제 폼 -->
<form action="comment.php" method="post">

<!-- 삭제할 댓글 번호 전달 -->
<input type="hidden"
name="id"
value="<?php echo $comment['id']; ?>">

<!-- 댓글 삭제 버튼 -->
<button name="comment_delete">
댓글 삭제
</button>

</form>

<?php } ?>

<!-- 게시글 삭제 폼 -->
<form method="post">

<!-- 삭제할 게시글 번호 전달 -->
<input type="hidden"
name="id"
value="<?php echo $post['id']; ?>">

<!-- 게시글 삭제 버튼 -->
<button name="post_delete">
게시글 삭제
</button>

</form>

<hr>

<?php } ?>
