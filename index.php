<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/27
 * Time: 10:32
 */
define("DOCROOT",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
define("SYSPATH",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
define("APPPATH",  DOCROOT.'application/'); /* 指向public的上一级 */
define("LIBRARYPATH",  DOCROOT.'application/library/'); /* 指向public的上一级 */
$app  = new Yaf_Application(DOCROOT . "/conf/application.ini");
$app->bootstrap()->run();