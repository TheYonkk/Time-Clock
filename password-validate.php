<?php
$open = true;
require 'lib/site.inc.php';
$view = new TimeClock\PasswordValidateView($site, $_GET, $_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $view->head(); ?>
</head>

<body class="password text-center">

    <?php
    echo $view->present();
    echo $view->boostrapJS();
    ?>

</body>
</html>