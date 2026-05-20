<?php
include "db.php";

if (isset($_POST["comment_add"])) {
    $post_id = $_POST["post_id"];
    $author_id = $_POST["author_id"];
    $content = $_POST["content"];

    mysqli_query($conn,
    "INSERT INTO comments(post_id, author_id, content)
    VALUES($post_id, $author_id, '$content')");
}

if (isset($_POST["comment_edit"])) {
    $id = $_POST["id"];
    $content = $_POST["content"];

    mysqli_query($conn,
    "UPDATE comments
    SET content='$content'
    WHERE id=$id");
}

if (isset($_POST["comment_delete"])) {
    $id = $_POST["id"];

    mysqli_query($conn,
    "DELETE FROM comments
    WHERE id=$id");
}

header("Location: index.php");
?>
