<?php

require_once dirname(__FILE__).'/ObjectInspector.php';
require_once dirname(__FILE__).'/database/Connection.php';
require_once dirname(__FILE__).'/ObjectRetriever.php';
require_once dirname(__FILE__).'/ObjectBuilder.php';
require_once dirname(__FILE__)."/../model/Game.php";
require_once dirname(__FILE__)."/../model/Player.php";
require_once dirname(__FILE__)."/../model/Round.php";

abstract class PersistentObject{
    private $id;
    
    public function __construct($id = -1) {
        $this->id = $id;
    }
    
    public function save(){
        $inspector = new ObjectInspector();
        $db = Connection::getInstance();
        $success = false;
        if ($this->id < 0){
            $success = $newID = $db->write($this->tableName(), $inspector->inspectObject($this, false));
            $this->id = $newID;
        } else {
            $success = $db->update($this->tableName(), $inspector->inspectObject($this, false), $this->id);
        }
        return $success;
    }
    
    public static function find($id, $proyection = null){
        $data = ObjectRetriever::retrieve(get_called_class(), $id, $proyection);
        return $data == null ? null : ObjectBuilder::build(get_called_class(), $data);
    }
    
    public static function all($proyection = null){
        $objects = array();
        
        foreach (ObjectRetriever::retrieveAll(get_called_class(), $proyection) as $data){
            $objects[] = ObjectBuilder::build(get_called_class(), $data);
        }
        
        return $objects;
    }
    
    //$condition must be on SQL form.
    public static function where($condition, $proyection = null){
        $objects = array();
        
        foreach (ObjectRetriever::retrieveWhere(get_called_class(), $condition, $proyection) as $data){
            $objects[] = ObjectBuilder::build(get_called_class(), $data);
        }
        
        return $objects;
    }
    
    //returns first result of where condition, null if no match found
    public static function first($condition, $proyection = null){
        $result = self::where($condition, $proyection);
        return count($result) > 0 ? $result[0] : null;
    }
    //returns first result of where condition, null if no match found
    public static function last($condition, $proyection = null){
        $result = self::where($condition, $proyection);
        $count = count($result);
        return $count > 0 ? $result[$count-1] : null;
    }

    public function delete(){
        $db = Connection::getInstance();
        $db->delete($this->tableName(), $this->getID());
    }

    public function JSON(){
        return json_encode(ObjectInspector::inspectObject($this));
    }

    public function asArray(){
        return ObjectInspector::inspectObject($this);
    }
    
    public function getID(){
        return $this->id;
    }
    
    public function setID($id){
        $this->id = $id;
    }

    function __call($method, $params) {
        $field = $this->toUnderscore(substr($method, 3));
        if (strncasecmp($method, "get", 3) == 0) {
            return ObjectInspector::getValue($this, $field);
        }
        if (strncasecmp($method, "set", 3) == 0) {
            ObjectBuilder::setValue($this, $field, $params[0]);
        }
    }
    
    private function toUnderscore($str){
        $res = "";
        $length = strlen($str);

        if ($length > 0){
            $res .= strtolower($str[0]);

            for($i = 1; $i < $length-1; $i++){
                if (ctype_upper($str[$i])){
                    // ...[A]...
                    if (ctype_lower($str[$i+1])){
                        //...[A]b... -> ...[_a]b...
                        $res .= '_'.strtolower($str[$i]);
                    } else
                    if (ctype_lower($str[$i-1])){
                        //...b[A]... -> ...b[_a]...
                        $res .= '_'.strtolower($str[$i]);
                    } else {
                        //...[A]B... -> ...[a]B...
                        $res .= strtolower($str[$i]);
                    }
                } else {
                    $res .= $str[$i];
                }
            }

            $res .= strtolower($str[$length-1]);
        }
        return $res;
    }

    public abstract function tableName();
}

?>
