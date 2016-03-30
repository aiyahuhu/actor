<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 公用类
 *
 * @author: ZDW
 * @date: 2013-04-01
 * @update:
 * @version:    $Id: Utility.php 4626 2016-03-09 06:03:19Z liukaida $
 */
class Utility
{
    /**
     *   生成JSON格式的正确消息
     *
     * @access  public
     * @param
     * @return  void
     */
    public static function jsonResult($content, $message = '', $append = array())
    {
        self::jsonResponse($content, 0, $message, $append);
    }

    /**
     * 创建一个JSON格式的错误信息
     *
     * @access  public
     * @param   string $msg
     * @return  void
     */
    public static function jsonError($msg)
    {
        self::jsonResponse('', 1, $msg);
    }

    /**
     * 创建一个JSON格式的数据
     *
     * @access  public
     * @param   string $content
     * @param   integer $error
     * @param   string $message
     * @param   array $append
     * @return  void
     */
    private static function jsonResponse($content = '', $error = "0", $message = '', $append = array())
    {

        $res = array('error' => $error, 'message' => $message, 'content' => $content);
        if (!empty($append)) {
            foreach ($append AS $key => $val) {
                $res[$key] = $val;
            }
        }
        $val = json_encode($res);
        exit($val);
    }

    /**
     *  API接口：生成JSON格式的正确消息
     * @param string $data 数据
     * @param string $msg 提示消息
     * @param array $append
     */
    public static function apiJsonResult($data, $msg = '', $append = array())
    {
        self::apiJsonResponse($data, '200', $msg, $append);
    }

    /**
     *  API接口：创建一个JSON格式的错误信息
     * @param string $error 错误代码
     * @param string $msg 提示消息
     */
    public static function apiJsonError($error, $msg)
    {
        self::apiJsonResponse('', $error, $msg);
    }

    /**
     * 创建一个JSON格式的数据
     *
     * @access  public
     * @param   string $data
     * @param   integer $error
     * @param   string $msg
     * @return  void
     */
    private static function apiJsonResponse($data = '', $error = '200', $msg = '', $append = array())
    {

        $res = array('error' => $error, 'msg' => $msg, 'data' => $data);
        if (!empty($append)) {
            foreach ($append AS $key => $val) {
                $res[$key] = $val;
            }
        }
        $val = json_encode($res);
        exit($val);
    }

    /**
     *   生成JSONP格式的正确消息
     *
     * @access  public
     * @param
     * @return  void
     */
    public static function jsonpResult($jsonpcallback, $content, $message = '', $append = array())
    {
        self::jsonpResponse($jsonpcallback, $content, 0, $message, $append);
    }

    /**
     * 创建一个JSON格式的错误信息
     *
     * @access  public
     * @param   string $msg
     * @return  void
     */
    public static function jsonpError($jsonpcallback, $msg)
    {
        self::jsonpResponse($jsonpcallback, '', 1, $msg);
    }

    /**
     * 创建一个JSONP格式的数据
     *
     * @access  public
     * @param   string $content
     * @param   integer $error
     * @param   string $message
     * @param   array $append
     * @return  void
     */
    private static function jsonpResponse($jsonpcallback, $content = '', $error = "0", $message = '', $append = array())
    {

        $res = array('error' => $error, 'message' => $message, 'content' => $content);
        if (!empty($append)) {
            foreach ($append AS $key => $val) {
                $res[$key] = $val;
            }
        }
        $val = $jsonpcallback . '(' . json_encode($res) . ')';
        exit($val);
    }


