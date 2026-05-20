<?php
include "db.php";

if (isset($_POST["comment_add"])) {
    $post_id = $_POST["post_id"];
    $author_id = $_POST["author_id"];
    $content = $_POST["content"];

    $sql = "INSERT INTO comments(post_id, author_id, content)
            VALUES($post_id, $author_id, '$content')";

    mysqli_query($conn, $sql);
}

header("Location: index.php");
?>
