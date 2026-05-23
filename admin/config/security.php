<?php
session_start();
ob_start();

function secured($data)
{
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = str_replace("'", "\'", $data);
    return $data;
}
