<?php

require_once dirname(__FILE__).'/../PersistentDatabase.php';

class Connection implements PersistentDatabase{
    private static $instance;
    private $db_connection;
    
    private function __construct(){
    
        $this->db_connection = new mysqli("localhost", "root", "assignmentgame", "eth_ag");
        if ($this->db_connection->connect_error){
            echo "connection failed: "+$this->db_connection->connect_error;
        } else {
        }
    }
    
    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new Connection();
        }
        return self::$instance;
    }
    
    public function write($table, $data){
        $dataList = $this->listData($data);
        $query = "INSERT INTO $table ($dataList[0]) VALUES ($dataList[1])";
        $success = $this->db_connection->query($query);
        $insertedID = -1;
        if ($success){
            $insertedID = $this->db_connection->insert_id;
        } else {
            echo "Persistency error: Can't insert object '$table' ($query)\n";
            //this value must be interpreted as a failure on writing
            $insertedID = 0;
        }
        
        return (int)$insertedID;
    }
    
    public function delete($table, $id){
        $query = "DELETE FROM $table WHERE id=$id";
        $success = $this->db_connection->query($query);
        if (!$success){
            echo "Persistency error: can't delete object #$id from $table\n";
        }
    }

    public function update($table, $data, $id){
        $dataList = $this->listData($data, true);
        $query = "UPDATE $table SET $dataList[0] WHERE id = $id";
        return $this->db_connection->query($query);
    }
    
    public function find($table, $proyection, $id){
        $proyection_list = $this->listProyection($proyection);
        $query = "SELECT $proyection_list FROM $table WHERE id = $id";
        $result = $this->db_connection->query($query);
        $data = null;
        while ($row = $result->fetch_assoc()){
            $data = $row;
        }
        
        return $data;
    }
    
    public function where($table, $proyection, $condition){
        $proyection_list = $this->listProyection($proyection);
        $query = "SELECT $proyection_list FROM $table WHERE $condition";
        $result = $this->db_connection->query($query);
        if ($result){
            $data = array();
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        } else {
            $data = null;
            echo "Persistent error: can't retreive '$proyection_list' from '$table' where condition '$condition' holds\n";
        }
        return $data;
    }
    
    public function all($table){
        $query = "SELECT id FROM $table";
        $result = $this->db_connection->query($query);
        $data = array();
        while ($row = $result->fetch_assoc()){
            $data[] = $row["id"];
        }
        return $data;
    }
    
    private function listProyection($proyection){
        $first = true;
        $list = "";
        
        foreach($proyection as $value){
            if ($first == false){
                $list .= ", ";
            }
            $list .= $value;
            $first = false;
        }
        
        return $list;
    }
    
    private function listData($anArray, $equals = false){
        $first = true;
        $columns = "";
        $values = "";
        
        foreach($anArray as $a => $v){
            if ($first ==  false){
                $columns .= ", ";
                $values .= ", ";
            }
            $columns .= $a;
            if ($equals){
                $columns .= "=".$this->clasify($v);
            } else {
                $values .= $this->clasify($v);
            }
            $first = false;
        }
        
        return array($columns, $values);
    }
    
    private function clasify($v){
        $res;
        if (is_int($v)){
            $res = "$v";
        } else { 
            $res = "'$v'";
        }
        return $res;
    }
}

?>