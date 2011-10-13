<?php
/**
* This file contains the Database class to use when connection and querying to the database
* @author  Sean Burke <seanb@eshark.net>
* @version 1.0
* @package Goumada
*/

/**
* Database class to handle database connections and queries
* Example usage:
* Database::query("SELECT * FROM TABLE");
* $res=Database::results();
* @access public
*/
class Database
{
    private static $instance=array();
    private static $db=null;
    public static $name=null;
    public static $results=null;
    /**
    *Initializes the class using the config settings and assigns the name to it
    * @param string $name connection name
    *
    * @access public
    * @return Database the connection instance
    */
    public static function Init($name='default') 
    {
        if (self::$instance[$name]==null) {
            self::$instance[$name]=new self;
        }
        $dbc=Config::get('database');
        $dbc=$dbc[$name];
        self::$db=new mysqli($dbc['host'], $dbc['user'], $dbc['pass'], $dbc['name']);
        self::$name=$name;
        return self::$instance[$name];
    }
    /**
    * Performs a raw query to the database
    * @param string $sql query to run
    *
    * @access public
    * @return bool if query was sucessfull
    */
    public static function query($sql) 
    {
        $osql=$sql;
        $sql=self::$db->query($sql);
        self::$results=array();
        self::$results['sql']=$osql;
        if (self::$db->error) {
            self::$results['error']=self::$db->error;            
            return false;
        } else {
            if ($sql->num_rows > 0) {
                $cnt=0;
                while ($tmp=$sql->fetch_assoc()) {
                    self::$results[]=$tmp;
                    $cnt++;
                }
                self::$results['rows']=$cnt;
                return true;
            } else {
                self::$results['rows']=0;
                return true;
            }
        }
    }
    /** 
    * Function to insert data into the table
    * @param string $table The table to insert data into
    * @param mixed $valueArray contains the values to inser using the key as the name and val as the value
    *
    * @access public
    * @return bool if query was successfull
    */
    public static function insert($table,$valueArray) 
    {
        $vals=array();
        $keys=array();
        foreach ($valueArray as $key=>$val) {
            $keys[]=$key;
            $tmp=self::$db->real_escape_string($val);
            if (gettype($val)!='integer') {
                $tmp=self::escapeString($tmp);
                $tmp="'".$tmp."'";
            }
            $vals[]=$tmp;
        }
        $keys=implode(',', $keys);
        $vals=implode(',', $vals);
        $sql="INSERT INTO $table ($keys) VALUES ($vals)";
        $osql=$sql;
        self::$db->query($sql);
        self::$results=array();
        self::$results[]=$sql;
        self::$results['sql']=$osql;
        if (self::$db->error) {
            self::$results[]=self::$db->error;            
            return false;
        } else {
            self::$results[]=self::$db->insert_id;
            return true;
        }
    }
    public static function update($table, $valueArray,$whereArray=null) 
    {
        $vals=array();
        foreach ($valueArray as $key=>$val) {
            $tmp=$val;
            if (gettype($val)!='integer') {
                $tmp=self::escapeString($tmp);
                $tmp="'".$tmp."'";
            }
            $vals[]="`$key`=$tmp";
        }
        $where=array();
        foreach ($whereArray as $key=>$val) {
            $tmp=$val;
            if (gettype($val)!='integer') {
                $tmp=self::escapeString($tmp);
                $tmp="'".$tmp."'";
            }
            $where[]="`$key`=$tmp";
        }
        $vals=implode(",", $vals);
        $where=implode(" AND ", $where);
        $sql="UPDATE $table SET $vals";
        if ($where) {
            $sql.=" WHERE $where";
        }
        self::$db->query($sql);
        self::$results=array();
        self::$results['sql']=$sql;
        if (self::$db->error) {
            self::$results['error']=self::$db->error;
            return false;
        }
        return true;
    }
    public static function select($table, $fieldArray, $whereArray=null) {
        $where=array();
        if(is_array($fieldArray)) {
            $fields=implode(', ', $fieldArray);
        }
        else {
            $fields=$fieldArray;
        }
        foreach($whereArray as $key=>$val) {
            $where[]="$key='$val'";
        }
        $where=implode(" AND ", $where);
        $sql="SELECT $fields FROM $table";
        if($where) {
            $sql.=" WHERE $where";
        }
        
        self::$results=array();
        self::$results['sql']=$sql;
        $sql=self::$db->query($sql);
        if (self::$db->error) {
            self::$results['error']=self::$db->error;            
            return false;
        } else {
            if ($sql->num_rows > 0) {
                $cnt=0;
                while ($tmp=$sql->fetch_assoc()) {
                    self::$results[]=$tmp;
                    $cnt++;
                }
                self::$results['rows']=$cnt;
                return true;
            } else {
                self::$results['rows']=0;
                return true;
            }
        }
    }
    public static function delete($table, $whereArray=null) {
        $sql="DELETE FROM $table";
        foreach($whereArray as $key=>$val) {
            $where[]="$key='$val'";
        }
        $where=implode(" AND ", $where);
        if($where) {
            $sql.=" WHERE $where";
        }    
        self::$db->query($sql);
        self::$results=array();
        self::$results['sql']=$sql;
        if (self::$db->error) {
            self::$results['error']=self::$db->error;
            return false;
        }
        return true;
    }
    public static function results() 
    {
        return self::$results;
    }
    public static function toArray() {
        $res=self::$results;
        if(empty($res) || $res['rows']==0) {
            return null;
        }
        $results=array();
        for($x=0;$x<$res['rows'];$x++) {
            $results[]=$res[$x];
        }
        return $results;
    }
    public static function escapeString($string) {
        return self::$db->real_escape_string($string);
    }
}
?>
