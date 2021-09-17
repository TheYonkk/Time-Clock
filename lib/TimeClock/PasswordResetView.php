<?php


namespace TimeClock;


class PasswordResetView extends View
{

    public function __construct($site, &$session, $get, &$cookie){
        parent::__construct($site);

        $this->session = $session;

        if (isset($get['e'])){
            $this->error = True;
        }

        if (isset($get['s'])){
            $this->success = True;
        }

        // log out if logged in
        if (isset($session[User::SESSION_NAME])){
            unset($session[User::SESSION_NAME]);
        }

        // if someone is at the login page, we want to log them out
        if(isset($cookie[LOGIN_COOKIE]) && $cookie[LOGIN_COOKIE] != "") {
            $cookie = json_decode($cookie[LOGIN_COOKIE], true);
            $cookies = new Cookies($site);
            $hash = $cookies->validate($cookie['user'], $cookie['token']);
            if($hash !== null) {
                $cookies->delete($hash);
            }

            $expire = time() - 3600;
            setcookie(LOGIN_COOKIE, "", $expire, "/");
        }

    }

    public function presentForm() {

        $html = "";

        $root = $this->site->getRoot();


        $html .= <<<HTML
    
    <main class="form-reset">
  <form  method="post" action="post/password-reset.php">
    <img class="mb-4" src="dist/img/SR_Badge.svg" alt="" width="72" height="73">
    <h1 class="h3 mb-3 fw-normal">Enter your email.</h1>
HTML;

        if ($this->error) {
            $msg = $this->session['error'];
            $html .= <<<HTML
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> $msg
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
        } elseif ($this->success) {
            $msg = $this->session['success'];
            $html .= <<<HTML
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> $msg
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
        }

        $html .= <<<HTML

    <div class="form-floating">
      <input type="email" name="email" class="form-control" id="floatingInput" placeholder="sparty@msu.edu">
      <label for="floatingInput">Email address</label>
    </div>

    <button class="w-100 btn btn-lg btn-primary mt-3" type="submit">Send password reset link</button>
    <a class="w-100 btn btn-lg btn-secondary mt-3" href="$root/login.php">Back to login</a>
  </form>
</main>

HTML;


        return $html;
    }

    private $session;
    private $error = False;
    private $success = False;

}