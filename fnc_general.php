<?php

require_once "/home/pransten/config.php";

function connect_db() {
    $db_connection = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

    // maaran suhtlemisel kasutatava kooditabeli
    $db_connection->set_charset("utf8");

    return $db_connection;
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>