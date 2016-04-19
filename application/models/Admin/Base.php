<?php
/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/4/19
 * Time: 21:24
 */
class Admin_BaseModel{
    protected $request = NULL; // Request
    protected $session = NULL; // Session
    protected $db = NULL; //


    public function __construct($domain = NULL)
    {

        $this->db = Database::instance();

        if (!$this->session){
            $this->session = Yaf_Session::getInstance();
            $this->session->start();
        }
    }
}