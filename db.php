<?php

// MariaDB의 board(게시판) 데이터베이스에 연결하는 부분
$conn = mysqli_connect("localhost", "board_user", "1234", "board");

// 만약 DB 연결 실패 시 오류 메시지 출력하기
if (!$conn) {
    die("DB 연결 실패");
}

// 한글이 안 깨지도록 문자 설정하기
mysqli_set_charset($conn, "utf8mb4");

?>
