<?php
    session_start();
    error_reporting(0);

    $sp_name  = $_GET['sp_name'];
    $jiage  = $_GET['jiage'];
    $geshu  = ceil($_GET['geshu']);
    $dizhi  = $_GET['dizhi'];
    $zongjia  = ceil($_GET['zongjie']);
    $youhui = (int)$_GET['youhui'];
    include_once("db_con.php");
    getConnect();
    $SQL = "delete from youhuiquan where youhui=".$youhui." order by id limit 1";
    $result = mysql_query($SQL);
    $SQL = "INSERT INTO dd(sp_name,jiage,geshu,dizhi,zongjia,youhui) VALUES(\"".$sp_name."\",\"".$jiage."\",\"".$geshu."\",\"".$dizhi."\",\"".$zongjia."\",\"".$youhui."\")";
    $result = mysql_query($SQL);
    if($result){
        echo 'success';
    }else{
        echo 'fail';
    }
    
?>