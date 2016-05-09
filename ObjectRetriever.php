<?php
/**
 * Description of ObjectRetriever
 *
 * @author dibriel
 */

require_once dirname(__FILE__).'/database/Connection.php';
require_once dirname(__FILE__).'/ObjectInspector.php';

class ObjectRetriever {
    public static function retrieve($class, $id, $proyection = null){
        $db = Connection::getInstance();
        $anInstance = new $class();
        $proyection = self::normalizeProyection($anInstance, $proyection);
        return $db->find($anInstance->tableName(), $proyection, $id);
    }
    
    public static function retrieveAll($class, $proyection = null){
        $db = Connection::getInstance();
        $anInstance = new $class();
        $proyection = self::normalizeProyection($anInstance, $proyection);
        $idList = $db->all($anInstance->tableName());
        $dataList = array();
        
        foreach($idList as $id){
            $dataList[] = self::retrieve($class, $id, $proyection);
        }
        return $dataList;
    }
    
    public static function retrieveWhere($class, $condition, $proyection = null){
        $db = Connection::getInstance();
        $anInstance = new $class();
        $proyection = self::normalizeProyection($anInstance, $proyection);
        
        return $db->where($anInstance->tableName(), $proyection, $condition);
    }
    
    private static function normalizeProyection($anInstance, $proyection){
        if ($proyection == null){
            $proyection = ObjectInspector::proyect($anInstance);
        } else {
            if (!in_array("id", $proyection)){
                $proyection[] = "id";
            }
        }
        return $proyection;
    }
}

?>
