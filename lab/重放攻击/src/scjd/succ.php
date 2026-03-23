<!DOCTYPE html>
<!-- saved from url=(0041)https://src.edu-info.edu.cn/introduction/ -->
<html class="js cssanimations"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="byc综合站点是一个综合性漏洞训练平台。">
  <meta name="keywords" content="通用漏洞、训练平台">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>byc综合站点</title>

  <link rel="icon" type="image/ico" href="favicon.ico">

  <!-- Set render engine for 360 browser -->
  <meta name="renderer" content="webkit">

  <!-- No Baidu Siteapp-->
  <meta http-equiv="Cache-Control" content="no-siteapp">

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="byc综合站点">

  <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
  <!--
  <link rel="canonical" href="http://www.example.com/">
  -->
  <link href="./static/normalize.min.css" rel="stylesheet">
  <link href="./static/amazeui.min.css" rel="stylesheet">

  <link rel="stylesheet" href="./static/archive.css">
  
</head>
<body>
<header class="am-topbar am-g am-g-collapse">
<div class="am-container">
  <div class="am-u-sm-centered">
  <h1 class="am-topbar-brand">
  <a href="./index.php">支付成功</a>
  </h1>

  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: &#39;#doc-topbar-collapse&#39;}">
      <span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span>
  </button>

  
  </div>
</div>
</header>

<div class="main-body">
<div class="main-content">
    

<?php 
    
    session_start();
    error_reporting(0);
    include_once("db_con.php");
    getConnect();
    
    $SQL = "select * from dd order by id DESC limit 1 ";
    $result = mysql_query($SQL);
    $row = mysql_fetch_array($result);

           
        

    
?>

<div class="am-container">
<div class="am-g">
  <div class="am-u-sm-centered am-u-sm-11">
  <hr>
 
  <form  method="post" action="resq.php">
  <div class="am-g">
      <div class="am-u-sm-2">
          名称
      </div>
      <div class="am-u-sm-10">
          <?php echo "<a id='1' >".$row[sp_name]."</a>" ?>
      </div>
  </div>  <hr>
  <div class="am-g">
      <div class="am-u-sm-2">
          个数
      </div>
      <div class="am-u-sm-10">
      <?php echo "<a id='3' >".$row[geshu]."</a>" ?>
      
      </div>
  </div><hr>
  <div class="am-g">
      <div class="am-u-sm-2">
          地址
      </div>
      <div class="am-u-sm-10">
      <?php echo "<a id='4' >".$row[dizhi]."</a>" ?>
   
      </div>
  </div><hr>
  <div class="am-g">
      <div class="am-u-sm-2">
          总价
      </div>
      <div class="am-u-sm-10">
          <p>
          <?php echo "<a id='4' >".$row[zongjia]."</a>" ?>
          </p>
      </div>
  </div>
  <hr>
  <div class="am-g">
      <div class="am-u-sm-2">
          优惠
      </div>
      <div class="am-u-sm-10">
      <?php echo "<a id='6' >".$row[youhui]."</a>" ?>元
   
      </div>
  </div><hr>
      <hr>
   
  <a href="jfsc.php" class="am-btn am-btn-secondary">再买点</a>
</form>

  </div>
</div>
</div>
<div class="main-footer">



</div>
</div>
</div>

<script src="./static/jquery.js"></script>
<script src="./static/amazeui.min.js"></script>
<script type="text/javascript" src="./static/saved_resource"></script>
</body></html>