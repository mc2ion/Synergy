<?php
class db {

    private $dbhost;
    private $dbname;
    private $dbuser;
    private $dbpass;

    var $host;
    var $conn;
    var $dbconn;
    var $schema;
    
    function __construct($setnames=true) {
       $this->dbhost       = '172.16.16.122';     //Cambiar este dato si la bd esta en otro servidor, por el IP correcto
       $this->port         = "3306";
       $this->dbname       = 'eventosplusdb';         //Poner los datos correctos
       $this->dbuser       = 'root';                  //Poner los datos correctos
       $this->dbpass       = 'r00tp@ss';              //Poner los datos correctos
       $this->schema       = $this->dbname;
       $this->dbFullHost   = $this->dbhost.":".$this->port;

        /* $this->dbhost       = 'localhost';     //Cambiar este dato si la bd esta en otro servidor, por el IP correcto
       //$this->port         = "3306";
       $this->dbname       = 'eventosplusdb';         //Poner los datos correctos
       $this->dbuser       = 'root';                  //Poner los datos correctos
       $this->dbpass       = 'administrator';              //Poner los datos correctos
       $this->schema       = $this->dbname;
       $this->dbFullHost   = $this->dbhost;*/

        $this->conn         = mysql_connect($this->dbFullHost, $this->dbuser, $this->dbpass,true) or die("Error en la conexion de base de datos");
        $this->dbconn       = mysql_select_db($this->dbname, $this->conn) or die('El usuario no tiene permisos suficientes');
    }

    function __destruct() {
        @mysql_close($this->conn);
    }

    function dbQuery($query) {
        $dbTemp = array() ;
        $i      = 0;
        mysql_query("SET NAMES UTF8");
        $q = mysql_query($query, $this->conn);
        if (!$q) {
            $this->displayError();
            return false;
        }
        while ($row = mysql_fetch_array($q, MYSQL_ASSOC)) {
            $i++;
            foreach ($row as $key => $value) {
                $dbTemp[$i][$key] = $value;
            }
        }
        return $dbTemp;
    }


    function dbInsert($tableName, $varPost) {
        global $dbname;
        $fieldList = "";
        $valueList = "";
    
        foreach ($varPost as $key => $value) {
            $fieldList .= ", `$key`";
            $valueList .= ", \"" . addslashes($value) . "\"";
        }
        $fieldList = substr($fieldList, 1);
        $valueList = substr($valueList, 1);
        $query = "Insert Into $tableName ($fieldList) values ($valueList)";

        mysql_query("SET NAMES UTF8");
        if (mysql_query($query, $this->conn)) {
            return mysql_insert_id($this->conn);
        } else {
            $this->displayError();
        }

    }


    function dbUpdate($table, $data, $where) {
        //unset($data[Id], $data[id]);
        $qr = "";
        foreach ($data as $k => $v) {
            $value = $v;
            if ($value == null) {
                 $qr .= "`$k` = NULL ,";  
            }else{
                $value = addslashes($value);
                $qr .= "`$k` = '$value' ,";
            }  
        }

        $qr = substr($qr, 0, -1);
        $query = "Update $table set $qr where $where";
        mysql_query("SET NAMES UTF8");
        return mysql_query($query, $this->conn);
    }

    function cQuery($query) {
        mysql_query("SET NAMES UTF8");
        $q = mysql_query($query, $this->conn);
        return $q;
    }
    
    function clean($input){
        $o = mysql_real_escape_string($input,$this->conn); 
        return $o;
    }
    
    function displayError(){
       echo "Could not run query: $query :" . mysql_error();
    }

}

?>