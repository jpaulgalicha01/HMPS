<?php
include 'config/security.php';
include '../dbConfig/ClsConnection.php';


spl_autoload_register("Autoload");

function Autoload($classname)
{
    $path = "config/";
    $extenstion = ".config.php";
    $full_path = $path . $classname . $extenstion;

    if (!file_exists($full_path)) {
        return false;
    }
    include_once $full_path;
}
