<?php

namespace Stack\Pyrite\Router;

class DispatchResponseImpl implements DispatchResponse {
    protected $data = array();
    protected $result = null;

    public function getData($key, $defaultValue = null) {
        if ($this->hasData($key)) {
            return $this->data[$key];
        }
        else {
            return $defaultValue;
        }
    }

    public function getAllDatas() {
        return $this->data;
    }

    public function setData($key, $name) {
        $this->data[$key] = $name;
        return $this;
    }

    public function setAllDatas(array $datas) {
        $this->data = $datas;
        return $this;
    }

    public function unsetData($key) {
        if ($this->hasData($key)) {
            unset($this->data[$key]);
        }
        return $this;
    }

    public function unsetAllDatas() {
        $this->data = array();
        return $this;
    }

    public function hasData($key) {
        return array_key_exists($key, $this->data);
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
        return $this;
    }
}