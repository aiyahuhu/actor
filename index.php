<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/27
 * Time: 10:32
 */
define("APP_PATH",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();