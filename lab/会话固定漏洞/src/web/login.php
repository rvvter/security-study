

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
        <h1 class="text-left mt0">管理登录</h1>
            <div class="form-panel">
                <form class="form-horizontal style-form" method="post" action="#">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Username</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="user">
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="pass">
                            
                        </div>
                    </div>
                    <p>

                        <input type="submit" class="btn btn-theme" value="Login" />
                        
                    </p>
                
                </form>
            </div>
    </div>
</div>
</body>

</html>
<?php
session_start();
$user='admin';
$pass='password';
if(isset($user)&&isset($pass)){    
if($_POST['user']==$user && $_POST['pass']==$pass ){        
    $_SESSION['user']='admin';  
    if ($_SESSION['user']=='admin'){    
        echo "<script>alert('登录成功')</script>";
         
    }else{    
        echo "<script>alert('登录失败')</script>";
    }  
    header("location:./info.php"); 
}
}

?>
