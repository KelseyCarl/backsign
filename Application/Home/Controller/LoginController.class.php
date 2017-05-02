<?php
namespace Home\Controller;
use Think\Controller\RestController;

class LoginController extends RestController{
    public function login(){
        session_start();
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
//            case 'get':{
//                $sql = "select * from userinfor where token='$token'";
//                $result = $Model->query($sql);
//                if(is_bool($result)){
//                    $this->response(retmsg(-1,"查询失败！"),'json');
//                }else{
//                    $data = array();
//                    $data["subitem"] = array();
//                    //用过foreach循环取数据
////                    foreach($result as $key=>$value){
////                        $temp = array(
////                            "token"=>$value['token'],
////                            "user_phone"=>$value['user_phone'],
////                            "user_name"=>$value['user_name'],
////                             "photo_url"=>$value['photo_url']
////                        );
////                        $data["subitem"][] = $temp;
////                    }
//                    //取得数据表所有字段的值
//                    $data["subitem"] = $result;
//                    $this->response(retmsg(0,$data,"查询成功！"),'json');
//                }
//                break;
//            }
            case 'post':{
                //取得输入的值
//                $data = '{"data":{"username":"3120130905123","password":"123456"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $user = $json["data"]["username"];
                $pswd = $json["data"]["password"];
                $query = "select * from sign_user where user_phone='$user' or user_id='$user'";
                $re = $Model->query($query);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    if($re == null){
                        $this->response(retmsg(-1,null,"该用户不存在！"),'json');
                    }else{
                        $data = array();
                        $data["data"] = array();
                        $pass = $re[0]["user_pass"];
                        $token2 = md5($user);//用户登录之后产生令牌
                        if($pswd != $pass){
                            $this->response(retmsg(-1,null,"密码错误！"),'json');
                        }else{
                            $data["data"] = $re;
                            $time = date("Y-m-d H:i:s");
                            $get_token = "update sign_user set token='$token2' where user_phone='$user' or user_id='$user'";
                            $log_result = $Model->execute($get_token);
                            if(is_bool($log_result)){
                                $this->response(retmsg(-1,null,"登录失败！"),'json');
                            }else{
                                $this->response(retmsg(0,$data,"登录成功！"),'json');
                            }
                        }
                    }
                }
                break;
            }
        }
    }


//    public function verify(){
//        $config = array(
//            'fontSize'=> 19,
//            'length'=>4,
//            'imageH'=>35
//        );
//        $verify = new Verify($config);
//        $verify->entry();
//    }

//    public function check_verify($code,$id=''){
//        $verify = new \Think\Verify();
//        $temp = $verify->check($code,$id);
//        $res['resultcode'] = 0;
//        $res["data"]["flag"] = $temp;
//        $this->ajaxReturn($res,"JSON");
//    }

}