<?php 
if($_COOKIE["uid"]){     
    include_once("db_con.php");
    getConnect();
    $username = base64_decode ( $_COOKIE["uid"]);
    //echo $username;
    $SQL = "select * from info where username =\"".$username."\"";
    $result = mysql_query($SQL);
    while($row = mysql_fetch_array($result)){
        $arr = array('姓名' => $row[xm], '性别' => $row[xb], '手机' => $row[sj]);
    }
}
$result=json_encode($arr); 
$callback=$_GET['callback']?$_GET['callback']:'callback'; 
echo $callback."($result)";
?>