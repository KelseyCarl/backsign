<?php
namespace Home\Controller;
use Think\Controller\RestController;

class SignController extends RestController{
    public function sign($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "post":{
                $sql = "select user_id from sign_user where token='$token'";
                $re = $Model->query($sql);
                $user = $re[0]["user_id"];
//                var_dump($re);
//                echo $re[0]["user_id"]."  ";
//                $json = '{"data":{"coursename":"移动通信","date":"2017-04-22"}}';
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $coursename = $data["data"]["coursename"];
                $date = $data["data"]["date"];
                $now = date("H:i:s");
//                echo $now."  ";
                $find = "select startTime,endTime,signstate,state from sign_course where user_id='$user' and courseName='$coursename' and date='$date'";
                $result = $Model->query($find);
                $starttime = $result[0]["starttime"];
                $endtime = $result[0]["endtime"];
                $signstate = $result[0]["signstate"];
                $state = $result[0]["state"];
                $data = array();
                if($signstate != null && $state != null){
                    $data["resultcode"] = -1;
                    $this->response(retmsg(-1,$data,"已经签过到了"),'json');
                }else{
                    if(strtotime($now) - strtotime($starttime) > 10*60){
                        $insert = "update sign_course set state='异常',signstate='已签到',signtime='$now' where user_id='$user' and courseName='$coursename' and date='$date'";
                    }else{
                        $insert = "update sign_course set state='正常',signstate='已签到',signtime='$now' where user_id='$user' and courseName='$coursename' and date='$date'";
                    }
                    $result2 = $Model->execute($insert);

                    if(is_bool($result2)){
                        $data["resultcode"] = -1;
                        $this->response(retmsg(-1,$data,"签到失败"),'json');
                    }else{
                        $data["resultcode"] = 0;
                        $this->response(retmsg(0,$data,"签到成功"),'json');
                    }
                }
                break;
            }
        }
    }


    public function quit($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "post":{
                $sql = "select user_id from sign_user where token='$token'";
                $re = $Model->query($sql);
                $user = $re[0]["user_id"];
//                $json = '{"data":{"coursename":"移动通信","date":"2017-04-25"}}';
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $coursename = $data["data"]["coursename"];
                $date = $data["data"]["date"];
                $now = date("H:i:s");
                $find = "select startTime,endTime,quitstate from sign_course where user_id='$user' and courseName='$coursename' and date='$date'";
                $result = $Model->query($find);
                $starttime = $result[0]["starttime"];
                $endtime = $result[0]["endtime"];
//                echo $endtime;
                $quitstate = $result[0]["quitstate"];
                $data = array();
                $data["resultcode"] = -1;
                if($quitstate != null){
                    $this->response(retmsg(-2,$data,"已经签过退了"),'json');
                }else{
                    if(strtotime($quitstate) - strtotime($starttime) > 80*60){
                        $this->response(retmsg(-1,$data,"未满教学时间！"),'json');
                    }else{
                        $data["resultcode"] = 0;
                        $this->response(retmsg(0,$data,"满教学时间"),'json');
                    }
                }
                break;
            }
        }
    }

    public function test($token){
        echo "test".$token;

    }
}