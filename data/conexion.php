<?php
    //datos de coneccion
    $server="127.0.0.1";
    $user="root";
    $pass="";
    $dbname="cat_ink";
    //sentencia de coneccion
    $con=new mysqli($server,$user,$pass,$dbname);
    mysqli_set_charset($con, "utf8mb4");
    /*if($con->connect_error){
        die("la coneccion fallo: "+$con->connect_error);
    }else{
        echo "coneccion exitosa";
    }*/
?>