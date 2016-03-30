<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/27
 * Time: 10:40
 */
class IndexController extends Yaf_Controller_Abstract {
    public function indexAction() {//默认Action
        $this->getView()->assign("content", "Hello admin");
    }
}
?>