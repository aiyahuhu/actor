<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/3/28
 * Time: 12:48
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig() {
        session_set_cookie_params(3600, '/', 'actor.com');
        session_name('session');
        //把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);




        /**
         * 设置默认时区
         *
         * @see  http://php.net/timezones
         */
        date_default_timezone_set('Asia/Shanghai');

        // Load the logger if one doesn't already exist
        if (!Kohana_Exception::$log instanceof Log)
        {
            Kohana_Exception::$log = Log::instance();
            Kohana_Exception::$error_view = LIBRARYPATH . '/Kohana/Error.php';
        }
        /**
         * Attach the file write to logging. Multiple writers are supported.
         */

//        Kohana_Exception::$log->attach(new Log_File(APPPATH.'/logs'));


        define('TIME_FORMAT', 'Y-m-d H:i:s');//时间格式
        define('DATE_FORMAT', 'Y-m-d');//日期格式
        define('TIMENOW', $_SERVER['REQUEST_TIME']); // 当前 Unix 时间戳

        Cookie::$salt = '17ced3';
        Cookie::$domain = 'houxue.com';
        if (DIRECTORY_SEPARATOR !== '\\') {
            Cache::$default = 'memcache'; //内存缓存，若服务器没安装，可注释掉此行
        }

        /*获取客户端 IP 地址*/
        if (isset ($_SERVER ['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
        else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (isset($_SERVER["REMOTE_ADDR"]))
            $ipaddress = $_SERVER["REMOTE_ADDR"];
        else $ipaddress = '0.0.0.0';
        define('IPADDRESS', $ipaddress);

        /*定义如果当前页面的访问是否通过SSL*/
        define('REQ_PROTOCOL', (isset($_SERVER ['HTTPS']) && ($_SERVER ['HTTPS'] == 'on' || $_SERVER ['HTTPS'] == '1') ? 'https' : 'http'));

        // 定义一些有用的内容与相关的环境
        define('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        define('REFERRER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

        /*当前域名*/
        if ($_SERVER ['HTTP_HOST'] or $_ENV ['HTTP_HOST']) {
            $http_host = ($_SERVER ['HTTP_HOST'] ? $_SERVER ['HTTP_HOST'] : $_ENV ['HTTP_HOST']);
        } else if ($_SERVER ['SERVER_NAME'] or $_ENV ['SERVER_NAME']) {
            $http_host = ($_SERVER ['SERVER_NAME'] ? $_SERVER ['SERVER_NAME'] : $_ENV ['SERVER_NAME']);
        }
        define('HTTP_HOST', trim($http_host));
    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }
    public function _initRoute(Yaf_Dispatcher $dispatcher) {

         $router = $dispatcher->getRouter();;
        /**
         * 添加配置中的路由
         */

        $route = new Yaf_Route_Simple("m", "c", "a");
        $router->addRoute("name", $route);

        $router->addRoute('default', new Yaf_Route_Regex('#^/([a-zA-Z]+)-([a-zA-Z]+).html$#',
            array('controller' => ':controller', 'action' => ':action'),
            array(1 => 'controller', 2 => 'action')
        ));
        $router->addRoute('admin', new Yaf_Route_Regex('#^/admin/([a-zA-Z]+)-([a-zA-Z]+)/$#',
            array('module'=>'ADMIN','controller' => ':controller', 'action' => ':action'),
            array(1 => 'controller', 2 => 'action')
        ));

    }
}