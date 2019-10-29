<?php

namespace UPTools\Components\Validator;

class DateFormatRule extends Rule
{
    protected $messageDictionary = [
        'equal' => null,
        'format' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        $this->messageDictionary['format'] = $format = $parameters[0] ?? null;
        $this->messageDictionary['equal'] = $equal = $parameters[1] ?? null;

        if (is_null($format) or ($value = strtotime($value)) === false){
            return false;
        }

        if (is_null($equal)){
            return date_create_from_format($format, $value) !== false;
        }

        return date_create_from_format($format, $value) !== false and $value === strtotime($equal);
    }

    protected function message(): string
    {
        if (is_null($this->messageDictionary['equal'])){
            return 'The #attribute# attribute must contain the correct timestamp in the specified form #format#';
        }

        return 'The #attribute# attribute must contain the correct timestamp in the specified form #format# and equal to #equal#';
    }
}