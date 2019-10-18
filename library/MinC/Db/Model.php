<?php

class MinC_Db_Model
{

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid agentes property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid agentes property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            foreach ($methods as $methodKey => $method) {
                $metodoTratado = 'set' . strtolower(str_replace(
                        '_',
                        '',
                        $key
                    ));
                if (strtolower($method) == $metodoTratado) {
                    $this->$method($value);
                }
            }
        }
        return $this;
    }

    public function toArray()
    {
        $methods = get_class_methods($this);
        $class_vars = get_class_vars(get_class($this));

        if (empty($class_vars)) {
            $reflection = new \ReflectionClass($this);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
            $class_vars = [];
            foreach ($properties as $value) {
                $class_vars[$value->getName()] = '';
            }
        }

        $array = array();
        foreach ($class_vars as $key => $value) {
            $key = str_replace('_', '', $key);
            $method = 'get' . ucfirst($key);
            if (in_array($method, $methods)) {
                $array[$key] = $this->$method();
            }
        }

        return $array;
    }

}
