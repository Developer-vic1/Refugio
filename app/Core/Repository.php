<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
abstract class Repository {
    protected function db(): PDO {return Database::connection();}
    protected function all(string $sql,array $params=[]):array {
       $stmt=$this->db()->prepare($sql);$stmt->execute($params);return $stmt->fetchAll();
    }
    protected function one(string $sql,array $params=[]):array|false {
       $stmt=$this->db()->prepare($sql);$stmt->execute($params);return $stmt->fetch();
    }
}
