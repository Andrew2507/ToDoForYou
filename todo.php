<?php
require_once ("./core/core.php");

if (!isAuth()) {
    header("Location: /");
    exit;
}

function CreateNewTask($userID, $task_name, $task_text, $tag, $status) {
    try {
        $mysqli = getCore()->getConn();

        $sql = "INSERT INTO tasks (id, TaskName, TaskText, Tag, TaskStatus) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param("isssi", $userID, $task_name, $task_text, $tag, $status);

        $stmt->execute();

    } catch (mysqli_sql_exception $ignored) {}
}

function getTasksByStatus($status) {
    try {
        $mysqli = getCore()->getConn();

        $sql = "SELECT TaskName, TaskText, Tag, id FROM tasks WHERE TaskStatus = ? AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $id = $_SESSION['id'];
        $stmt->bind_param("ii", $status, $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);

    } catch (mysqli_sql_exception $ignored) {}
    return [];
}

function getTaskStats($userID) {
    try {
        $mysqli = getCore()->getConn();

        $sql = "SELECT COUNT(*) as total FROM tasks WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalTasks = $result->fetch_assoc()['total'];

        if ($totalTasks == 0) {
            return ['done' => 0, 'in_progress' => 0, 'waiting' => 0];
        }

        $sql = "SELECT COUNT(*) as done FROM tasks WHERE TaskStatus = 3 AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $doneTasks = $result->fetch_assoc()['done'];

        $sql = "SELECT COUNT(*) as in_progress FROM tasks WHERE TaskStatus = 2 AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $inProgressTasks = $result->fetch_assoc()['in_progress'];

        $donePercent = ($doneTasks / $totalTasks) * 100;
        $inProgressPercent = ($inProgressTasks / $totalTasks) * 100;
        $waitingPercent = 100 - $donePercent - $inProgressPercent;

        return [
            'done' => round($donePercent),
            'in_progress' => round($inProgressPercent),
            'waiting' => round($waitingPercent)
        ];

    } catch (mysqli_sql_exception $ignored) {}

    return ['done' => 0, 'in_progress' => 0, 'waiting' => 0];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    try {
        $mysqli = getCore()->getConn();
        $sql = "DELETE FROM tasks WHERE TaskText = ? AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $id = $_SESSION['id'];
        $stmt->bind_param("si", $task_id, $id);
        $stmt->execute();

        header("Location: /todo.php");
        exit;
    } catch (mysqli_sql_exception $ignored) {}
} else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_id_done'])) {
    $task_id = $_POST['task_id_done'];

    try {
        $mysqli = getCore()->getConn();

        $sql = "SELECT TaskStatus FROM tasks WHERE TaskText = ? AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $id = $_SESSION['id'];
        $stmt->bind_param("si", $task_id, $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $currentStatus = $task['TaskStatus'];

        $newStatus = $currentStatus;
        if ($currentStatus == 1) {
            $newStatus = 2;
        } else if ($currentStatus == 2) {
            $newStatus = 3;
        }

        $sql = "UPDATE tasks SET TaskStatus = ? WHERE TaskText = ? AND id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isi", $newStatus, $task_id, $id);
        $stmt->execute();

        header("Location: /todo.php");
        exit;
    } catch (mysqli_sql_exception $ignored) {}
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_SESSION['id'];
    $task_name = $_POST['task_name'];
    $task_text = $_POST['task_text'];
    $category = $_POST['category'];
    $tag = getTag(getCore()->getConfig(), $task_text);

    $status = 1;
    if ($category == "2") {
        $status = 2;
    } else if ($category == "3") {
        $status = 3;
    }

    CreateNewTask($userID, $task_name, $task_text, $tag, $status);

    header("Location: /todo.php");
    exit;
}

