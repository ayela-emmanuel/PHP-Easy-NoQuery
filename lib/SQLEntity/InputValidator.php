<?php
namespace Lib\SQLEntity;

class InputValidator{
    public static function HasSpecialChar(string $data):bool{
        return preg_match('/[^a-zA-Z0-9_]+/', $data, $matches);
    }

    public static function ArrHasSpecialChar(array $data):bool{
        foreach ($data as $key => $value) {
            if(preg_match('/[^a-zA-Z0-9_]+/', $value, $matches)){
                return true ;
            }
            if(preg_match('/[^a-zA-Z0-9_]+/', $key, $matches)){
                return true ;
            }
        }
        return false ;
    }
}


?>