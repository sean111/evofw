<?php
/**
 * @author Sean Burke
 * @package EVOFW
 * @desc PDO Database driver for EVOFW
 */

//TODO: Check to see if using array_keys and array_values will be faster then using foreach

/**
 * Class Database
 */
class Database
{
    public static $instance = array();
    private static $db = null;
    public static $name = null;
    protected static $results = null;

    /**
     * @param string $name
     * @return mixed Instance of defined name
     * @throws Exception PDO Error
     */
    public static function Init($name='default')
    {
        if (empty(self::$instance[$name])) {
            self::$instance[$name] = new self;
        }
        $dbc = Config::get('database');
        $dbc = $dbc[$name];
        if ($dbc['driver'] == 'sqlite') {
            self::$db = new PDO("sqlite:$dbc[file]");
        }
        else {
            try {
                $port=null;
                if(!empty($dbc['port'])) {
                    $dbc['port'] = ":".$dbc['port'];
                }
                self::$db = new PDO("$dbc[driver]:host=$dbc[host]$port;dbname=$dbc[name]", $dbc['user'], $dbc['pass']);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }
            catch(PDOException $e) {
                throw new Exception("Error: ".$e->getMessage());
            }
        }
        unset($dbc, $port);
        return self::$instance[$name];
    }

    /**
     * @param $sql Query to run
     * @return bool Did query succeed?
     * @throws Error Query not defined error
     */
    public static function query($sql)
    {
        self::$results = null;
        if (empty($sql)) {
            throw new Error("Database::Query => No query defined");
        }
        $results = array();
        try {
            $res = self::$db->query($sql);
            $results = $res->fetchAll();
            $results['rows'] = sizeof($results);
            $results['sql'] = $sql;
            self::$results = $results;
            unset($results, $res);
            return true;
        }
        catch(PDOException $e) {
            $results['error'] = true;
            $results['errormsg'] = $e->getMessage();
            $results['sql'] = $sql;
            self::$results = $results;
            unset($results, $res, $e);
            return false;
        }
    }


    /**
     * @param $table Table to retrieve data from
     * @param $fields Fields to retrieve
     * @param null $where Where clause (Array or String)
     * @return bool Did query succeed?
     * @throws Exception Missing either $table or $fields
     */
    public static function select($table, $fields, $where=null)
    {
        self::$results = null;
        if (empty($fields)) {
               throw new Exception("Database::Select => No table defined");
        }
        if (empty($fields)) {
            throw new Exception("Database::Select => No fields defined");
        }
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        if (is_array($where)) {
            $tmp = array();
            foreach($where as $key=>$val) {
                $tmp[] = "`$key` = '$val'";
            }
            $where = implode(' AND ', $tmp);
            unset($tmp);
        }
        $sql = "SELECT $fields FROM $table";
        if(!empty($where)) {
            $sql.=" WHERE $where";
        }
        unset($fields, $where);
        return self::query($sql);
    }

    /**
     * @param $table Table to insert $values into
     * @param $values Array of values to insert
     * @return bool Did the query succeed?
     * @throws Exception Missing $tables or $values or $values is not an array
     */
    public static function insert($table, $values)
    {
        self::$results = null;
        if (empty($table)) {
            throw new Exception("Database::Insert => No table defined");
        }
        if (empty($values)) {
            throw new Exception("Database::Insert => No values defined");
        }
        if (!is_array($values)) {
            throw new Exception("Database::Insert => values to insert must be an array");
        }
        $keys = array();
        $vals = array();
        foreach ($values as $key => $val) {
            print "$key => $val<br />";
            $keys[] = $key;
            $vals[] = $val;
        }
        $keys = implode("`, `", $keys);
        $vals = implode("', '", $vals);
        $sql = "INSERT INTO $table (`$keys`) VALUES ('$vals')";
        $res = self::query($sql);
        self::$results['insert_id'] = self::$db->lastInsertId();
        unset($keys, $vals, $sql, $table, $values);
        return $res;
    }

    /**
     * @param $table
     * @param $fields
     * @param null $where
     * @return bool
     * @throws Exception
     */
    public static function update($table, $fields, $where=null)
    {
        if (empty($table)) {
            throw new Exception("Database::Update => No table defined");
        }
        if (empty($fields)) {
            throw new Exception("Database::Update => No fields defined");
        }
        if (!is_array($fields)) {
            throw new Exception("Database::Update => fields must be an array");
        }
        $vals = array();
        foreach($fields as $key => $val) {
            $vals[] = "`$key` = '$val'";
        }
        $vals = implode(", ", $vals);
        if (!empty($where)) {
            if (is_array($where)) {
                $tmp = array();
                foreach ($where as $key => $val) {
                    $tmp[] = "`$key` = '$val'";
                }
                $where = implode(' AND ', $tmp);
            }
            $where = " WHERE $where";
        }
        $sql  = "UPDATE $table SET $vals $where";
        unset($tmp, $vals, $key, $val, $table, $fields, $where);
        return self::query($sql);

    }

    /**
     * @param $table
     * @param null $where
     * @return bool
     * @throws Exception
     */
    public static function delete($table, $where = null)
    {
        if (empty($table)) {
            throw new Exception("Database::Delete => No table defined");
        }
        if (!empty($where)) {
            if(is_array($where)) {
                $tmp = array();
                foreach($where as $key => $val) {
                    $tmp[] = "`$key` = '$val'";
                }
                $where = implode(" AND ", $tmp);
                unset($tmp);
            }
            $where = " WHERE $where";
        }
        $sql = "DELETE FROM $table $where";
        return self::query($sql);
    }

    /**
     * @return Array Returns query results
     */
    public static function results()
    {
        return self::$results;
    }
}