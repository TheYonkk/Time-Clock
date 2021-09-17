<?php
$open = true;
require 'lib/site.inc.php';
$view = new TimeClock\PasswordResetView($site, $_SESSION, $_GET, $_COOKIE);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php echo $view->head(); ?>
</head>

<body class="reset text-center">

    <?
    echo $view->presentForm();

    // must go last
    echo $view->boostrapJS();
    ?>


</body>
</html>
