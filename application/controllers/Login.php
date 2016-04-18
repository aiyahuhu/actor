<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/30
 * Time: 20:53
 */
class LoginController extends BaseController {
    public function indexAction() {//默认Action

        $this->getView()->assign("content", "Hello login");
    }
    public function checkAction()
    {
        Utility::jsonResult('success');

    }
}