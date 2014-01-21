<?php

namespace Pyrite\Stack\Validation;

use Pyrite\Stack\Request;

interface Validator
{

    function validate(Request $request);
}