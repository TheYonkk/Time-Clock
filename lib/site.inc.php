<?php
/**
 * @file
 * A file loaded for all pages on the site.
 */
require __DIR__ . "/../vendor/autoload.php";

define("LOGIN_COOKIE", "timeclock_cookie");


// Start the session system
session_start();

// Create and localize the Site object
$site = new TimeClock\Site();
$localize = require 'localize.inc.php';
if(is_callable($localize)) {
	$localize($site);
}


/*
 * Login functionality
 */
if(!isset($open)) {

    // This is a page other than the login pages
    if (!isset($_SESSION[TimeClock\User::SESSION_NAME])) {

        // there is a cookie
        if (isset($_COOKIE[LOGIN_COOKIE]) && $_COOKIE[LOGIN_COOKIE] != ""){

          $cookie_raw = $_COOKIE[LOGIN_COOKIE];
          $cookie = json_decode($cookie_raw, true);

          // if the cookie has the required information
          if (isset($cookie['user']) && isset($cookie['token'])){
            $userID = $cookie['user'];
            $token = $cookie['token'];

            // if the cookie is valid
            $cookies = new \TimeClock\Cookies($site);
            $hash = $cookies->validate($userID, $token);
            if (!is_null($hash)){

              // It's valid, we can log in!
              $users = new \TimeClock\Users($site);
              $user = $users->get($userID);
              $_SESSION[TimeClock\User::SESSION_NAME] = $user;

              // delete the old hash
              $cookies->delete($hash);

              // set the cookie to the new hash
              $token = $cookies->create($user->getId());
              $expire = time() + (86400 * 365); // 86400 = 1 day
              $cookie = array("user" => $user->getId(), "token" => $token);
              setcookie(LOGIN_COOKIE, json_encode($cookie), $expire, "/");
              return;

            }

          }

        }

        // If not logged in, force to the login page
        $root = $site->getRoot();
        header("location: $root/login.php");
        exit;

    } else {
        // We are logged in.
        $user = $_SESSION[TimeClock\User::SESSION_NAME];
    }
}
