

<!DOCTYPE html>

<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <title>综合站点</title>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>



  <section class="container typo">
      <div class="content">
          <nav class="l">
         
          </nav><div class="row">
        <div class="col-lg-12 main-chart">


    <div class="box1 l">
        <h1 class="text-left mt0">用户</h1>
            <div class="form-panel">
                <form class="form-horizontal style-form" method="post" action="#">
                <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Username</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="username">
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password1">
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Password repeat</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password2">
                            
                        </div>
                    </div>
                    <p>

                        <input type="submit" class="btn btn-theme" value="submit" />
                        <?php            
                            session_start();
                            error_reporting(0);
                            
                                if($_POST['username']&$_POST['password1']&$_POST['password2']){
                                    if($_POST['password1']==$_POST['password2']){
                                        $username = $_POST['username'];
                                        $password = $_POST['password1'];
                                        include_once("db_con.php");
                                        getConnect();
                                        
                                        $SQL = "INSERT INTO user1 (username,password, is_ad) VALUES ('".$username."','".$password."','0')";
                                        $resultLogin = mysql_query($SQL);
                                        if($resultLogin>0){
                                            echo '注册成功';
                                        }
                                        closeConnect();
                                    }else{
                                        echo '输入不合法';
                                    }
                                }else{
                                    echo '输入不合法';
                                }
                                
                                
                           
                            ?>






                    
                    </p>
                
                </form>
            </div>
    </div>
</div>
</body>

</html>