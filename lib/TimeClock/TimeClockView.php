<?php


namespace TimeClock;


class TimeClockView extends View
{

    public function __construct($site, &$session, $get){
        $this->session = $session;

        if (isset($get['e'])){
            $this->error = True;
        }

        $this->setTitle("Time Clock");

    }

    public function presentForm() {

        $html = "<div id='message'></div>";

        $user = $_SESSION[User::SESSION_NAME];
        $name = $user->getName();


        $html .= <<<HTML
    
    <main class="form-timeclock">
  <form id="timeclock">
    <img class="mb-4" src="dist/img/SR_Badge.svg" alt="" width="72" height="73">
    <h1 class="h3 mb-1 fw-normal">Hello, $name</h1>
    <p class="">Not you? <a href="login.php">Sign out</a>.</p>

    <div>
        <input type="radio" class="btn-check" name="clock" value="in" id="success-outlined" autocomplete="off">
        <label class="btn btn-outline-success my-1 w-100" for="success-outlined">Clock in</label>

        <input type="radio" class="btn-check" name="clock" value="out" id="danger-outlined" autocomplete="off">
        <label class="btn btn-outline-danger my-2 w-100" for="danger-outlined">Clock out</label>
    </div>
    
    <div class="checkbox mb-3 clock-override">
      <label>
        <input type="checkbox" value="override" name="override" value="override"> Override error?
      </label>
    </div>

    <div class="form-floating">
        <button class="w-100 my-4 btn btn-primary" type="submit">Submit</button>
    </div>
  </form>
</main>

HTML;


        return $html;
    }

    private $session;
    private $error = False;

}