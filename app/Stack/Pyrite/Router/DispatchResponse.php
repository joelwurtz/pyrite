<?php

namespace Stack\Pyrite\Router;


interface DispatchResponse {
    function getData($key);
    function getAllDatas();

    function setData($key, $name);
    function setAllDatas(array $datas);

    function unsetData($key);
    function unsetAllDatas();

    function hasData($key);

    function getResult();
    function setResult($result);
}