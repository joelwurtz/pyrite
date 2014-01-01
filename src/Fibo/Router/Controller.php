<?php
namespace Fibo\Router;

/**
 * Base controller for all application controllers.
 * @author thibaud
 *
 */
abstract class Controller
{

    /**
     * 
     * @var Request
     */
    private $request;
    
    /**
     * 
     * @var Redirect
     */
    private $redirect;
    
    /**
     * 
     * @var mixed[]
     */
    private $data = array();
    
    protected abstract function executeAction();
    
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Returns the current request object.
     * @return \Fibo\Router\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * Sets the request object.
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * Gets a redirect object instance.
     * @return \Fibo\Router\Redirect
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Sets the redirection object instance.
     * @param \Fibo\Router\Redirect $redirect
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }
    
    /**
     * Executes the controller action.
     * @return string Response output.
     */
    public function execute()
    {
    	$this->executeAction();
    }
}