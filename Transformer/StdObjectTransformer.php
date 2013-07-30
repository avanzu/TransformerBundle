<?php

namespace Avanzu\TransformerBundle\Transformer;

use Doctrine\Common\Annotations\Reader;

/**
 * Description of StdObjectTransformer
 *
 * @author avanzu
 */
class StdObjectTransformer {

    private $reader;
    private $annotationClass = "Avanzu\TransformerBundle\Annotation\StdObject";

    public function __construct(Reader $reader, $annotationClass) {
        $this->reader = $reader;
        $this->annotationClass = $annotationClass;
    }
    public function convert($originalObject) {
        $convertedObject = new \stdClass;

        $reflectionObject = new \ReflectionObject($originalObject);

        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            // fetch the @StandardObject annotation from the annotation reader
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->annotationClass);
            if (null !== $annotation) {
                $propertyName = $annotation->getPropertyName();

                // retrieve the value for the property, by making a call to the method
                $value = $reflectionMethod->invoke($originalObject);

                // try to convert the value to the requested type
                $type = $annotation->getDataType();
                
                if($type == 'datetime') {
                    if($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $convertedObject->$propertyName =  $value;
                }
                
                elseif (false === settype($value, $type)) {
                    throw new \RuntimeException(sprintf('Could not convert value to type "%s"', $value));
                }

                $convertedObject->$propertyName = $value;
            }
        }

        return $convertedObject;
    }
}