    /**
     * javascript escape php 实现
     * @param $string           the sting want to be escaped
     * @param $in_encoding
     * @param $out_encoding
     */
    public static function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2')
    {
        $return = '';
        if (function_exists('mb_get_info')) {
            for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
                $str = mb_substr($string, $x, 1, $in_encoding);
                if (strlen($str) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
                } else {
                    $return .= '%' . strtoupper(bin2hex($str));
                }
            }
        }
        return $return;
    }

    /**
     * javascript unescape php 实现
     * @param $str
     * @return string
     */
    public static function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else
                    if ($val < 0x800)
                        $ret .= chr(0xc0 | ($val >> 6)) .
                            chr(0x80 | ($val & 0x3f));
                    else
                        $ret .= chr(0xe0 | ($val >> 12)) .
                            chr(0x80 | (($val >> 6) & 0x3f)) .
                            chr(0x80 | ($val & 0x3f));
                $i += 5;
            } else
                if ($str[$i] == '%') {
                    $ret .= urldecode(substr($str, $i, 3));
                    $i += 2;
                } else
                    $ret .= $str[$i];
        }
        return $ret;
    }


    /**
     * @param array $filter
     * @return bool
     */
    public static function buildPageSize(array &$filter)
    {
        if (!isset($filter['record_count']))
            return FALSE;
        if (Cookie::get('page_size') !== NULL) {
            $filter['page_size'] = intval(Cookie::get('page_size'));
        } else {
            $filter['page_size'] = !isset($filter['page_size']) ? 10 : intval($filter['page_size']);
        }
        $filter['page'] = max(1, $filter['page']);
        $filter['record_count'] = intval($filter['record_count']);
        $filter['page_count'] = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
        /* 边界处理 */
        if ($filter['page'] > $filter['page_count']) {
            $filter['page'] = $filter['page_count'];
            $filter['record_count'] = 0;
        }
        $filter['offset'] = ($filter['page'] - 1) * $filter['page_size'];
    }
    /**
     * 分页大小
     * @access  public
     * @return  array
     */
    public static function pageAndSize(&$filter)
    {
        /* 每页显示 */
        $page_size = Arr::get($_GET, 'pgsize');
        if ($page_size > 0) {
            $filter['page_size'] = $page_size;
        } elseif (Cookie::get('page_size') !== NULL) {
            $filter['page_size'] = intval(Cookie::get('page_size'));
        } else {
            $filter['page_size'] = 10;
        }
        /* 当前页 */
        $filter['page'] = max(1, Arr::get($_GET, 'page'));
        $filter['sidx'] = Arr::get($_GET, 'sort_by', '');
        $filter['sord'] = Arr::get($_GET, 'sort_order', 'DESC');
        $filter['sort_by'] = Arr::get($_GET, 'sort_by', '');
        $filter['sort_order'] = Arr::get($_GET, 'sort_order', 'DESC');
        /* page 总数 */
        $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 边界处理 */
        if ($filter['page'] > $filter['page_count']) {
            $filter['page'] = $filter['page_count'];
        }
        $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
        $filter['pagelink'] = self::makePageLink($filter['record_count'], $filter['page_size'],
            $filter['page'], '', $filter['page_count']); // 显示分页
        return $filter;
    }

    /**
     * 创建翻页URL
     * @param $num 总记录数
     * @param $perpage 每页记录数
     * @param $curpage 当前页数
     * @param $mpurl 除页变量外 URL
     * @param int $maxpages 最大页面值
     * @param int $page 一次最多显示几页
     * @return string
     */
    public static function makePageLink($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10)
    {
        $a_name = '';
        if (strpos($mpurl, '#') !== FALSE) {
            $a_strs = explode('#', $mpurl);
            $mpurl = $a_strs[0];
            $a_name = '#' . $a_strs[1];
        }
        if (strpos($mpurl, 'page=') !== FALSE) {
            $mpurl = preg_replace('/([&]?)page=([0-9]*)/', '', $mpurl);
        }
        if (strpos($mpurl, 'pgsize=') !== FALSE) {
            $mpurlvar = preg_replace('/([&]?)pgsize=([0-9]*)/', '', $mpurl);
        } else {
            $mpurlvar = $mpurl;
        }

        $pagevar = 'page=';
        $pagesizevar = 'pgsize=';

        $shownum = TRUE; //是否显示总记录数
        $showkbd = TRUE; //是否显示 <kbd>页数跳转输入框</kbd>
        $showpagejump = TRUE; //是否显示页数跳转输入框

        $dot = '...';
        $mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';
        $mpurlvar .= strpos($mpurlvar, '?') !== FALSE ? '&amp;' : '?';

        $page -= strlen($curpage) - 1;
        if ($page <= 0) {
            $page = 1;
        }
        if ($perpage <= 0 || $perpage >= 1000) {
            $perpage = 10;
        }

//        if ($num > $perpage) {

        $offset = floor($page * 0.5);

        $realpages = @ceil($num / $perpage);
        $curpage = $curpage > $realpages ? $realpages : $curpage;
        $pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;


        if ($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $from + $page - 1;

            if ($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if ($to - $from < $page) {
                    $to = $page;
                }
            } elseif ($to > $pages) {
                $from = $pages - $page + 1;
                $to = $pages;
            }
        }

        $multipage = ($curpage - $offset > 1 && $pages > $page ? '<li><a href="' . $mpurl . $pagevar . '1' . $a_name . '" class="first">1 ' . $dot . '</a></li>' : '') .
            ($curpage > 1 ? '<li><a href="' . $mpurl . $pagevar . ($curpage - 1) . $a_name . '" class="prev">上一页</a></li>' : '');
        for ($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $curpage ? '<li><strong>' . $i . '</strong></li>' :
                '<li><a href="' . $mpurl . $pagevar . $i . $a_name . '">' . $i . '</a></li>';
        }
        $multipage .= ($to < $pages ? '<li><a href="' . $mpurl . $pagevar . $pages . $a_name . '" class="last">' . $dot . ' ' . $realpages . '</a></li>' : '') .

            ($curpage < $pages ? '<li><a href="' . $mpurl . $pagevar . ($curpage + 1) . $a_name . '" class="nxt">下一页</a></li>' : '');

//        $multipage = $multipage ? '<div class="pg">' . ($shownum ? '<em>&nbsp;&nbsp;共&nbsp;' . $num .
//                '&nbsp;条记录</em>' : '') .
//            $multipage . '</div>' : '';
//        }
        return $multipage;
    }


    /**
     * 多币种格式化价格
     *
     * @access  public
     * @param   float $price 价格
     * @param   string $currency 货币名称简写字母（三个大小字母）
     * @return  string
     */
    public static function currency_price_format($price, $currency = 'CNY')
    {
        if ($price === '') {
            $price = 0;
        }
        $code = '';
        switch (strtoupper($currency)) {
            case 'USD': //美元
                $code = 'USD:%s';
                break;
            case 'EUR': //欧元
                $code = 'EUR:%s';
                break;
            case 'GBP': //英磅
                $code = 'GBP:%s';
                break;
            case 'HKD': //港币
                $code = 'HKD:%s';
                break;
            case 'TWD': //台币
                $code = 'TWD:%s';
                break;
            case 'AUD': //澳元
                $code = 'AUD:%s';
                break;
            case 'JPY': //日元
                $code = 'JPY:%s';
                break;
            case 'KRW': //韩元
                $code = 'KRW:%s';
                break;
            case 'CAD': //加拿大元
                $code = 'CAD:%s';
                break;
            case 'MOP': //澳门元
                $code = 'MOP:%s';
                break;
            case 'CNY': //人民币
            default:
                $code = '￥%s';
                break;

        }
//        $price = number_format($price, 2, '.', '');
        $price = intval($price);

        return sprintf($code, $price);
    }


    /**
     *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
     *
     * @access  public
     * @param   string $str 待转换字串
     *
     * @return  string       $str         处理后字串
     */
    public static function makeSemiangle($str)
    {
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
            '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
            '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
            '》' => '>',
            '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
            '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
            '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
            '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
            '　' => ' ');

        return strtr($str, $arr);
    }


    /**
     * 判断IPv4
     * @param $ip
     * @return bool
     */
    public static function is_ipaddress($ip)
    {
        $reg = '^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$';
        return preg_match('#' . $reg . '#isU', $ip, $matches);
    }

    /**
     * 判断utf8
     * @param $val
     * @return bool
     */
    public static function is_utf8($val)
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
[\x09\x0A\x0D\x20-\x7E] # ASCII
| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
)*$%xs', $val);
    }

    /**
     * 36位UUID
     * @return string
     */
    public static function uuid()
    {
        // The field names refer to RFC 4122 section 4.1.2
        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
            mt_rand(0, 65535), // 16 bits for "time_mid"
            mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );
    }

    /**
     * 32位UUID，无-横杠
     * @return string
     */
    public static function guid()
    {
        // The field names refer to RFC 4122 section 4.1.2
        return sprintf('%04x%04x%04x%03x4%04x%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
            mt_rand(0, 65535), // 16 bits for "time_mid"
            mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );
    }


    /**
     * 实时显示数据，不使用缓存?
     * @return bool
     */
    public static function isPreviewNoCache()
    {
        $allow_ip = array('58.240.123.178','221.6.206.26', '221.226.128.154', '127.0.0.1', '58.240.123.179','192.168.21.38','192.168.21.37');
        if (isset($_GET['preview']) && $_GET['preview'] == 'demo' && in_array(IPADDRESS, $allow_ip))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * 去除 文本信息里的class样式及css样式 例如：<style type='css/text'>
     * @param $str
     * @return mixed
     */
    public static function filterStyle($str)
    {
        $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU", '', $str);
        $str = preg_replace('#class=["\']([^"\']*)["\']#i', '', $str);
        //过滤链接
        $str = preg_replace('/<a.*?>(.*?)<\/a>/i', '${1}', $str);
        //内容图片url转换
        $str = preg_replace('#<img([^>]+)>#', '', $str);
        $str = preg_replace('#<p>([\s]+)</p>#', '', $str);
//        $str = preg_replace('/<img(?:.+)src=(\'|\")\/loadimage.php\?id=(\d+)(\'|\")/',
//            '<img class="lazy-load" data-url="http://www.houxue.com/loadimage.php?id=$2"', $str);
        return $str;
    }
    //获得HTML里的文本
    public static function html2text($str)
    {
        $str = preg_replace(
            "/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU", '', $str);
        $str = str_replace(array('<br />', '<br>', '<br/>'), "\n", $str);
        $str = strip_tags($str);
        $str = html_entity_decode($str);
        return $str;
    }
}