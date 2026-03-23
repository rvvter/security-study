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
        <h1 class="text-left mt0">成绩查询系统</h1>
            <div class="form-panel">
                <form class="form-horizontal style-form" method="post" action="#">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-2 control-label">查询编号</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="number" >
                            
                        </div>
                    </div>
                    
                    <p>

                        <input type="submit" class="btn btn-theme" value="查询" />
                        <?php
                        session_start();
                        error_reporting(0);

                            if($_POST['number']){
                                include_once("db_con.php");
                                $number = $_POST['number'];

                                getConnect();
                                $SQL = "select * from score where number='$number'";
                                $result = mysql_query($SQL);
                                    echo $number;
                                    echo"<table border=\"1\"><tr>
                                        <td>Math</td>
                                        <td>English</td>
                                        <td>Chinese</td>
                                    </tr>";
                                while($row = mysql_fetch_array($result)){
                                    echo"<tr>
                                        <td>".$row[math]."</td>
                                        <td>".$row[english]."</td>
                                        <td>".$row[chinese]."</td>
                                    </tr>
                                </table>";
                                }
                                
                            }
                            
                    ?>
                    </p>
                
                </form>
            </div>
    </div>
</div>
</body>

</html>