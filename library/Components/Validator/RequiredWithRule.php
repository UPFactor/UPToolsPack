<?php

namespace UPTools\Components\Validator;

class RequiredWithRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (empty($value)){
            foreach ($parameters as $field){
                $field = $this->getMidValue($field);
                if (!is_null($field)){
                    return false;
                }
            }
        }
        return true;
    }

    protected function message(): string
    {
        return '#attribute# attribute is required, if one of the attributes #parameters# is set';
    }
}