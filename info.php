<?php

$open = true;
require 'lib/site.inc.php';

echo("<h1>Session</h1><pre>");
echo(print_r($_SESSION));
echo("</pre>");

echo("<h1>Cookie</h1><pre>");
echo(print_r($_COOKIE));
echo("</pre>");

phpinfo();

