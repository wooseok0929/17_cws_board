<?php
$conn = mysqli_connect("localhost", "board_user", "1234", "board");

if (!$conn) {
    die("DB 연결 실패");
}

mysqli_set_charset($conn, "utf8mb4");
?>
