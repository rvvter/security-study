<!DOCTYPE html>

<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <title>综合站点</title>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>



  <body>
  <section class="container typo">
      <div class="content">
      <nav class="l">
          <ul>
            <?php            
        
        error_reporting(0);
        session_start();
     
        ?>
   </ul>   
          <div class="col-lg-12 main-chart">
    <div class="col-lg-12 main-chart">
        <div class="box1 l">
            <h1 class="text-left mt0">个人信息</h1>


            <div class="row mtbox">
                <div class="col-md-12 col-sm-12">
                    <div class="mnc-center">
                        <div class="display-data">
                           
                                <div class="alert alert-success">
                               
                             
                                </div>
                               
                        </div>
                    </div>
                </div>
            </div>

            <div class="mnc-center">
             
              <pre>
        
        <?php            
        session_start();
        error_reporting(0);

        if($_COOKIE["uid"]){
            
            include_once("db_con.php");
            getConnect();
            $username = base64_decode ( $_COOKIE["uid"]);
            echo $username;
            $SQL = "select * from info where username =\"".$username."\"";
            $result = mysql_query($SQL);
            while($row = mysql_fetch_array($result)){
                echo "</br>姓名 : ".$row[xm];
                echo "</br>性别 : ".$row[xb];
                echo "</br>手机 : ".$row[sj];
            }
        }else{

            header("location:./index.php");
        }
        ?>
        

    </div>

    </section>
    </body>
</html>