<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/28
 * Time: 12:48
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig() {
        $config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $config);
    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }
    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        /**
         * 添加配置中的路由
         */
        var_dump(Yaf_Registry::get("config"));
        $router->addConfig(Yaf_Registry::get("config")->routes);
        $route = new Yaf_Route_Simple("m", "c", "a");
        $router->addRoute("name", $route);
    }
}