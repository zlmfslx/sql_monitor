<?php
/**
 * User: xiaoh
 * Date: 2018/6/11
 * Time: 10:51
 */
include_once('./deal.php');
$m = isset($_GET['m'])?$_GET['m']:header("Location:404.php");

if($m == 'breakdown')//下断点
{
    if($_COOKIE['token'] !==$_GET['token'])
    {
        die(json_encode(array('status'=>100000,'message'=>'重新刷新页面')));
    }
    $deal = new deal();
    $retrun = $deal->breakDown();
    echo $retrun;
}
elseif ($m == 'updateSql')//获取执行过程SQL语句
{
    if($_COOKIE['token'] !=$_POST['token'])
    {
        die(json_encode(array('status'=>100000,'message'=>'重新刷新页面')));
    }

    $master = empty($_POST['master']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写地址'))):$_POST['master'];
    $username = empty($_POST['username']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写数据库名'))):$_POST['username'];
    $password = empty($_POST['password']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写数据库密码'))):$_POST['password'];
    $time = empty($_POST['time']{0})? die(json_encode(array('status'=>100000,'message'=>'请先点击下断'))):$_POST['time'];
    $deal = new deal();
    $retrun = $deal->updateSql($master,$username,$password,$time);
    echo $retrun;
}
elseif ($m == 'searchSql')//根据搜索内容获取执行过程SQL语句
{
    if($_COOKIE['token'] !=$_POST['token'])
    {
        die(json_encode(array('status'=>100000,'message'=>'重新刷新页面')));
    }
    $master = empty($_POST['master']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写地址'))):$_POST['master'];
    $username = empty($_POST['username']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写数据库名'))):$_POST['username'];
    $password = empty($_POST['password']{0})?die(json_encode(array('status'=>100000,'message'=>'请填写数据库密码'))):$_POST['password'];
    $time = empty($_POST['time']{0})? die(json_encode(array('status'=>100000,'message'=>'请先点击下断'))):$_POST['time'];
    $search = empty($_GET['search']{0})? '':$_GET['search'];
    $deal = new deal();
    $retrun = $deal->updateSql($master,$username,$password,$time,$search);
    echo $retrun;
}
elseif ($m == 'token')//获取token,识别
{
    unset($_COOKIE['token']);
    $token = getMillisecond()+rand(1,99999).make_password();
    setcookie('token',$token,time()+3600);
    $data['token'] = $token;
    $data['status'] = '111111';
    echo json_encode($data);
}


function make_password( $length = 16 )
{
    // 密码字符集，可任意添加你需要的字符
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'Bc', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'aN',
    );

    // 在 $chars 中随机取 $length 个数组元素键名
    $keys = array_rand($chars, $length);
    $password = '';
    for($i = 0; $i < $length; $i++)
    {
        // 将 $length 个数组元素连接成字符串
        $password .= $chars[$keys[$i]];
    }
    return $password;
}

function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}
