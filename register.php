<?php
include "db.php";

if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    mysqli_query($conn,
    "INSERT INTO users(username, password)
    VALUES('$username', '$password')");

    echo "<script>alert('회원가입 완료'); location.href='login.php';</script>";
}
?>

<h1>회원가입</h1>

<form method="post">
아이디 <input name="username">
<br><br>

비밀번호 <input name="password" type="password">
<br><br>

<button name="register">회원가입</button>
</form>

<a href="login.php">로그인</a>
