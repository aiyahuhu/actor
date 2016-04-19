<?php

/**
 *  错误控制器
 *
 * @author: ZDW
 * @date: ${YEAR}-${MONTH}-${DAY}
 * @version: $Id: Error.php 2883 2016-01-11 09:01:12Z zhudewei $
 */
class ErrorController extends Yaf_Controller_Abstract
{

    public function errorAction($exception)
    {
        /* error occurs */
        switch ($exception->getCode()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
               // echo 404, ":", $exception->getMessage();
                header('HTTP/1.1 404 Not Found', TRUE, 404);
                header('status: 404 Not Found', TRUE, 404);
                break;
            default :
               // $html = Kohana_Exception::handler($exception);
//                echo $html;
                echo $exception->getMessage();
                break;
        }

        return FALSE;
    }
}
