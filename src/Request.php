<?php

namespace Pyrite\Stack;

use Pyrite\Stack\Validation\Validator;
use Pyrite\Stack\Validation\ParameterValidator;
use Pyrite\Stack\Validation\ValidationException;
use Pyrite\Stack\Validation\ParameterValidationException;
use Pyrite\Stack\Validation\Parameter\Int;
use Pyrite\Stack\Validation\Parameter\Email;
use Pyrite\Stack\Validation\Parameter\UInt;
use Pyrite\Stack\Validation\Parameter\NotNullOrEmpty;
class Request
{
    
    private $request;

    /**
     * 
     * @var \Pyrite\Stack\Validation\ParameterValidator[]
     */
    private $validators = array();
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
        
        $this->addValidator('int', new Int());
        $this->addValidator('uint', new UInt());
        $this->addValidator('email', new Email());
        $this->addValidator('required', new NotNullOrEmpty());
    }
    
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->request, $name), $args);
    }
    
    /**
     * Adds a validation rule.
     * @param string $validationKey Name of the rule
     * @param ParameterValidator $validator Rule validator.
     */
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
     * @param mixed $default OPTIONAL Default value to return if the parameter is not found.
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
    
    /**
     * Checks whether the current request's method is a POST method.
     * @return boolean
     */
    public function isPost()
    {
        return $this->request->isMethod('POST');
    }
    
    /**
     * Validates current request using a custom validator.
     * @param Validator $validator
     */
    public function validate(Validator $validator)
    {
        $validator->validate($this);
    }
    
    /**
     * Validates a parameter against a validation rule.
     * @param string $name
     * @param string $validationKey
     * @throws \InvalidArgumentException
     * @throws ParameterValidationException
     */
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