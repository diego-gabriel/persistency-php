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
        
        //return field value
        public static function getValue($anObject, $fieldName){
            $reflectionClass = new ReflectionClass($anObject);
            $field = $reflectionClass->getProperty($fieldName);
            $field->setAccessible(true);
            return $field->getValue($anObject);
        }

        private static function getClassProperties($class){
            return $class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
        }
    }
?>