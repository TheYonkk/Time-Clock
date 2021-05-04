<?php


namespace TimeClock;


class PasswordValidateView extends View
{
    /**
     * @var string
     */
    private $validator;
    private $session;

    /**
     * Constructor
     * Sets the page title and any other settings.
     */
    public function __construct($site, $get, &$session) {
        $this->site = $site;
        $this->get = $get;
        $this->session = $session;
        $this->setTitle("Time Clock Password Entry");
        $this->validator = strip_tags($get['v']);
    }


    public function present(){


        $html = "";

        $error = "";
        if (isset($this->get['e'])){
            $error = $this->session["ERROR"];
        }

        $html .= <<<HTML
<main class="form-password">
<form id="validate" method="post" action="post/password-validate.php">
	<fieldset>
		<img class="mb-4" src="dist/img/SR_Badge.svg" alt="" width="72" height="73">
        <h1 class="h3 mb-2 fw-normal">Change your password</h1>
		<input type="hidden" name="validator" value="$this->validator">
HTML;

        if ($error != ""){
            $html .= <<<HTML
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> $error
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
        }


        $html .= <<<HTML
		
		<div class="form-group my-2">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" area-describedby="emailHelp" placeholder="sparty@msu.edu">
            <small id="emailHelp" class="form-text text-muted">Please verify your email</small>
        </div>
		
		<div class="form-group my-2">
			<label for="password1">Password:</label><br>
			<input type="password" class="form-control" type="text" id=password1" name="password1" placeholder="password">
		</div>
		
		<div class="form-group my-2">
			<label for="password2">Password (again):</label><br>
			<input type="password" class="form-control" type="text" id=password2" name="password2" placeholder="password">
		</div>
		
		<div class="form-group mt-4">
		    <input class="btn btn-success w-25" type="submit" name="OK" value="OK">
		    <input class="btn btn-danger w-25" type="submit" name="Cancel" value="Cancel">
        </div>

	</fieldset>
</form>
</main>
HTML;

        return $html;

    }


    private $get;


}