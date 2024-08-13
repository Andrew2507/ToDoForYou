<header class="navbar">
    <div class="left">
        <a href="/" class="name">
            <div class="logo"></div>
            <div class="name">ToDoForYou</div>
        </a>
        <div class="nav-buttons">
            <a href="/" class="nav-button active">Главная</a>
            <?
            if (isAuth()) echo '<a href="/todo.php" class="nav-button">ToDo</a>';
            ?>
        </div>
    </div>

    <a href="/auth.php" class="auth">
        <div class="login">Личный кабинет</div>
    </a>
</header>