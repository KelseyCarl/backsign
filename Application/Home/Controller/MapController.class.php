<?php
namespace Home\Controller;
use Think\Controller\RestController;

class MapController extends RestController{
    public function map($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                $sql = "select schoolName from sign_user where token='$token'";
                $re = $Model->query($sql);
                $school = $re[0]["schoolname"];
//                echo $re[0]["schoolname"];
                $sql2 = "select * from sign_location where schoolName='$school'";
                $scl = $Model->query($sql2);
                if(is_bool($scl)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $data = array();
                    $data["data"] = $scl;
                    $this->response(retmsg(0,$scl,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function incourse($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                $today = date("Y-m-d");
                $sql = "select user_id from sign_user where token='$token'";
                $re = $Model->query($sql);
                $userid = $re[0]["user_id"];
                $panduan = "select * from sign_course where date='$today' and  user_id='$userid'";
//                echo $panduan;
                $re2 = $Model->query($panduan);
                if(is_bool($re2)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $data = array();
                    $data["current"] = "null";
                    $data["others"] = array();
                    $time = date("H:i:s");
                    $stamp = strtotime($time);
//                    echo "<br>".strtotime($time)."  ";
                    foreach($re2 as $key=>$value){
                        $stamp1 = strtotime($value["starttime"]);
                        $stamp2 = strtotime($value["endtime"]);
//                        echo $value["starttime"]."  ";
                        if($stamp >= $stamp1 && $stamp <= $stamp2 ){
                            $data["current"]= array("id"=>$value["id"],
                                "user_id"=>$value["user_id"],
                                "coursename"=>$value["coursename"],
                                "date"=>$value["date"],
                                "starttime"=>$value["starttime"],
                                "endtime"=>$value["endtime"],
                                "teacher"=>$value["teacher"],
                                "location"=>$value["location"],
                                "detailocation"=>$value["detailocation"],
                                "signstate"=>$value["signstate"],
                                "signtime"=>$value["signtime"],
                                "state"=>$value["state"],
                                "quitstate"=>$value["quitstate"],
                                "quittime"=>$value["quittime"],
                                "quitlogo"=>$value["quitlogo"],
                                "duration"=>$value["duration"]
                            );
                        }else{
                            $data["others"] = $re2;
                        }
                    }
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}