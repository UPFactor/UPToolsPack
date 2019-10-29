<?php

namespace UPTools\Components\Validator;

class RequiredWithoutAllRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (empty($value)){
            foreach ($parameters as $field){
                $field = $this->getMidValue($field);
                if (!is_null($field)){
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    protected function message(): string
    {
        return '#attribute# attribute is required, if all attributes #parameters# is not set';
    }
}