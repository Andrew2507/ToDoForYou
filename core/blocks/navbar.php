<header class="navbar">
    <div class="left">
        <a href="/" class="name">
            <div class="logo"></div>
            <div class="name">ToDoForYou</div>
        </a>
        <div class="nav-buttons">
            <?
            if (isAuth()) echo '<a href="/todo.php" class="nav-button">ToDo</a>';
            ?>

            <a href="/price.php" class="nav-button active">Тарифы</a>
        </div>
    </div>

    <?
    if (isAuth()) {
        $href = "/todo.php";
        $text = $_SESSION['username'];
    } else {
        $href = "/auth.php";
        $text = "Личный кабинет";
    }
    ?>
    <a href="<?= $href ?>" class="auth">
        <div class="login"><?= $text ?></div>
    </a>
</header>