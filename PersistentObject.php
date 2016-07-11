<?php

require_once dirname(__FILE__).'/ObjectInspector.php';
require_once dirname(__FILE__).'/database/Connection.php';
require_once dirname(__FILE__).'/ObjectRetriever.php';
require_once dirname(__FILE__).'/ObjectBuilder.php';

abstract class PersistentObject{
    private $id;
    
    public function __construct($id = -1) {
        $this->id = $id;
    }
    
    public function save(){
        $inspector = new ObjectInspector();
        $db = Connection::getInstance();
        if ($this->id < 0){
            $newID = $db->write($this->tableName(), $inspector->inspectObject($this, false));
            $this->id = $newID;
        } else {
            $db->update($this->tableName(), $inspector->inspectObject($this, false), $this->id);
        }
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
    
    public function delete(){
        $db = Connection::getInstance();
        $db->delete($this->tableName(), $this->getID());
    }

    public function JSON(){
        return json_encode(ObjectInspector::inspectObject($this));
    }
    
    public function getID(){
        return $this->id;
    }
    
    public function setID($id){
        $this->id = $id;
    }

    function __call($method, $params) {
        $field = strtolower(substr($method, 3));
        if (strncasecmp($method, "get", 3) == 0) {
            return ObjectInspector::getValue($this, $field);
        }
        if (strncasecmp($method, "set", 3) == 0) {
            ObjectBuilder::setValue($this, $field, $params[0]);
        }
    }
    
    public abstract function tableName();
}

?>
