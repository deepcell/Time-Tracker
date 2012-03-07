<?php
/**
 * @author Weldon Sams <wjsams@gmail.com>
 * @version 0.0.1
 * @package database
 *
 * To use this class, you instantiate the object with your database parameters. The database name
 * is optional and can be switched later with the switchdb() method. After executing a query with
 * the query() method, the result is stored in $this->a_result array.
 *
 * Usage:
 * $mydb = new mysqldb("localhost", 3306, "joefoo", "barpass");
 * $mydb->switchdb("bookmarks");
 * $mydb->query("select itemid, url from items");
 * print_r($mydb->a_result);
 *
 * $mydb = Mysqldb::getInstance("localhost", 3306, "joefoo", "barpass", "database [optional]");
 * $a_results = $mydb->query("select album_dir, created_date from metadata");
 * print_r($a_results);
 */
class Mysqldb {
    public $a_result;
    public $q;
    private static $singleton;

    private function __construct () {
        $cfg = Config::getInstance();
        if (isset($cfg->mysqlServer) && isset($cfg->mysqlPort) && isset($cfg->mysqlUsername) && isset($cfg->mysqlPassword)) {
            $this->mysqlConnection = mysql_pconnect("{$cfg->mysqlServer}:{$cfg->mysqlPort}", $cfg->mysqlUsername, $cfg->mysqlPassword);
            if (!$this->mysqlConnection) {
                trigger_error("Could not connect to your MySQL server: mysql://{$this->server}:{$this->port}", E_USER_ERROR);
                return false;
            }
            if (isset($cfg->mysqlDatabase)) {
                $this->switchDatabase($cfg->mysqlDatabase);
            }
            return $this;
        }
        return false;
    }

    public static function getInstance () {
        if (is_null(self::$singleton)) {
            self::$singleton = new Mysqldb();
        }
        return self::$singleton;
    }

    /**
     * This function is questionable for other database software like Oracle, SQLite ...
     * @param string $db Database name.
     * @return mixed Returns false if the database can not be selected, or $this otherwise.
     */
    public function switchDatabase ($database) {
        if (!mysql_select_db($database, $this->mysqlConnection)) {
            trigger_error("Could not connect to your database: {$database}", E_USER_ERROR);
            return false;
        }
        return $this;
    }

    /**
     * Given an sql query this function returns an array, where the first index indicates the row number,
     * and points to an associative array where the index is the column name and the value is the column value.
     * @param string $q SQL query
     * @return mixed Return false if a query could not be performed, or $this otherwise.
     */
    public function query ($q) {
        $this->q = $q;
        $result = mysql_query($q, $this->mysqlConnection);
        if ($result) {
            if (preg_match("/^\s*select/", $q)) {
                $cnt = 0;
                $a_result = array();
                while ($row = mysql_fetch_assoc($result)) {
                    foreach ($row as $k=>$v) {
                        $a_result[$cnt][$k] = $v;
                    }
                    unset($row);
                    $cnt++;
                }
                return $a_result;
            } else {
                return true;
            }
        } else {
            trigger_error("Could not perform MySQL query: {$q}", E_USER_NOTICE);
            return false;
        }
    }

    /**
     * Returns a MySQL database safe string. (Has been escaped)
     * @param string $str SQL query string
     * @return string The escaped SQL string.
     */
    public function esc ($str) {
        $str = mysql_real_escape_string($str, $this->mysqlConnection);
        return $str;
    }

    public function escapeQuery($query) {
        return $this->esc($query);
    }

    public function __destruct () {
        unset($a_result);
    }
}
?>
