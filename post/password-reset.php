<?php
$open = true;
require '../lib/site.inc.php';

$controller = new TimeClock\PasswordResetController($site, $_POST, $_SESSION);
header("location: " . $controller->getRedirect());