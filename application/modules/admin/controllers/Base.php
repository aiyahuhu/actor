<?php
/** 
* @author: hush
* @date: 2016/5/3-12:48
* @version: $Id:Base.php
**/
class BaseController extends Yaf_Controller_Abstract{

    protected $session = NULL; // Session



    public function init()
    {

        if (!$this->session){
            $this->session = Yaf_Session::getInstance();
            $this->session->start();
        }
        $loginuid = $this->session->get('adminloginid');
        if( $loginuid<=0){
            $this->redirect('/admin/login');
        }
    }

}