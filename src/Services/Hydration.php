<?php

namespace src\Services;

trait Hydration
{


    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $parts = explode('_', $key);
            $Parts = array_map('ucfirst', $parts);
            $setter = "set" . implode('', $Parts);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
    public function __set($name, $value)
    {
        $this->hydrate([$name => $value]);
    }
}
