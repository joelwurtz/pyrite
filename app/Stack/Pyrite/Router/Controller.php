<?php

namespace Stack\Pyrite\Router;

interface Controller {
    function getRequest();
    function setRequest($request);
    function getData($key, $defaultValue = null);
    function getAllDatas();
    function hasData($key);
    function setData($key, $value);
    function execute();
}