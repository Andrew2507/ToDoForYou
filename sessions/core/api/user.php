<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/core.php');
function getSqlResultUser($id) {
    $conn = getCore()->getConn();

    $stmt = $conn->prepare("SELECT * FROM Users WHERE id = ?");
    $stmt->bind_param("s", $id);

    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
    return $result;
}

function isAuth() {
    return isset($_SESSION['id']) && $_SESSION['id'];
}