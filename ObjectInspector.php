<?php
    class ObjectInspector{
        public static function inspectObject($anObject, $includeID = true){
            $valuesMap = array();
            $class = new ReflectionClass($anObject);
            $fields = self::getClassProperties($class);
            
            foreach($fields as $field){
                $field->setAccessible(true);
                $valuesMap[$field->getName()] = $field->getValue($anObject);
            }
            if ($includeID){
                $valuesMap["id"] = $anObject->getID();
            }
            
            return $valuesMap;
        }
        
        //returns an array of all field names
        public static function proyect($anObject){
            $proyection = array();
            $class = new ReflectionClass($anObject);
            foreach(self::getClassProperties($class) as $field){
                $proyection[] = $field->getName();
            }
            $proyection[] = "id";
            return $proyection;
        }
        
        private static function getClassProperties($class){
            return $class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
        }
    }
?>