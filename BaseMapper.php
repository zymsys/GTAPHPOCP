<?php

class BaseMapper
{
    protected $pdo;
    protected $dbName;
    protected $pkName;

    public function __construct($pdo, $modelName, $dbName, $pkName)
    {
        $this->pdo = $pdo;
        $this->dbName = $dbName;
        $this->pkName = $pkName;
        $this->fields = array_keys(get_class_vars($modelName));
    }

    public function fetch()
    {
        $statement = $this->pdo->prepare("SELECT * FROM `{$this->dbName}`");
        $statement->execute();
        $result = array();
        foreach($statement->fetchAll() as $row)
        {
            $result[] = $this->mapRow($row);
        }
        return $result;
    }

    private function mapRow($row)
    {
        $item = array();
        foreach ($this->fields as $field)
        {
            $item[$field] = $row[$field];
        }
        return $item;
    }
}