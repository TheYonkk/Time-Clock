<?php
require 'lib/site.inc.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jolly Time Clock</title>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Boostrap stylesheet -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>

<h1>um, yuh</h1>

<form> <!-- id="login" class="login" method="post" action="post/login.php"> -->
    <div class="form-group">
    <fieldset>
        <p><label for="user">Net ID: </label>
            <input type="text" id="user" name="user"></p>
        <p><label for="password">Password: </label>
            <input type="password" id="password" name="password"></p>
        <p class="in-out"><input type="radio" name="in-out" id="in">
            <label for="in">Clock in</label></p>
        <p class="in-out"><input type="radio" name="in-out" id="out">
            <label for="out">Clock out</label></p>
        <p class=""><button class="btn btn-success" type="submit" name="ok" value="Login">Submit</button>
    </fieldset>

    </div>

    <div class="message"></div>
</form>

</body>
</html>