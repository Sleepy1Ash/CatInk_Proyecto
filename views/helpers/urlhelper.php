<?php
function basePath(){
    $host = $_SERVER['HTTP_HOST'];
    // entorno local
    if(strpos($host, 'localhost') !== false){
        return "/CatInk_Proyecto";
    }
    // producción
    return "";
}
function encodeId($id){
    return rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
}
function decodeId($hash){
    return base64_decode(strtr($hash, '-_', '+/'));
}
function newsUrl($id){
    return basePath() . "/n/" . encodeId($id);
}