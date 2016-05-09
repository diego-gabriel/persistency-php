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
                $field = $class->getProperty($field_name);
                $field->setAccessible(true);
                $field->setValue($instance, is_numeric($value) ? (int)$value : $value);
            }
        }
        
        $instance->setID((int)$data["id"]);
        
        return $instance;
    }
}

?>