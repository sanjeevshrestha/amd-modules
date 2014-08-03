<?php
/**
 * User: sanjeev
 * Date: 1/12/14
 * Time: 2:52 PM
 * Filename: ajax.php
 * Package: catalystdev
 */


define ('FOUNDRY_AJAX_ELEMENTS',0);
define ('FOUNDRY_AJAX_MODAL',1);
define ('FOUNDRY_AJAX_ALERT',2);



class FoundryAjax extends JObject
{
    private static $instance=null;

    private $validApis=null;


    /**
     * @param array $config
     * @return FoundryAjax|null
     */
    public static function getInstance($config=array())
    {
       if(!self::$instance instanceof FoundryAjax)
       {
            self::$instance=new FoundryAjax($config);
       }
        return self::$instance;

    }

    public function __construct($config=array())
    {
      //  set_exception_handler(array($this,'handleError'));

    }

    /**
     * @param $component
     * @param $task
     */
    public function addApi($component,$task)
    {
        $this->validApis[]=array($component,$task);
    }


    /**
     * @param $compnent
     * @param $task
     * @return bool
     */
    public function isValidRequest($compnent,$task)
    {
        $needle=array($compnent,$task);
        if(in_array($needle,$this->validApis))
        {
            return true;
        }

        return false;

    }


    public function throwError()
    {

        throw new FoundryAjaxException(JText::_('LIB_FOUNDRY_AJAX_ERROR_INVALID_REQUEST'),500);
    }


    /***
     * isAjax()
     * Check if The request is ajax
     * @return bool
     */
    public static function isAjax()
    {

        if(self::getHeader('FOUNDRY_AJAX')==='FoundryAjax')
        {
            return true;

        }

        else if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest')
        {
            return true;
        }
        else
        {
            return false;
        }

        //




    }

/***
 * getHeader from Requests
 * @param string $key
 * @return bool
 */
    public static function getHeader($key='')
    {
       if(empty($key)) return false;




        if(isset($_SERVER['HTTP_'.strtoupper($key)]))
        {
            return $_SERVER['HTTP_'.strtoupper($key)];
        }

        return false;

    }

    public function handleError(FoundryAjaxException $e)
    {

       $resp=FoundryAjaxResponse::getInstance();
        $resp->addError('Error!',$e->getMessage(),$e->getCode(),$e->getElement());
        $resp->send();


    }
}