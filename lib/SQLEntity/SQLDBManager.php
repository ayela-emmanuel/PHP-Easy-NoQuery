<?php
namespace Lib\SQLEntity;

use Lib\SQLEntity\InputValidator;

use function Lib\Database\db;

class SQLDBManager{

    public static function CreateTable(string $TableName) : SQLResult {
        try{
            if(InputValidator::HasSpecialChar($TableName)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "CREATE TABLE $TableName (id bigint UNSIGNED NOT NULL AUTO_INCREMENT, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,  updated DATETIME , CONSTRAINT PK PRIMARY KEY (id)) ";
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    public static function AddColumn(string $TableName,string $name,DATATYPES $datatype,int $length,string $default = "",bool $nullable = true) : SQLResult {
        try{
            if(InputValidator::arrHasSpecialChar([$TableName,$name,$default])){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "ALTER TABLE $TableName ADD $name ".$datatype->value;

            if($length > 0 ){
                $query.="($length)";
            }
            if(!$nullable){
                $query.=" NOT NULL";
            }else{
                $query.=" NULL";
            }
            if($default!=""){
                if(mb_strtoupper($default, 'utf-8') == $default){
                    $query.=" DEFAULT $default";
                }else{
                    $query.=" DEFAULT '$default'";
                }
            }else{
                $query.=" DEFAULT NULL";

            }
            
            // constraint
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    public static function ChangeColumnDataType(string $TableName,string $col,DATATYPES $datatype,int $length,string $default = "",$nullable = true,$after ="") : SQLResult {
        try{
            if(InputValidator::arrHasSpecialChar([$TableName,$col])){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "ALTER TABLE $TableName CHANGE COLUMN $col $col ".$datatype->value;
            
            if($length > 0 ){
                $query.="($length)";
            }
            if(!$nullable){
                $query.=" NOT NULL";
            }else{
                $query.=" NULL";
            }
            if($default!=""){
                if(mb_strtoupper($default, 'utf-8') == $default){
                    $query.=" DEFAULT $default";
                }else{
                    $query.=" DEFAULT '$default'";
                }
            }else{
                $query.=" DEFAULT NULL";

            }
            if($after){
                $query.=" AFTER $after";
            }
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }


    public static function RenameColumn(string $TableName,string $col,string $NewName) : SQLResult {
        try{
            if(InputValidator::arrHasSpecialChar([$TableName,$col,$NewName])){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "ALTER TABLE $TableName RENAME COLUMN $col to $NewName";
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    public static function RemoveColumn(string $TableName,string $col) : SQLResult {
        try{
            if(InputValidator::HasSpecialChar($TableName)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            if(InputValidator::HasSpecialChar($col)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "ALTER TABLE $TableName DROP COLUMN $col";
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }


    public static function EmptyTable(string $TableName) : SQLResult {
        try{
            if(InputValidator::HasSpecialChar($TableName)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "TRUNCATE TABLE $TableName";
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    public static function DeleteTable(string $TableName) : SQLResult {
        try{
            if(InputValidator::HasSpecialChar($TableName)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "DROP TABLE $TableName";
            $result = $conn->query($query);
            return new SQLResult($result!=false,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    public static function ListTables() : array {
        try{
            $conn = db();
            $query = "SHOW TABLES;";
            $result = $conn->query($query);
            $res = [];
            while ($row = $result->fetch_row()) {
                array_push($res,$row[0]);
            }
            return $res;
        }catch(\Exception $e){
            return [];
        }
    }
}

enum DATATYPES : string{
    case String = "VARCHAR";
    case Text = "TEXT";
    case Blob = "BLOB";
    case Int = "INT";
    case BigInt = "BIGINT";
    case Float = "FLOAT";
    
    case DateTime = "DATETIME";
    case TimeStamp = "TIMESTAMP";
    case Time = "TIME";
    case Date = "DATE";
    case Year = "YEAR";
    // No Len
    case Bool = "BOOL";
    
    
}

?>