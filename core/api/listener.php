<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/core.php');

$conn = getCore()->getConn();
$config = getCore()->getConfig();
$langArray = getLang();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //исполнители post запросов
}