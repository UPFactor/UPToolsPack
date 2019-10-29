<?php

namespace UPTools\Components\Validator;

class CallableRule extends Rule
{
    protected $callableMessage = '';

    protected function check($value, array $parameters): bool
    {
        if (!is_callable($callable = $parameters[0] ?? null)){
            return false;
        }

        $message = function(string $message){
            $this->callableMessage = $message;
        };

        return $callable($value, $message, $this);
    }

    protected function message(): string
    {
        return $this->callableMessage;
    }
}