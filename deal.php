<?php
/**
 * User: xiaoh
 * Date: 2018/6/11
 * Time: 10:06
 */
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
class deal
{
    public function breakDown()
    {
        $now = date('Y-m-d H:i:s');
        $data['time'] = $now;
        $data['status'] = 'p1staff';
        return json_encode($data);
    }

    public function updateSql($master,$username,$password,$time,$search='')
    {
        $var_datatime = $time;
        if(!deal::dateTime($var_datatime))
        {
            die(json_encode(array('status'=>100000,'message'=>'时间有误')));
        }
        try
        {
            $pdo = new PDO("mysql:host=" . $master . ";dbname=mysql", $username, $password);//创建一个pdo对象
        }
        catch(PDDException $e)
        {
            die(json_encode(array('status'=>100000,'message'=>'数据库链接失败')));
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_1 = "set global general_log=on";
        $sql_2 = "SET GLOBAL log_output='table'";

        $pdo->exec($sql_1);
        $pdo->exec($sql_2);
        if($search !='')
        {
            $sql1 = "select event_time,argument from mysql.general_log where argument like :search_sql and command_type='Query' and argument not like 'set global general_log=on;SET GLOBAL log_output%' and argument not like 'select event_time,argument from%' and event_time>'" . $var_datatime . "'";
            $stmt = $pdo->prepare($sql1);
            $stmt->bindValue(':search_sql', '%'.$search.'%');
            $stmt->execute();


        }
        else
        {
            $sql1 = "select event_time,argument from mysql.general_log where command_type='Query' and argument not like 'set global general_log=on;SET GLOBAL log_output%' and argument not like 'select event_time,argument from%' and event_time>'" . $var_datatime . "'";
            $stmt = $pdo->prepare($sql1);
            $stmt->execute();
        }
        $rows = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if(strstr($row['argument'],'SET NAMES') || strstr($row['argument'],'SET character_set_connection=')|| strstr($row['argument'],'SET global general_log=')|| strstr($row['argument'],'SET GLOBAL log_output='))
            {
                continue;
            }
            $rows[] = $row;
        }
        $data['status'] = '111111';
        $data['data'] = $rows;
        $data['token'] = $_COOKIE['token'];
        return json_encode($data);
    }



    private function updateSql_bak($master,$username,$password,$time,$search='')
    {

        $conn = mysqli_connect($master,$username,$password);
        if(!$conn)
        {
            die('数据库链接失败');
        }
        $sql_1 = "set global general_log=on";
        $sql_2 = "SET GLOBAL log_output='table'";
        $var_datatime = $time;
        if($search !='')
        {
            $where = "argument like '%".$search."%'";
            $sql1 = "select event_time,argument from mysql.general_log where ".$where." and command_type='Query' and argument not like 'set global general_log=on;SET GLOBAL log_output%' and argument not like 'select event_time,argument from%' and event_time>'" . $var_datatime . "'";
        }
        else
        {
            $sql1 = "select event_time,argument from mysql.general_log where command_type='Query' and argument not like 'set global general_log=on;SET GLOBAL log_output%' and argument not like 'select event_time,argument from%' and event_time>'" . $var_datatime . "'";
        }
        #$sql = "show databases";
        mysqli_query($conn,$sql_1);
        mysqli_query($conn,$sql_2);

        $res1 = mysqli_query($conn,$sql1);
        $rows = [];
        while ($row = mysqli_fetch_row($res1))
        {
            if(strstr($row[1],'SET NAMES') || strstr($row[1],'SET character_set_connection='))
            {
                continue;
            }
            $rows[] = $row;
        }
        $data['status'] = '111111';
        $data['data'] = $rows;
        $data['token'] = $_COOKIE['token'];
        return json_encode($data);
    }

    public  function dateTime($datetime, $empty=false)
    {
        if ( true===$empty && !isset($datetime{0}) )
        {
            return true;
        }
        return self::match('/^((19)|(20))[0-9]{2}-(([1-9])|(0[1-9])|(1[0-2]))-(([1-9])|(0[1-9])|([1,2][0-9])|(3[0-1])) (([0-9])|([0-1][0-9])|(2[0-3])):(([0-9])|([0-5][0-9])):(([0-9])|([0-5][0-9]))$/', $datetime);
    }

    public static function match($pattern, $string)
    {
        return preg_match($pattern, (string)$string);
    }
}
