<?php

    require('./queryManager.php');

    session_start();

    if(!isset($_SESSION['username']))
    {
        echo 'CONTENUTO VIETATO AD UTENTI NON LOGGATI';
        exit;
    }

    $result = getMeds($_GET['startID']);
    
    echo json_encode($result);
?>