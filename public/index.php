<?php 
include_once __DIR__."/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../");
$dotenv->load();


use Lib\SQLEntity\DATATYPES;
use Lib\SQLEntity\OPERATOR;
use Lib\SQLEntity\SQLEntity;
use Lib\SQLEntity\SQLDBManager;

class user extends SQLEntity{
    public static $table = "users"; 
}
$e = SQLDBManager::ListTables();
var_dump($e); 
?>