<?php
//�������
function p($value)
{
    if (is_bool($value))
    {
        var_dump($value);
    }elseif (is_null($value))
    {
        var_dump(NULL);
    }else
    {
        echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>".print_r($value,true)."</pre>";
    }
}
//���汾,��ѡȡʱ��ǰ������Ϊ׼
function checkbanben($filepath,$banben)
{
    $chk = file_exists($filepath."/".$banben);
    if (!$chk)
    {
        $mon = $banben.'01';
        $banben = checkbanben($filepath,date("Ym",strtotime("$mon -1 month")));
    }else
    {
        return $banben;
    }
    return $banben;
}

//��ע��ʽ
function safe($s)
{
    //��ȫ���˺���
    if(get_magic_quotes_gpc())
    {
        $s=stripslashes($s);
    }
    //$s=mysql_real_escape_string($s);
    $s = addslashes($s);
    return $s;
}

// ��֤token��������
function checktoken($token)
{
    if(!isset($token) || empty($token))
        return false;
    import("Xsrb.Token");
    $token_obj = new \Token($token);
    return $token_obj->CheckToken(C('CACHE_NAME'),C('Cache_TimeOut_Token'));
}

// ��֤������֤�빫������
function checkyzm($skey,$phone,$yzm)
{
    import("Xsrb.Token");
    $token_obj = new \Token();

    $json_str = $token_obj->GetKey(C('CACHE_NAME'),C('Cache_TimeOut_Token'),$skey."_seesionid");
    $s_arr = "";
    if(!$json_str)//session������
    {
        return -3;
    }
    else		//session����
    {
        $s_arr = json_decode($json_str,true);
        if($s_arr["mobile"] != $phone)
        {
            return -10;
        }
        if($s_arr["mobile_code"] != $yzm)
        {
            return -11;
        }
        $token_obj->DeleteKey(C('CACHE_NAME'),C('Cache_TimeOut_Token'),$skey."_seesionid");
    }
    return 0;
}

//����ʡ���С����� ƥ����´�
function getoffice(&$model,$province,$city,$county)
{
    //���ݵ�ַƥ����´�

    $office = "";
    //ƥ������
    $sql = "select office from area_county where county='".safe($county)."'";
    $sqlret=$model->query($sql);
    if(!is_bool($sqlret))
    {
        if(count($sqlret) > 0)
        {
            $office = $sqlret[0]["office"];
        }
        else
        {
            //ƥ����
            $sql = "select office from area_city where city='".safe($city)."'";
            $sqlret=$model->query($sql);
            if(!is_bool($sqlret))
            {
                if(count($sqlret) > 0)
                {
                    $office = $sqlret[0]["office"];
                }
                else
                {
                    //ƥ��ʡ
                    $sql_office = "select office from area_province where province='".safe($province)."'";
                    $sqlret=$model->query($sql);
                    if(!is_bool($sqlret))
                    {
                        if(count($sqlret) > 0)
                        {
                            $office = $sqlret[0]["office"];
                        }
                        else
                        {
                            $office = "";
                        }
                    }
                    else
                    {
                        return -17;
                    }
                }
            }
            else
            {
                return -16;
            }
        }
    }
    else
    {
        return -15;
    }
    return $office;
}

//��֤���㷨
function random($length = 6 , $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric)
    {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    }
    else
    {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++)
        {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

//�ֻ��Ÿ�ʽ��֤
function isPhoneNum($phone)
{
    return preg_match("/^1[3|4|5|6|7|8][0-9]\d{8}$/",$phone);
}

//��ȡ������´�
function getlastoffice(&$model,$userphone)
{
    $sql = "select lastoffice,office from Xsrb_user where phone='".$userphone."'";
    $list = $model->query($sql);
    if(isset($list[0]["lastoffice"]) && $list[0]["lastoffice"] != '')
    {
        return $list[0]["lastoffice"];
    }
    else if(isset($list[0]["office"]) && $list[0]["office"] != '')
    {
        return $list[0]["office"];
    }
    return "";
}

//curl send
function http($url, $params, $method = 'GET', $header = array(), $multi = false)
{
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header
    );
    /* �����������������ض����� */
    switch(strtoupper($method))
    {
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //�ж��Ƿ����ļ�
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('��֧�ֵ�����ʽ��');
    }
    /* ��ʼ����ִ��curl���� */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) throw new Exception('����������' . $error);
    return  $data;
}

