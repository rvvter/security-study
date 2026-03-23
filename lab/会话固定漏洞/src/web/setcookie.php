<?php
if($_GET['PHPSESSID']){
    header('Set-Cookie: PHPSESSID='.$_GET['PHPSESSID']);
}