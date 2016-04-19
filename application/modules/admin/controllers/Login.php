<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/30
 * Time: 20:53
 */
class LoginController extends Yaf_Controller_Abstract {
    public function indexAction() {//默认Action


    }
    public function checkAction()
    {

        $username = trim($this->_request->getPost('username',''));
        $password = trim($this->_request->getPost('password',''));
        if( empty($username) || empty($password)){
            Utility::jsonError('用户名和密码不能为空!');
        }
        $login_mdl = new Admin_LoginModel();
        $uid= $login_mdl->checkLogin($username,$password);
        if( $uid> 0 ){
            $login_mdl->setUserLogin($uid);
            Utility::jsonResult('登录成功!');
        }else{
            Utility::jsonError('用户名或者密码错误!');
        }
    }
}