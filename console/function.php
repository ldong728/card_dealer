<?php

function printView($addr, $title = 'abc', $subPath = '/console')
{
    $mypath = $GLOBALS['mypath'];
    include $mypath . $subPath . '/templates/header.html.php';
    include $mypath .$subPath. '/' . $addr;
    include $mypath . $subPath . '/templates/footer.html.php';
}