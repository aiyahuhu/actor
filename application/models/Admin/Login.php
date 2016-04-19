<?php

/**
 * Created by PhpStorm.
 * User: hush
 * Date: 2016/4/19
 * Time: 21:22
 */
class Admin_LoginModel extends Admin_BaseModel
{


    public function checkLogin($username, $password)
    {


        $password = md5($password);
        $sql = ' SELECT `id` FROM ac_user WHERE loginname=:loginname AND password=:password AND disabled=0 AND groupid=1';
        $query = DB::query(Database::SELECT, $sql);
        $query->parameters(array(
            ':loginname' => $username, ':password' => $password
        ));
        $result = $query->execute()->current();
        if (empty($result)) {
            return 0;
        } else {
            return $result['id'];
        }
    }
    public  function setUserLogin($uid)
    {

        $this->session->set('adminloginid',$uid);
        $userinfo = DB::select()->from('ac_user')->where('id','=',$uid)->execute()->current();
        $this->session->set('adminuserinfo',$userinfo);
        $this->session->set('admingroupid',$userinfo['groupid']);

    }


}