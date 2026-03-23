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
          <h1 class="text-left mt0">修改密码</h1>
            <?php            
        error_reporting(0);
        session_start();
        
        ?> 
   </ul>
   <form class="form-horizontal style-form" method="post" action="#">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">NewPassword</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="newpassword">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Repeat</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="passwordrepeat">
                            
                        </div>
                    </div>
                    <p>
                        <input type="submit" class="btn btn-theme" value="Submit" />
                        
                    </p>
                
                </form>
   <hr>   
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
        <hr> 
        <?php   
        if($_POST['newpassword']){
            include_once("db_con.php");
            $newpassword = $_POST['newpassword'];
            $passwordrepeat = $_POST['passwordrepeat'];
            $username = base64_decode($_COOKIE["uid"]);
            if($newpassword === $passwordrepeat){
                getConnect();
                $SQL = "UPDATE user1 SET password='$newpassword' where username='$username'";
                $SQLresult = mysql_query($SQL);
                echo "<script>alert`修改成功`</script>";
                header("location:./index.php");                        
            } else {
                echo "两次密码不同，请重新输入";
            }
        }
            closeConnect();
                    ?>
        

    </div>

    </section>
    </body>
</html>