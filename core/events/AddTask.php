<?php
require_once("./core/api/GetTag.php");
require_once ("../core.php");
function CreateNewTask($userID, $task_name, $text, $category, $tag, $status)
    {
        try {
            $pdo = getCore()->getConn();

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO tasks (UserID, TaskName, TaskText, Category, Tag, TaskStatus) VALUES (:userID, :name, :text, :category, :tag, :status)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->bindParam(':name', $task_name, PDO::PARAM_STR);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);

            $stmt->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

$config = getCore()->getConfig();

$userID = 1;
$task_name = "Penis"; // вводит пользователь
$text = "penis";  // вводит пользователь
$category = "penis";  // определяет пользователь
$tag = getTag($config);
$status = 1;  //  Определяет пользователь (изначально не выполнено = 1)

CreateNewTask($userID, $task_name, $text, $category, $tag, $status);

?>