<?php
namespace Lib\Database;


function db(){
    $DB_Host = $_ENV["DB_Host"];
    $DB_User = $_ENV["DB_User"];
    $DB_Password = $_ENV["DB_Password"];
    $DB_Database = $_ENV["DB_Database"];

    $conn = new \mysqli($DB_Host,$DB_User,$DB_Password,$DB_Database);
    if($conn->errno){
        die("Database Connection Error!");
    }
    return $conn;
}





?>
