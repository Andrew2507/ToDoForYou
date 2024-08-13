<?php

//загрузка ядра в основой поток

declare(strict_types=1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . '/core/BCore.php');

global $core;

$core = new Core();
$core->init();
$core->startSession();

function getCore(): Core { global $core; return $core; }
