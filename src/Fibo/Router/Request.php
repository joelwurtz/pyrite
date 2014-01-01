<?php

namespace Fibo\Router;

use Fibo\Router\Validation\Validator;
use Fibo\Router\Validation\ParameterValidator;
use Fibo\Router\Validation\ValidationException;
use Fibo\Router\Validation\ParameterValidationException;
use Fibo\Router\Validation\Parameter\Int;
use Fibo\Router\Validation\Parameter\Email;
use Fibo\Router\Validation\Parameter\UInt;
use Fibo\Router\Validation\Parameter\NotNullOrEmpty;
class Request
{
    
    private $request;

    /**
     * 
     * @var \Fibo\Router\Validation\ParameterValidator[]
     */
    private $validators = array();
    
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
        
        $this->addValidator('int', new Int());
        $this->addValidator('uint', new UInt());
        $this->addValidator('email', new Email());
        $this->addValidator('required', new NotNullOrEmpty());
    }
    
    public function addValidator($validationKey, ParameterValidator $validator)
    {
        $this->validators[$validationKey] = $validator;
    }
    
    /**
     * Gets all request parameters from post, get, and url.
     * @return multitype:
     */
    public function getAllParams()
    {
        $post = $this->request->request->all();
        $get = $this->request->query->all();
        $url = $this->request->attributes->get('_route_params');
    
        return array_merge($url, $get, $post);
    }
    
    /**
     * Gets a param identified by its name (looking in post, get, and url in that order)
     * and returns its value or the default value if the param does not exist in the request.
     * @param string $name Name of the parameter.
     * @param mixed $default [opt] Default value to return if the parameter is not found.
     * @return mixed|null
     */
    public function getParam($name, $default = null)
    {
        if ($this->request->request->has($name)) {
            return $this->request->request->get($name);
        }
    
        if ($this->request->query->has($name)) {
            return $this->request->query->get($name);
        }
    
        $url = $this->request->attributes->get('_route_params');
        if (array_key_exists($name, $url)) {
            return $url[$name];
        }
    
        return $default;
    }
    
    public function isPost()
    {
        return $this->request->isMethod('POST');
    }
    
    public function validate(Validator $validator)
    {
        $validator->validate($this);
    }
    
    public function validateParam($name, $validationKey)
    {
        if (! isset($this->validators[$validationKey])) {
            throw new \InvalidArgumentException(sprintf('Validation key "%s" is not registered.'));
        }   
        
        if (! $this->validators[$validationKey]->validate($this->getParam($name))) {
            throw new ParameterValidationException($name, $validationKey);
        }
    }
}