<?php

namespace Stack\Pyrite\Router\Plugin\Output;

class View {
    protected $path = null;
    protected $data = array();

    public function __construct($path, array $data = array()) {
        $this->path = $path;
        $this->data = $data;
    }

    public function __get($key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        else {
            return null;
        }
    }

    public function render() {
        include($this->path);
    }
}