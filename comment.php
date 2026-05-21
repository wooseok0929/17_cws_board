<?php

// db.php 파일을 불러와서 DB 연결 사용
include "db.php";

// 댓글 작성 버튼을 눌렀는지 확인하기
if (isset($_POST["comment_add"])) {

    // 댓글 작성 폼에서 전달된 값 가져오기
    $post_id = $_POST["post_id"];
    $author_id = $_POST["author_id"];
    $content = $_POST["content"];

    // 댓글을 comments 테이블에 저장(조금 헷갈려서 ai의 도움을 받았습니다)
    mysqli_query($conn,
    "INSERT INTO comments(post_id, author_id, content)
    VALUES($post_id, $author_id, '$content')");
}

// 댓글 수정 버튼을 눌렀는지 확인하기
if (isset($_POST["comment_edit"])) {

    // 수정할 댓글 id와 수정 내용 가져오기
    $id = $_POST["id"];
    $content = $_POST["content"];

    // 해당 id의 댓글 내용 수정하기
    mysqli_query($conn,
    "UPDATE comments
    SET content='$content'
    WHERE id=$id");
}

// 댓글 삭제 버튼을 눌렀는지 확인하기
if (isset($_POST["comment_delete"])) {

    // 삭제할 댓글 번호 가져오기
    $id = $_POST["id"];

    // 해당 댓글 삭제하기
    mysqli_query($conn,
    "DELETE FROM comments
    WHERE id=$id");
}

// 댓글 처리 후에 다시 게시판 메인 화면으로 이동
header("Location: index.php");

?>
