<?php

namespace UPTools;

use UPTools\Exceptions\ModelException;

/**
 * Class Model
 *
 * @package UPTools
 */
class Model
{
    /**
     * Model properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Property reload.
     * Get property value.
     *
     * @param $name
     * @return mixed
     * @throws ModelException
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * Property reload.
     * Set property value.
     *
     * @param $name
     * @return mixed
     * @param $value
     * @throws ModelException
     */
    public function __set($name, $value)
    {
        $this->setProperty($name, $value);
        return $value;
    }

    /**
     * Property reload.
     * Determines whether a property has a value other than NULL.
     *
     * @param $name
     * @return bool
     * @throws ModelException
     */
    public function __isset($name): bool
    {
        return $this->hasProperty($name);
    }

    /**
     * Conversion to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * Serialization.
     *
     * @return array
     */
    public function __sleep(): array
    {
        return array_keys(get_class_vars(static::class));
    }

    /**
     * Set property value.
     *
     * @param $name
     * @param $value
     * @return static
     * @throws ModelException
     */
    public function setProperty($name, $value)
    {
        $this->propertyExist($name, true);
        if (!method_exists($this, $handler = 'set'.ucfirst($name))){
            throw new ModelException('Property ['. static::class . '::' . $name.'] cannot be set');
        }

        $this->{$handler}($value);
        return $this;
    }

    /**
     * Get property value.
     *
     * @param $name
     * @return mixed
     * @throws ModelException
     */
    public function getProperty($name)
    {
        $this->propertyExist($name, true);
        if (method_exists($this, $handler = 'get'.ucfirst($name))){
            return $this->{$handler}();
        }
        return $this->properties[$name];
    }

    /**
     * Determines whether a property has a value other than NULL
     *
     * @param $name
     * @return bool
     * @throws ModelException
     */
    public function hasProperty($name): bool
    {
        $this->propertyExist($name, true);
        return !is_null($this->properties[$name]);
    }

    /**
     * Checks for the existence of a property.
     *
     * @param string $name
     * @param bool $strict
     * @return bool
     * @throws ModelException
     */
    protected function propertyExist(string $name, bool $strict = false): bool
    {
        if (!array_key_exists($name, $this->properties)){
            if ($strict === true) {
                throw new ModelException('Property [' . static::class. '::' . $name . ']  is missing');
            }
            return false;
        }
        return true;
    }

    /**
     * Converts model properties to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->properties as $key => $value){
            if ($value instanceof Model){
                $result[$key] = $value->toArray();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Converts model properties to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return Arr::toJson($this->properties);
    }

    /**
     * Filling model properties from an array.
     *
     * @param array $properties
     * @return static
     */
    public function fill(array $properties)
    {
        foreach ($properties as $name => $value){
            $this->fillProperty($name, $value);
        }

        return $this;
    }

    /**
     * Filling model property
     *
     * @param string $name
     * @param mixed|null $value
     * @return void
     */
    protected function fillProperty(string $name, $value= null): void
    {
        try {
            $this->setProperty($name, $value);
        } catch (ModelException $exception) {
            return;
        }
    }

    /**
     * Handling the initialization of a class instance.
     *
     * @return void
     */
    protected function init(): void {}
}