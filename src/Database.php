<?php

namespace Hive;

// database connectivity
use mysqli;
use mysqli_result;

class Database
{
    private mysqli $db;

    public function __construct() {
        $this->db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], $_ENV['DB_PORT']);
    }

    // execute query with result
    public function query(string $query, array $params): ?mysqli_result
    {
        if (!$params)
            return null;

        $paramTypes = "";
        foreach ($params as $param) {
            $paramTypes = $paramTypes . $this->getParamType($param);
        }

        $statement = $this->db->prepare($query);

        if (!empty($paramTypes))
            $statement->bind_param($paramTypes, ...$params);

        $statement->execute($params);
        $result = $statement->get_result();
        if ($result === false) {
            return null;
        }
        return $result;
    }

    // execute query without result
    public function execute(string $query, array $params): void
    {
        $this->query($query, $params);
    }

    // escape string for mysql
    public function escape(string $string): string {
        return mysqli_real_escape_string($this->db, $string);
    }

    // get last insert id
    public function getInsertId(): int {
        return intval($this->db->insert_id);
    }

    protected function getParamType($param): string
    {
        $type = gettype($param);
        if ($type == "integer")
            return "i";
        if ($type == "string")
            return "s";

        return "";
    }
}
