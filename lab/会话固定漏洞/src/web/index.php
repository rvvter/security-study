<?php


$id=md5(time().'salt');//无法预测的salt
header('location:login.php?PHPSESSID='.$id);