<?php
session_start();
include "db.php";

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($conn,
    "SELECT * FROM users
    WHERE username='$username'
    AND password='$password'");

    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        echo "<script>alert('로그인 성공'); location.href='index.php';</script>";
    } else {
        echo "<script>alert('로그인 실패');</script>";
    }
}
?>

<h1>로그인</h1>

<form method="post">
아이디 <input name="username">
<br><br>

비밀번호 <input name="password" type="password">
<br><br>

<button name="login">로그인</button>
</form>

<a href="register.php">회원가입</a>
