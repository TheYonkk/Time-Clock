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

        if (isset($this->get['e'])){
            $error = $this->session["ERROR"];
            $html .= "<p class='error'>ERROR: $error</p>";
        }

        $html .= <<<HTML
<form method="post" action="post/password-validate.php">
	<fieldset>
		<legend>Change Password</legend>
		<input type="hidden" name="validator" value="$this->validator">
		
		<div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" area-describedby="emailHelp" placeholder="sparty@msu.edu">
            <small id="emailHelp" class="form-text text-muted">Please verify your email</small>
        </div>
		
		<div class="form-group">
			<label for="password1">Password:</label><br>
			<input type="password" class="form-control" type="text" id=password1" name="password1" placeholder="password">
		</div>
		
		<div class="form-group">
			<label for="password2">Password (again):</label><br>
			<input type="password" class="form-control" type="text" id=password2" name="password2" placeholder="password">
		</div>
		
		<div class="form-group">
		    <input class="btn btn-success" type="submit" name="OK" value="OK">
		    <input class="btn btn-danger" type="submit" name="Cancel" value="Cancel">
        </div>

	</fieldset>
</form>
HTML;

        return $html;

    }


    private $get;


}