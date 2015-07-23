<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 23/07/15
 * Time: 10:55 AM
 */
class DAO {
    /** @var $mysqlLink mysqli */
    private static $mysqlLink;
    /** @var $tableName string */
    private $tableName;

    /**
     * @param $tableName
     */
    function __construct($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * @param mysqli $link
     */
    public static function init(mysqli $link) {
        self::$mysqlLink = $link;
    }

    /**
     * @param $tableName
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * @param $query
     * @return bool|mixed
     * @throws Exception
     */
    public function query($query) {
        $result = self::$mysqlLink->query($query);
        if(self::$mysqlLink->error) {
            throw new Exception(self::$mysqlLink->error);
        }
        if(is_bool($result)) {
            return($result);
        } else {
            return($result->fetch_all(MYSQLI_ASSOC));
        }
    }

    /**
     * @param $dataset
     * @return bool
     * @throws Exception
     */
    public function insert($dataset) {
        if(!is_array($dataset)) {
            throw new Exception("Dataset not an array");
        }
        $keys = array_keys($dataset);
        $values = array();
        foreach($keys as $key) {
            $values[] = mysql_real_escape_string($dataset[$key]);
        }
        $values = "'" . implode('\',\'', $values) . "'";

        $columns = implode(', ', $keys);
        $query = "INSERT INTO {$this->tableName} ($columns) VALUES ($values);";
        return((bool)$this->query($query));
    }

}