//���غ���
function retmsg($retcode,$retdata=null,$retmessage=null)
{
    $retmsg = "";
    switch($retcode)
    {
        case 0	: { $retmsg = "�����ɹ�"; break; }
        case -1	: { $retmsg = "����ʧ��"; break; }
        case -2	: { $retmsg = "token��֤ʧ��"; break; }
        case -3	: { $retmsg = "������֤�����"; break; }
        case -4	: { $retmsg = "�����ѱ�ע��"; break; }
        case -5	: { $retmsg = "�ֻ��Ÿ�ʽ����ȷ"; break; }
        case -6	: { $retmsg = "���ն���������ʹ����"; break; }
        case -7	: { $retmsg = "һ������ֻ�ܻ�ȡһ�ζ���"; break; }
        case -8	: { $retmsg = "��֤�뷢��ʧ��"; break; }
        case -9	: { $retmsg = "���������û�������"; break; }
        case -10: { $retmsg = "�ֻ����벻ƥ��"; break; }
        case -11: { $retmsg = "��֤�����"; break; }
        case -12: { $retmsg = "ԭ�������"; break; }
        case -13: { $retmsg = "�û���Ϣδ���κ��޸�"; break; }
        case -14: { $retmsg = "���벻��Ϊ��"; break; }
        case -15: { $retmsg = "��ѯ������Χ�ڵİ��´�ʧ��(����)"; break; }
        case -16: { $retmsg = "��ѯ������Χ�ڵİ��´�ʧ��(��)"; break; }
        case -17: { $retmsg = "��ѯ������Χ�ڵİ��´�ʧ��(ʡ)"; break; }
        case -18: { $retmsg = "skey����Ϊ��"; break; }
        case -19: { $retmsg = "�ú����ѱ�ע��"; break; }
        case -20: { $retmsg = "ͼƬ���ݲ���Ϊ��"; break; }
        case -21: { $retmsg = "ͼƬbase64���ݸ�ʽ����"; break; }
        case -22: { $retmsg = "�������ݲ���Ϊ��"; break; }
        case -23: { $retmsg = "����ʱ,��Ѷid����Ϊ��"; break; }
        case -24: { $retmsg = "�ջ���ַδ���޸�"; break; }
        case -25: { $retmsg = "ɾ���ջ���ַʱid����Ϊ��"; break; }
        case -25: { $retmsg = "δ�ҵ���Ӧ���ջ��ַ"; break; }
        case -26: { $retmsg = "�û�������"; break; }
        case -27: { $retmsg = "������"; break; }
        case -28: { $retmsg = "ɾ������ʱid����Ϊ��"; break; }
        case -29: { $retmsg = "���ɶ�����ʧ��"; break; }
        case -30: { $retmsg = "�������ظ�"; break; }
        case -31: { $retmsg = "û��ѡ����Ʒ"; break; }
        case -32: { $retmsg = "��������"; break; }
        case -33: { $retmsg = "�ջ���ַ����Ϊ��"; break; }
        case -34: { $retmsg = "�ջ���ַid����Ϊ��"; break; }
        case -35: { $retmsg = "����˺Ϳͻ��˼�����ܼ۲���"; break; }
        case -36: { $retmsg = "δ���κ��޸�"; break; }
        case -37: { $retmsg = "����ʣ������������"; break; }
        case -38: { $retmsg = "���ﳵ��಻�ܳ���10����Ʒ"; break; }
        case -39: { $retmsg = "���㣬���ֵ��"; break; }
        //�����̨������
        case -51: { $retmsg = "��û�й���ԱȨ��"; break; }
        default	: { $retmsg = "δ֪����";}
    }
    return array("resultcode"=>$retcode,"resultmsg"=>empty($retmessage)?$retmsg:$retmessage,"data"=>$retdata);
}
?>