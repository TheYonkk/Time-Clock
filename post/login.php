<?php
$open = true;		// Can be accessed when not logged in
require '../lib/site.inc.php';

$controller = new TimeClock\LoginController($site, $_SESSION, $_POST);

$page = $controller->getRedirect();
//header("location: " . $page);

echo("<a href='$page'>$page</a>");

echo("<pre>");
print_r($_SESSION);
echo("</pre>");

echo("<pre>");
print_r($_COOKIE);
echo("</pre>");