<?php
/**
 * Description of ObjectBuilder
 *
 * @author dibriel
 */
class ObjectBuilder {
    public static function build($class_name, $data){
        $instance = new $class_name();
        $class = new ReflectionClass($instance);
        
        foreach($data as $field_name => $value){
            if ($field_name != "id"){
                self::setField($class->getProperty($field_name), $instance, $value);
            }
        }
        
        $instance->setID((int)$data["id"]);
        
        return $instance;
    }

    public static function setValue($object, $field_name, $value){
        $reflection = new ReflectionClass($object);
        self::setField($reflection->getProperty($field_name), $object, $value);
    }

    public static function setField($field, $instance, $value){
        $field->setAccessible(true);
        $field->setValue($instance, is_numeric($value) ? (int)$value : $value);
    }
}

?>