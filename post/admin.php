<?php
require '../lib/site.inc.php';

$controller = new TimeClock\AdminController($site, $_POST);

if (!$controller->hasDownload()) {
//    $page = $controller->getRedirect();
//    echo "<a href='$page'>$page</a>\n";
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
    header("location: " . $page);
}