<?php
/**
 * User: sanjeev
 * Date: 1/12/14
 * Time: 2:51 PM
 * Filename: response.php
 * Package: catalystdev
 */

/**
 * Class FoundryAjaxResponse
 */

/*
 * Ajax Response Type Codes
 * err=Error
 * inf=Info
 * alt=Alert
 * scl=Script Call
 * elm=Bind Elements
 * rep=Redirect Page
 * rex=Redirect Parent Page
 * sta=Status
 */

class FoundryAjaxResponse extends JObject
{

    private static $instance;

    private $responseStack=array();

    public function __construct($config=array())
    {

    }

    public static function getInstance($config=array())
    {
        if(!self::$instance instanceof FoundryAjaxResponse)
        {

            self::$instance=new FoundryAjaxResponse($config);
        }
        return self::$instance;

    }

    public function addScriptCall($script='')
    {
        $resp=new stdClass();
        $resp->type='scl';
        $resp->data=array('script'=>$script);
        $resp->error=false;

        $this->responseStack[]=$resp;
    }

    public function addContent($content='')
    {
        $resp=new stdClass();
        $resp->type='cot';
        $app=JFactory::getApplication();
        $callback=$app->input->getCmd('callback','callback');
        $resp->data=array('content'=>$content,'callback'=>$callback);
        $resp->error=false;

        $this->responseStack[]=$resp;
    }

    public function invokeCall($func='',$data)
    {
        $resp=new stdClass();
        $resp->type='inv';
        $resp->data=array('func'=>$func,'data'=>json_encode($data));
        $resp->error=false;

        $this->responseStack[]=$resp;
    }

    public function addAlert($msg)
    {
        $resp=new stdClass();
        $resp->type='alt';
        $resp->data=array('msg'=>$msg);
        $resp->error=false;

        $this->responseStack[]=$resp;

    }
    public function addConfirm($msg,$payload='')
    {
        $resp=new stdClass();
        $resp->type='con';
        $resp->data=array('msg'=>$msg,'payload'=>$payload);
        $resp->error=false;

        $this->responseStack[]=$resp;

    }

    public function addBAlert($msg,$callback='')
    {
        $resp=new stdClass();
        $resp->type='abl';
        $resp->data=array('msg'=>$msg,'callback'=>$callback);

        $resp->error=true;
        $this->responseStack[]=$resp;

    }

    public function addInfo($title='',$msg,$type='danger',$element='')
    {
        $resp=new stdClass();
        $resp->type='inf';
        $resp->data=array('title'=>$title,'type'=>$type,'msg'=>$msg,'element'=>$element);

        $resp->error=true;
        $this->responseStack[]=$resp;

    }

    public function addError($title='',$msg,$code=0,$element='')
    {
        $resp=new stdClass();
        $resp->type='err';
        $resp->data=array('title'=>$title,'code'=>$code,'msg'=>$msg,'element'=>$element);

        $resp->error=true;
        $this->responseStack[]=$resp;

    }

    public function redirect($url='',$msg='',$type='message')
    {
        if(!empty($msg))
        {
            $app=JFactory::getApplication();
            $app->enqueueMessage($msg,$type);

        }
        $resp=new stdClass();
        $resp->type='rep';
        $resp->data=array('url'=>$url);
        $resp->error=false;
        $this->responseStack[]=$resp;


    }

    public function update($data=array(),$mode=FOUNDRY_AJAX_ELEMENTS)
    {
        $resp=new stdClass();
        $resp->type='bin';

        $resp->data=array('elements'=>$data,'mode'=>$mode);
        $resp->error=false;

        $this->responseStack[]=$resp;

    }

    public function modal($title='Modal Title',$content='Modal Content',$actionbar=array(),$type='default')
    {
        $resp=new stdClass();
        $resp->type='mod';

        $resp->data=array('title'=>$title,'body'=>$content,'action'=>$actionbar,'type'=>$type);
        $resp->error=false;

        $this->responseStack[]=$resp;
    }


    public function send()
    {
        if(FoundryAjax::isAjax())
        {
            header('Content-type: application/json');
            echo json_encode($this->responseStack);
        }
        else
        {
            header('Content-type: application/json');
            echo json_encode('Nice Try!!');
        }

    }

}
