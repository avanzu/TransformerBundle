<?php

namespace Avanzu\TransformerBundle\Transformer;

use Symfony\Component\Form\Form;
/**
 * Description of FormErrorsToArrayTransformer
 *
 * @author avanzu
 */
class FormErrorsToArrayTransformer {
    
    /**
     *
     * @var \Symfony\Component\Translation\Translator 
     */
    protected $translator = null;
    
    protected $errors = array();
    
    public function __construct($translator) {
        $this->errors = array();
        $this->translator = $translator;
    }
    
    public function convert(Form $form, $key = 'global') {
        foreach($form->getErrors() as $error) {
            /*@var $error \Symfony\Component\Form\FormError */

            $this->errors[$key] = is_null($error->getMessagePluralization())  
                ? $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators')
                : $this->translator->transChoice ($error->getMessageTemplate(), $error->getMessagePluralization(), $error->getMessageParameters(), 'validators');
        }
        
        foreach($form as $k => $child) {
                $this->convert($child, (string)$child->getName());
            }
        
        return $this->errors;
    }

}