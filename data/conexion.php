<?php
    //datos de coneccion
    $server="127.0.0.1";
    $user="root";
    $pass="";
    $dbname="catink";
    //sentencia de coneccion
    $con=new mysqli($server,$user,$pass,$dbname);
    /*if($con->connect_error){
        die("la coneccion fallo: "+$con->connect_error);
    }else{
        echo "coneccion exitosa";
    }*/
?>