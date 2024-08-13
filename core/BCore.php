<?php

class Core {
    function init() {

        global $config;
        global $conn;

        $this->inputClasses();

        $config = getConfig();
        $conn = new mysqli($config['database']['host'], $config['database']['user'], $config['database']['pass'], $config['database']['name']);

        $conn = $this->getConn();

        $sql = "CREATE TABLE IF NOT EXISTS Users (
            id BIGINT PRIMARY KEY,
            username VARCHAR(255),
            password VARCHAR(255)
        );";

        $conn->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id BIGINT,
            UserID BIGINT,
            TaskName VARCHAR(255),
            TaskText VARCHAR(10000),
            Category VARCHAR(255),
            Tag VARCHAR(255),
            TaskStatus int DEFAULT 1
        );";

        $conn->query($sql);
        register_shutdown_function(function() { $this->end(); });
    }

    function end() {
        $conn = getCore()->getConn();
        $conn->close();
    }

    function inputClasses() {
        global $documentRoot;
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];

        $autoloadFiles = [
            '/config.php',
            '/core/api/user.php',
            '/core/api/GetTag.php'
        ];

        foreach ($autoloadFiles as $file) { require_once($documentRoot . $file); }
    }

    function startSession() {
        if (ob_get_level() == 0) ob_start();
        if (ini_get('session.gc_maxlifetime') != 60 * 60 * 24 * 100) { ini_set('session.gc_maxlifetime', (string)(60 * 60 * 24 * 100)); }
        if (ini_get('session.cookie_lifetime') != 60 * 60 * 24 * 100) { ini_set('session.cookie_lifetime', (string)(60 * 60 * 24 * 100)); }
        if (ini_get('session.save_path') != $this->getDocumentRoot() . '/sessions') { ini_set('session.save_path', $this->getDocumentRoot() . '/sessions'); }
        if (ini_get('display_errors') !== "1") { ini_set('display_errors', "1"); }
        if (!isAuth()) session_start();

        if (isAuth()) {
            $result = getSqlResultUser($_SESSION['id']);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['id'] = $row['id'];
            } else die(512);
        }
    }

    function getConn() { global $conn; return $conn; }
    function getConfig() { global $config; return $config; }
    function getDocumentRoot() { global $documentRoot; return $documentRoot; }
}