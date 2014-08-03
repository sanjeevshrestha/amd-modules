<?php
/**
 * User: sanjeev
 * Date: 1/13/14
 * Time: 2:24 PM
 * Filename: exception.php
 * Package: catalystdev
 */


class FoundryAjaxException extends RuntimeException
{

   public $element=null;

    public function __construct($message = "", $code = 0, $element='', Exception $previous = null)
    {
        parent::__construct($message,$code,$previous);
        $this->element=$element;
    }

    public function getElement()
    {
        return $this->element;
    }
}