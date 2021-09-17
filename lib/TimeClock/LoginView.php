<?php


namespace TimeClock;


class LoginView extends View
{

    public function __construct($site, &$session, $get, &$cookie){
        parent::__construct($site);

        $this->session = $session;

        if (isset($get['e'])){
            $this->error = True;
        }

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
    
    <main class="form-signin">
  <form  method="post" action="post/login.php">
    <img class="mb-4" src="dist/img/SR_Badge.svg" alt="" width="72" height="73">
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
HTML;

        if ($this->error) {
            $msg = $this->session['error'];
            $html .= <<<HTML
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> $msg
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
        }

        $html .= <<<HTML

    <div class="form-floating">
      <input type="email" name="email" class="form-control" id="floatingInput" placeholder="sparty@msu.edu">
      <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating">
      <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>

    <div class="checkbox mb-3">
      <label>
        <input type="checkbox" value="remember-me" name="keep" value="keep"> Remember me
      </label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <p class="mt-5"><a href="$root/password-reset.php">I forgot my damn password</a></p>
  </form>
  
</main>

HTML;


        return $html;
    }

    private $session;
    private $error = False;

}