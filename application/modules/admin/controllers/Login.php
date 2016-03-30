<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/30
 * Time: 20:53
 */
class LoginController extends Yaf_Controller_Abstract {
    public function indexAction() {//默认Action
        echo "admin login";
        exit;
        $this->getView()->assign("content", "Hello login");
    }
}