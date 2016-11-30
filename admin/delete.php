<?php
include_once '../includePackage.php';
session_start();

if(isset($_SESSION['login'])) {
    if (isset($_GET['delimg'])) {
        unlink('../' . $_GET['delimg']);
        $sql = 'DELETE FROM g_image_tbl WHERE url="' . $_GET['delimg'] . '"';
//    $pdo->exec($sql);
        exeNew($sql);
        $g_id = $_GET['g_id'];
        header('location:index.php?goods-config=1&g_id=' . $g_id);
        exit;

    }


    if (isset($_GET['name'])) {
        echo $_GET['name'];
        exit;
    }
}
?>