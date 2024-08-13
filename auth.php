<?php
require_once ("./core/core.php");

$config = getCore()->getConfig();

$conn = getCore()->getConn();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    if (isset($_POST['register'])) {
        $sql = "SELECT * FROM Users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $id = rand(1, 10000);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (username, password, id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $login, $hashedPassword, $id);
            if ($stmt->execute()) {
                $_SESSION['id'] = $stmt->insert_id;
                header("Location: /todo.php");
                exit;
            }
        }
    } else {
        $sql = "SELECT * FROM Users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["password"];
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['id'] = $row['id'];
                header("Location: /todo.php");
                exit;
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="core/public/css/core.css">
    <link rel="stylesheet" href="/scripts/auth.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>TodoForYou</title>
</head>
<body>

<div class="app">
    <? require_once("./core/blocks/navbar.php") ?>

    <div class="pre-body">
        <div class="container">
            <div class="forms">
                <div class="form login">
                    <span class="title">Логин</span>

                    <form method="post">
                        <div class="input-field">
                            <input type="text" name="login" placeholder="Enter your login" required>
                            <i class="uil uil-envelope icon"></i>
                        </div>
                        <div class="input-field">
                            <input type="password" name="password" class="password" placeholder="Enter your password" required>
                            <i class="uil uil-lock icon"></i>
                            <i class="uil uil-eye-slash showHidePw"></i>
                        </div>

                        <div class="input-field button">
                            <input type="submit" value="Login">
                        </div>
                    </form>

                    <div class="login-signup">
                    <span class="text">Нет аккаунта?
                        <a href="#" class="text signup-link">Зарегистрироваться</a>
                    </span>
                    </div>
                </div>

                <div class="form signup">
                    <span class="title">Регистрация</span>

                    <form method="post">
                        <input type="hidden" name="register" value="true">
                        <div class="input-field">
                            <input type="text" name="login" placeholder="Enter your login" required>
                            <i class="uil uil-user"></i>
                        </div>
                        <div class="input-field">
                            <input type="password" name="password" class="password" placeholder="Create a password" required>
                            <i class="uil uil-lock icon"></i>
                        </div>

                        <div class="input-field button">
                            <input type="submit" value="Signup">
                        </div>
                    </form>

                    <div class="login-signup">
                    <span class="text">Уже есть аккаунт?
                        <a href="#" class="text login-link">Войти</a>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.querySelector(".container"),
            pwShowHide = document.querySelectorAll(".showHidePw"),
            pwFields = document.querySelectorAll(".password"),
            signUp = document.querySelector(".signup-link"),
            login = document.querySelector(".login-link");

        pwShowHide.forEach(eyeIcon =>{
            eyeIcon.addEventListener("click", ()=>{
                pwFields.forEach(pwField =>{
                    if(pwField.type ==="password"){
                        pwField.type = "text";
                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye-slash", "uil-eye");
                        })
                    }else{
                        pwField.type = "password";
                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye", "uil-eye-slash");
                        })
                    }
                })
            })
        })

        signUp.addEventListener("click", ( )=>{
            container.classList.add("active");
        });
        login.addEventListener("click", ( )=>{
            container.classList.remove("active");
        });
    </script>
</div>

</body>
</html>