$stats = getTaskStats($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="core/public/css/core.css">
    <link rel="stylesheet" href="scripts/todo.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>TodoForYou</title>
</head>
<body>
<div class="app">
    <? require_once("./core/blocks/navbar.php") ?>

    <div class="header" style="position: unset; padding: 4% 10%;transform: unset;">
        <div class="left">
            <div class="name">ToDoForYou</div>
            <div class="description">Организуйте свою жизнь здесь и сейчас.</i></div>
        </div>
        <div class="right"></div>
    </div>

    <div class="table">
        <div class="head">
            <div class="text">Tables</div>
            <div class="add">+</div>
        </div>
        <div class="grid">
            <div class="column todo">
                <div class="type">Сделать</div>
                <div class="tasks">
                    <?php
                    $tasks = getTasksByStatus(1);
                    foreach ($tasks as $task) { ?>
                        <div class="task">
                            <div class="flex">
                                <div class="name"><?= htmlspecialchars($task['TaskName'] ?? '') ?></div>
                                <form method="post" class="done-form">
                                    <input type="hidden" name="task_id_done" value="<?= $task['TaskText'] ?>">
                                    <button type="submit" class="remove">+</button>
                                </form>
                            </div>
                            <div class="description"><?= htmlspecialchars($task['TaskText'] ?? '') ?></div>
                            <div class="tags">
                                <div class="tag"><?= htmlspecialchars($task['Tag'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="column programming">
                <div class="type">В процессе</div>
                <div class="tasks">
                    <?php
                    $tasks = getTasksByStatus(2);
                    foreach ($tasks as $task) { ?>
                        <div class="task">
                            <div class="flex">
                                <div class="name"><?= htmlspecialchars($task['TaskName'] ?? '') ?></div>
                                <form method="post" class="done-form">
                                    <input type="hidden" name="task_id_done" value="<?= $task['TaskText'] ?>">
                                    <button type="submit" class="remove">+</button>
                                </form>
                            </div>
                            <div class="description"><?= htmlspecialchars($task['TaskText'] ?? '') ?></div>
                            <div class="tags">
                                <div class="tag"><?= htmlspecialchars($task['Tag'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="column done">
                <div class="type">Готово</div>
                <div class="tasks">
                    <?php
                    $tasks = getTasksByStatus(3);
                    foreach ($tasks as $task) { ?>
                        <div class="task">
                            <div class="flex">
                                <div class="name"><?= htmlspecialchars($task['TaskName'] ?? '') ?></div>
                                <form method="post" class="remove-form">
                                    <input type="hidden" name="task_id" value="<?= $task['TaskText'] ?>">
                                    <button type="submit" class="remove">-</button>
                                </form>
                            </div>
                            <div class="description"><?= htmlspecialchars($task['TaskText'] ?? '') ?></div>
                            <div class="tags">
                                <div class="tag"><?= htmlspecialchars($task['Tag'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" style="display: none;">
        <form method="post" name="modal" class="wrapper">
            <div class="name">Создание задания</div>

            <div class="bottom">
                <div class="left">
                    <div class="flex" style="margin-left: 14.5px;margin-bottom: 5%;">
                        <input class="input" style="width: 92%;" placeholder="Название" name="task_name" type="text" required>
                    </div>
                    <div class="content">
                        <textarea name="task_text" id="desc" style="resize: none;width: 86%;" placeholder="Описание" required></textarea>
                    </div>
                </div>

                <div class="right">
                    <label>
                        <select name="category" required>
                            <option value="1">Сделать</option>
                            <option value="2">В процессе</option>
                            <option value="3">Готово</option>
                        </select>
                    </label>
                </div>
            </div>

            <input class="button-form" value="Подтвердить" name="modal" type="submit">
        </form>
    </div>

    <div class="stat">
        <div class="block">
            <div class="progress-pie-chart" data-percent="<?= $stats['waiting'] ?>">
                <div class="ppc-progress">
                    <div class="ppc-progress-fill"></div>
                </div>
                <div class="ppc-percents">
                    <div class="pcc-percents-wrapper">
                        <span><?= $stats['waiting'] ?>%</span>
                    </div>
                </div>
            </div>
            <div class="text">В ожидании</div>
        </div>

        <div class="block">
            <div class="progress-pie-chart" data-percent="<?= $stats['in_progress'] ?>">
                <div class="ppc-progress">
                    <div class="ppc-progress-fill"></div>
                </div>
                <div class="ppc-percents">
                    <div class="pcc-percents-wrapper">
                        <span><?= $stats['in_progress'] ?>%</span>
                    </div>
                </div>
            </div>
            <div class="text">В работе</div>
        </div>

        <div class="block">
            <div class="progress-pie-chart" data-percent="<?= $stats['done'] ?>">
                <div class="ppc-progress">
                    <div class="ppc-progress-fill"></div>
                </div>
                <div class="ppc-percents">
                    <div class="pcc-percents-wrapper">
                        <span><?= $stats['done'] ?>%</span>
                    </div>
                </div>
            </div>
            <div class="text">Выполнено</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function(){
        $('.progress-pie-chart').each(function() {
            var $ppc = $(this),
                percent = parseInt($ppc.data('percent')),
                deg = 360 * percent / 100;

            if (percent > 50) {
                $ppc.addClass('gt-50');
            }

            $ppc.find('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
            $ppc.find('.ppc-percents span').html(percent + '%');
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.querySelector('.add');
        const modal = document.querySelector('.modal');

        addButton.addEventListener('click', function() {
            modal.style.display = 'block';
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                modal.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            if (!modal.querySelector('.wrapper').contains(event.target) && event.target !== addButton) {
                modal.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
