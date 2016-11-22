<?php
    class ObjectInspector{
        # Returns a map {field->value} ot the given object
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
            # created_at attribute is appended
            $valuesMap["created_at"] = $anObject->getCreatedAt();
            
            return $valuesMap;
        }

        # Returns an array of all field names
        public static function proyect($anObject){
            $proyection = array();
            $class = new ReflectionClass($anObject);
            foreach(self::getClassProperties($class) as $field){
                $proyection[] = $field->getName();
            }
            # Append properties specified in PersistentObject
            $proyection[] = "id";
            $proyection[] = "created_at";
            return $proyection;
        }
        
        # Returns object's field value
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