<?php
namespace Stack\Pyrite\Router;

abstract class ControllerAbstract implements Controller {
    const SUCCESS = "success";
    const FAILURE = "failure";

    protected $request;
    protected $data = array();

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
        return $this;
    }

    public function getData($key, $defaultValue = null) {
        if ($this->hasData($key)) {
            return $this->data[$key];
        }
        return $defaultValue;
    }

    public function getAllDatas() {
        return $this->data;
    }

    public function setData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function hasData($key) {
        return array_key_exists($key, $this->data);
    }

    public function execute() {
        $ret = $this->executeAction();

        if (null === $ret) {
            return self::SUCCESS;
        }
        else {
            return $ret;
        }
    }

    abstract protected function executeAction();
}