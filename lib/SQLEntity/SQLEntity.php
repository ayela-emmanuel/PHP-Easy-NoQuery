<?php
namespace Lib\SQLEntity;

use Lib\SQLEntity\InputValidator;

use function Lib\Database\db;


class SQLEntity{
    protected static $table = null;
    
    /**
     * Create row on Database
     * Example:
     * [
     *  ["row"]=>"data",
     *  "username":"johndoe",
     *  "name":"johndoe",
     * ]
     */
    public static function BaseCreate(array $data) : SQLResult {
        return static::Create($data);
    }
    
    /**
     * Create row on Database
     * Example:
     * [
     *  ["row"]=>"data",
     *  "username":"johndoe",
     *  "name":"johndoe",
     * ]
     */
    protected static function Create(array $data): SQLResult{
        try{
            if(InputValidator::ArrHasSpecialChar(array_keys($data))){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $cols = [];
            $colData = [];
            $types = "";
            for ($i=0; $i < count($data); $i++) { 
                array_push($cols,"`".array_keys($data)[$i]."`");
                array_push($colData,"?");
                $types.="s";
            }
            $cols = implode(",",$cols);
            $colData = implode(",",$colData);
            $query = "INSERT INTO `".static::$table."` ($cols) VALUES ($colData)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types,...array_values($data));
            $result = $stmt->execute(); 
            return new SQLResult($result,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    /**
     * Update Column by id
     */
    public static function Update(int $id,string $col,string $data): SQLResult{
        try{
            if(InputValidator::HasSpecialChar($col)){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            $query = "UPDATE `".static::$table."` SET `$col` = ? where `id` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si",$data,$id);
            $result = $stmt->execute(); 
            return new SQLResult($result,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }

    /**
     * Update Column by id
     */
    public static function Delete(int $id): SQLResult{
        try{
            $conn = db();
            $query = "DELETE from `".static::$table."` WHERE `id` = ? ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i",$id);
            $result = $stmt->execute(); 
            return new SQLResult($result,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }
    /**
     * the updateMany updates 1 row multiple colums using an assoc array
     * [col]=>NewValue
     */
    public static function UpdateMany(int $id,array $newData): SQLResult{
        try{
            if(InputValidator::ArrHasSpecialChar(array_keys($newData))){
                return new SQLResult(false,"Unwanted Character Found");
            }
            $conn = db();
            //`$col` = ?
            $updates = [];
            foreach ($newData as $key => $value) {
                # code...
                array_push($updates, "`$key` = ?");
            }
            $updates = implode($updates);

            $query = "UPDATE `".static::$table."` SET $updates where `id` = ?";
            $values = array_values($newData);
            array_push($values,$id);
            $stmt = $conn->prepare($query,...$values);
            $stmt->bind_param("s",$data);
            $result = $stmt->execute(); 
            return new SQLResult($result,$conn->error);
        }catch(\Exception $e){
            return new SQLResult(false,$e->getMessage());
        }
    }
    
    /**
     * 
     */
    public static function ReadOne(int $id,array $cols = ["id"]): array{
        try{
            if(InputValidator::ArrHasSpecialChar(array_keys($cols))){
                return [];
            }
            $conn = db();
            if(count($cols)==0){
                return [];
            }
            $cols = implode(",",$cols);
            $query = "SELECT $cols from `".static::$table."` WHERE `id` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i",$id);
            if(!$stmt->execute()){
                return [];
            }
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }catch(\Exception $e){
            return[];
        }
    }

    /**
     * 
     */
    public static function FindWhere(string $col,OPERATOR $condition,$other,array $cols = ["id"]): array{
        
        if(InputValidator::ArrHasSpecialChar(array_keys($cols))){
            return [];
        }
        if(InputValidator::HasSpecialChar($col)){
            return [];
        }
        $conn = db();
        if(count($cols)==0){
            return [];
        }
        $cols = implode(",",$cols);
        $query = "SELECT $cols from `".static::$table."` WHERE `$col` ".$condition->value." ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$other);
        if(!$stmt->execute()){
            return [];
        }
        $result = $stmt->get_result();
        $rows = [];
        while($row = $result->fetch_assoc()){
            array_push($rows,$row);
        }
        return $rows;
    }

    /**
     * 
     */
    public static function FindWhereBuild(string $col,OPERATOR $condition,$other,array $cols = ["id"]): FindBuilder{
        if(InputValidator::ArrHasSpecialChar(array_keys($cols))){
            return false;
        }
        if(InputValidator::HasSpecialChar($col)){
            return false;
        }
        $conn = db();
        if(count($cols)==0){
            return null;
        }
        $cols = implode(",",$cols);
        $query_starter = "SELECT $cols from `".static::$table."` WHERE `$col` ".$condition->value." ? ";
        return new FindBuilder($query_starter,$other);
    }

    /**
     * 
     */
    public static function All(int $page = 0,array $cols = ["id"],int $perpage = 10): array{
        if(InputValidator::ArrHasSpecialChar(array_keys($cols))){
            return false;
        }
        $conn = db();
        if(count($cols)==0){
            return [];
        }
        $cols = implode(",",$cols);
        $query = "SELECT $cols from `".static::$table."`LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $offset = $page*$perpage;
        $stmt->bind_param("ii",$perpage,$offset);
        if(!$stmt->execute()){
            return [];
        }
        $result = $stmt->get_result();
        $rows = [];
        while($row = $result->fetch_assoc()){
            array_push($rows,$row);
        }
        return $rows;
    }
}

class FindBuilder{
    private string $query;
    private string $types;
    private array $inputs =array();
    public function __construct(string $starterQuery,$starterData,$starterTypes = "s")
    {

        $this->query = $starterQuery;
        $this->types = $starterTypes;
        array_push($this->inputs,$starterData);
    }
    public function AND(string $col, OPERATOR $condition, $data, $type = "s"):FindBuilder{
        $this->query.="AND `$col` ".$condition->value." ? ";
        $this->types.=$type;
        $this->inputs[count($this->inputs)] = $data;
        return $this;
    }
    public function OR(string $col, OPERATOR $condition, $data, $type = "s"):FindBuilder{
        $this->query.="OR `$col` ".$condition->value." ? ";
        $this->types.=$type;
        $this->inputs[count($this->inputs)] = $data;
        return $this;
    }
    public function EXECUTE($page = 0,$perpage = 10): array{
        $conn = db();
        $stmt = $conn->prepare($this->query." LIMIT ? OFFSET ?");
        $offset = $page*$perpage;
        $allData = [];
        array_push($allData,...$this->inputs);
        array_push($allData,$perpage,$offset);
        var_dump($this->inputs);
        $stmt->bind_param($this->types."ii",...$allData);
        if(!$stmt->execute()){
            return [];
        }
        $result = $stmt->get_result();
        $rows = [];
        while($row = $result->fetch_assoc()){
            array_push($rows,$row);
        }
        return $rows;
    }
}

/**
 * 
 */
enum OPERATOR: string {
    case EQUAL = "=";
    case GRATERTHAN = ">";
    case LESSTHAN = "<";
    case GRATEROREQUAL = ">=";
    case LESSOREQUAL = "<=";
    case LIKE = "LIKE";
}

?>


