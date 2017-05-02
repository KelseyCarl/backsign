<?php
namespace Home\Controller;
use Think\Controller\RestController;

class TestController extends RestController{
    public function hello(){
        echo "hello";
//        header("Access-Control-Allow-Origin:*");
//        $Model = M();
//        switch($this->_method) {
//            case 'get':{
//                $sql = "select * from admin where token='$token'";
//                $result = $Model->query($sql);
////                var_dump($result);
//                if(is_bool($result)){
//                    $this->response(retmsg(-1,"查询失败！"),'json');
//                }else{
//                    $data = array();
//                    $data["subitem"] = array();
//                    //取得数据表所有字段的值
//                    $data["subitem"] = $result;
//                    $this->response(retmsg(0,$data,"查询成功！"),'json');
//                }
//                break;
//            }
//        }
    }
}