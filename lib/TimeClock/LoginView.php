<?php


namespace TimeClock;


class LoginView extends View
{

    public function __construct($site, &$session, $get, &$cookie){
        $this->session = $session;

        if (isset($get['e'])){
            $this->error = True;
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

        if ($this->error){
            $msg = $this->session['error'];
            $html .= "<p class=\"msg\">$msg</p>";
        }


        $html .= <<<HTML
    <form class="form-signin text-center" method="post" action="post/login.php">
      <img class="mb-4" src="images/SR_Badge.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required="" autofocus="">
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required="">
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" name="keep" id="keep" value="keep"> Remember me
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>

HTML;


        return $html;
    }

    private $session;
    private $error = False;

}