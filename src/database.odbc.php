<?php
/**
 * This is the ODBC database driver
 * @package evofw
 * @version 1.0
 */

class Database
{
    private static $instance = array();
    private static $db=null;
    public static $name=null;
    public static $results=null;

    public static function Init($name='default')
    {
        if(self::$instance[$name]==null){
            self::$instance[$name]=new self;
        }
        $dbc=Config::get('database');
        $dbc=$dbc[$name];
        self::$db=odbc_connect($dbc['dsn'], $dbc['user'], $dbc['pass']);
        self::$name=$name;
        return self::$instance[$name];
    }

    public static function query($sql)
    {
        $osql = $sql;
        $sql=odbc_exec(self::$db, $sql);
        self::$results=array();
        self::$results['sql']=$osql;
        if(odbc_error($sql)) {
            var_dump(odbc_errormsg($sql));
            self::$results['error']=odbc_errormsg($sql);
            return false;
        }
        else {
            $cnt=0;
            while($tmp=odbc_fetch_array($sql)) {
                self::$results[]=$tmp;
                $cnt++;
            }
            self::$results['rows']=$cnt;
            return true;
        }
    }

    public static function insert($table, $valueArray)
    {
        $vals=array();
        $keys=array();
        foreach($valueArray as $key => $val) {
            $keys[]=$key;
            if(gettype($val)!='integer') {
                $tmp=str_replace("'", "''", $val);
                $tmp="'$tmp'";
            }
            else {
                $tmp=$val;
            }
            $vals[]=$tmp;
        }
        $keys=implode('],[',$keys);
        $vals=implode(',',$vals);
        $sql="INSERT INTO $table ([$keys]) VALUES ($vals)";
        $osql = $sql;
        $sql=odbc_exec(self::$db, $sql);
        self::$results=array();
        self::$results['sql']=$osql;
        if(odbc_error($sql)) {
            self::$results['error']=odbc_errormsg($sql);
            return false;
        }
        else {
            self::$results[]=odbc_cursor($sql);
            return true;
        }
    }

    public static function select($table, $fieldArray, $whereArray=null) 
    {
        $where=array();
        if(is_array($fieldArray)) {
            $fields="[".implode('], [', $fieldArray)."]";
        }
        else {
            $fields=$fieldArray;
        }
        if(is_array($whereArray)) {
            foreach($whereArray as $key=>$val) {
                $where[]="$key='$val";
            }
            $where=implode(" AND ", $where);
        }
        else {
            $where=$whereArray;
        }
        $sql="SELECT $fields FROM $table";
        if($where) {
            $sql.=" WHERE $where";
        }
        self::query($sql);

    }

    public static function results()
    {
        return self::$results;
    }

    public static function toArray() 
    {
        $res=self::$results;
        if(empty($res) || $res['rows'] == 0) {
            return null;
        }
        $results=array();
        for($x=0; $x<$res['rows']; $x++) {
            $results[]=$res[$x];
        }
        return $results;
    }
}
?>