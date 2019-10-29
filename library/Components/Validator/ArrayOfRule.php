<?php

namespace UPTools\Components\Validator;

class ArrayOfRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_array($value) or empty($parameters)){
            return false;
        }

        $check = false;

        foreach ($value as $item){
            foreach ($parameters as $parameter){
                switch ($parameter){
                    case 'array': {
                        $check = is_array($item);
                        break;
                    }
                    case 'string': {
                        $check = is_string($item);
                        break;
                    }
                    case 'integer': {
                        $check = filter_var($item, FILTER_VALIDATE_INT) !== false;
                        break;
                    }
                    case 'float': {
                        $check = filter_var($item, FILTER_VALIDATE_FLOAT) !== false;
                        break;
                    }
                    case 'boolean': {
                        $check = in_array($item, [true,false,1,0,'1','0'], true);
                        break;
                    }
                    case 'numeric': {
                        $check = is_numeric($item);
                        break;
                    }
                }

                if ($check === true) {
                    break;
                }
            }

            if ($check === false) {
                return false;
            }
        }

        return true;
    }

    protected function message(): string
    {
        return 'The values of the #attribute# attribute must contain the specified value types #parameters#';
    }
}