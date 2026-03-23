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
         
   </ul>
   
  
          <div class="col-lg-12 main-chart">
    <div class="col-lg-12 main-chart">
        <div class="box1 l">
            <?php 
            session_start();
            if($_SESSION['user']=='admin'){    
                echo '<h1 class="text-left mt0">Hello admin!</h1>';
            }
                else{
                    echo '<h1 class="text-left mt0">未登录</h1>';
                }
                ?>


  

    </div>

    </section>
    </body>
</html>