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
//                    $this->response(retmsg(-1,"��ѯʧ�ܣ�"),'json');
//                }else{
//                    $data = array();
//                    $data["subitem"] = array();
//                    //ȡ�����ݱ������ֶε�ֵ
//                    $data["subitem"] = $result;
//                    $this->response(retmsg(0,$data,"��ѯ�ɹ���"),'json');
//                }
//                break;
//            }
//        }
    }
}