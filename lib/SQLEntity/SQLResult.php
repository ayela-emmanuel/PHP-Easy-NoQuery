<?php
namespace Lib\SQLEntity;


class SQLResult{
    public bool $result;
    public string $message;
    public function __construct(bool $result,string $message)
    {
        $this->result = $result;
        $this->message = $message;
    }
    public function __invoke()
    {
        return $this->result;
    }
}




?>