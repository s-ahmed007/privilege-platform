<?php

namespace Efemer\Higg\Factory\Traits;

use Validator;

trait FormHandlerTrait {

    public $formsProperty = 'forms';

    function getFormConfig($formName){
        $forms = property_exists($this, $this->formsProperty) ? $this->{$this->formsProperty} : [];
        return array_get($forms, $formName);
    }

    function isValid($rules, $data){
        if (!empty($rules)) {
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $messages = [];
                foreach ($errors->all() as $message) {
                    $messages[] = $message;
                }
                $messages = implode("\n", $messages);
                $this->error($messages);
                return false;
            }
        }
        return true;
    }


} // end