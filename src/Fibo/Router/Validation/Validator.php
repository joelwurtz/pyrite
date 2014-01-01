<?php

namespace Fibo\Router\Validation;

use Fibo\Router\Request;

interface Validator
{

    function validate(Request $request);
}