<?php
/**
 *  HttpClient类库,支持301，302转向跟踪
 * ============================================================================
 * 版权所有 (C) 2009 Dewei<zdw163@hotmail.com>，并保留所有权利。
 * ----------------------------------------------------------------------------
 * This is NOT a freeware, use is subject to license terms
 * ============================================================================
 * 用法：
 *   $header = array(
 *       'Accept' => $_SERVER['HTTP_ACCEPT'],
 *       'Accept-Language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
 *      'DNT' => 1
 *   );
 *   $net->setUserAgent($_SERVER['HTTP_USER_AGENT']);
 *   $net->setHeaders($header);
 *   $net->setReferer('http://www.baidu.com');
 *   $net->connect('http://www.baidu.com');
 *   $net->execute($params);
 *   $body = $net->body();
 */
@set_time_limit(0);

// Http_Client
class Http_Client
{
    // 目标网站无法打开时返回的错误代码
    const ERROR_CONNECT_FAILURE = 600;
    // 自定义 UserAgent 字符串
    private $_user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 BIDUBrowser/6.x Safari/537.31';
    private $_url, $_method, $_timeout;
    private $_host, $_port, $_path, $_query, $_referer;
    private $_header;
    private $_body;
    private $_cookiefile = '';
    private $_cookie = '';

    //自定义头信息
    private $_headers = array();


    // __construct
    public function __construct($url = null, $method = 'GET', $timeout = 30)
    {
        $this->connect($url, $method, $timeout);
    }

    // connect
    public function connect($url = null, $method = 'GET', $timeout = 30)
    {
        $this->_url = $url;
        $this->_method = strtoupper(empty($method) ? 'GET' : $method);
        $this->_timeout = empty($timeout) ? 30 : $timeout;
        if (!empty($url)) {
            $this->parseURL($url);
        }
        return $this;
    }

    // execute
    public function execute($params = array())
    {
        if (function_exists('curl_init')) {
            $this->fetchByCurl($params);
        } elseif (function_exists('fsockopen')) {
            $this->fetchBySocket($params);
        }
        return $this;
    }

    /**
     * curl抓取远程文件
     * @param array|string $params
     * @return bool
     */
    private function fetchByCurl($params)
    {
        $header = null;
        $body = null;
        $QueryStr = null;
        $ch = curl_init($this->_url);
        if (strncmp($this->_url, 'https', 5) === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $opt = array(
            CURLOPT_TIMEOUT => $this->_timeout,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->_user_agent,
            CURLOPT_REFERER => $this->_referer,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_HTTPHEADER => $this->_headers
        );
        if ($this->_cookiefile !== '') {
            $opt[CURLOPT_COOKIEFILE] = $this->_cookiefile;
            $opt[CURLOPT_COOKIEJAR] = $this->_cookiefile;
        }
        curl_setopt_array($ch, $opt);
        if ($this->_cookie !== '') {
            curl_setopt($ch, CURLOPT_COOKIE, $this->_cookie);
        }
        if ($this->_method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            if (is_array($params)) {
                $QueryStr = http_build_query($params);
            } else {
                $QueryStr = $params;
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $QueryStr);
        }
        $fp = curl_exec($ch);
        curl_close($ch);
        if (!$fp) {
            return false;
        }
        $i = 0;
        $length = strlen($fp);
        // 读取 header
        do {
            $header .= substr($fp, $i, 1);
            $i++;
        } while (!preg_match("/\r\n\r\n$/", $header));
        // 遇到跳转，执行跟踪跳转
        if ($this->redirect($header)) {
            return true;
        }
        // 读取内容
        do {
            $body .= substr($fp, $i, 4096);
            $i = $i + 4096;
        } while ($length >= $i);
        unset($fp, $length, $i);
        $this->_header = $header;
        $this->_body = $body;
        return true;
    }


    /**
     * socket抓取远程文件
     * @param array|string $params
     * @return bool
     */
    private function fetchBySocket($params)
    {
        $header = null;
        $body = null;
        $QueryStr = null;
        $fp = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout);
        if (!$fp) {
            return false;
        }
        $SendStr = "{$this->_method} {$this->_path}{$this->_query} HTTP/1.0\r\n";
        $SendStr .= "Host:{$this->_host}:{$this->_port}\r\n";
        $SendStr .= "Referer:{$this->_referer}\r\n";
        $SendStr .= "User-Agent: " . $this->_user_agent . "\r\n";
        if (!empty($this->_headers)) {
            foreach ($this->_headers as $k => $v) {
                $SendStr .= $k . ':' . $v . "\r\n";
            }
        }
        //如果是POST方法，分析参数
        if ($this->_method == 'POST') {
            //判断参数是否是数组，循环出查询字符串
            if (is_array($params)) {
                $QueryStr = http_build_query($params);
            } else {
                $QueryStr = $params;
            }
            $length = strlen($QueryStr);
            $SendStr .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $SendStr .= "Content-Length: {$length}\r\n";
        }
        $SendStr .= "Connection: Close\r\n\r\n";
        if (strlen($QueryStr) > 0) {
            $SendStr .= $QueryStr . "\r\n";
        }
        fputs($fp, $SendStr);
        // 读取 header
        do {
            $header .= fread($fp, 1);
        } while (!preg_match("/\r\n\r\n$/", $header));
        // 遇到跳转，执行跟踪跳转
        if ($this->redirect($header)) {
            return true;
        }
        // 读取内容
        while (!feof($fp)) {
            $body .= fread($fp, 4096);
        }
        fclose($fp);
        $this->_header = $header;
        $this->_body = $body;
        return true;
    }

    // header
    public function header()
    {
        return $this->_header;
    }

    // body
    public function body()
    {
        return $this->_body;
    }

    // status
    public function status($header = null)
    {
        if (empty($header)) {
            $header = $this->_header;
        }
        if (preg_match('#(.+) (\d+) (.+)([\r\n]{0,1})#i', $header, $status)) {
            return (int)$status[2];
        } else {
            return self::ERROR_CONNECT_FAILURE;
        }
    }

    // parseURL
    private function parseURL($url)
    {
        $aUrl = parse_url($url);
        $this->_host = $aUrl['host'];
        $this->_port = empty($aUrl['port']) ? 80 : (int)$aUrl['port'];
        $this->_path = empty($aUrl['path']) ? '/' : (string)$aUrl['path'];
        $this->_query = isset($aUrl['query']) && strlen($aUrl['query']) > 0 ? '?' . $aUrl['query'] : null;
        // $this->_referer = 'http://' . $aUrl['host'];
    }

    /**
     * 获取当前URL
     * @return mixed
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /*
     * 设置http_user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->_user_agent = $user_agent;
    }

    /**
     * 设置COOKIE文件
     * @param string $cookiefile
     */
    public function setCookiefile($cookiefile)
    {
        $this->_cookiefile =  $cookiefile;
    }

    /**
     * 设定HTTP请求中"Cookie: "部分的内容。多个cookie用分号分隔，分号后带一个空格(例如， "fruit=apple; colour=red")。
     * @param string $cookie
     */
    public function setCookie($cookie)
    {
        $this->_cookie = $cookie;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    /**
     * @param mixed $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    // redirect
    private function redirect($header)
    {
        if (in_array($this->status($header), array(301, 302))) {
//            if (preg_match('#Location\:(.+)([\r\n]{0,1})#i', $header, $regs)) {
            if (preg_match('#Location:(.+)[\s]+#i', $header, $regs)) {
                $this->connect(trim($regs[1]), $this->_method, $this->_timeout);
                $this->execute();
                return true;
            }
        } else {
            return false;
        }
    }
}

