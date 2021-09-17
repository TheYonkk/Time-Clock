<?php
$open = true;
require 'lib/site.inc.php';
$view = new TimeClock\QRView($site, $_SESSION, $_GET);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php echo $view->head(); ?>
</head>

<body class="login text-center">

    <?
    echo $view->present();

    // must go last
    echo $view->boostrapJS();
    ?>


</body>
</html>
