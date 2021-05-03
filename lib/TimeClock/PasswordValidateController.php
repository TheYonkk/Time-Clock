<?php


namespace TimeClock;


class PasswordValidateController
{

    /**
     * PasswordValidateController constructor.
     * @param Site $site The Site object
     * @param array $post $_POST
     */
    public function __construct(Site $site, array $post, &$session) {
        $root = $site->getRoot();
        $this->redirect = "$root/";

        if (isset($post["Cancel"])){
            return;
        }

        //
        // 1. Ensure the validator is correct! Use it to get the user ID.
        //
        $validators = new Validators($site);
        $validator = strip_tags($post['validator']);
        $userid = $validators->get($validator);
        if($userid === null) {
            $this->redirect = "$root/password-validate.php?v=$validator&e";
            $session["ERROR"] = "Invalid or unavailable validator";
            return;
        }

        //
        // 2. Ensure the email matches the user.
        //
        $users = new Users($site);
        $editUser = $users->get($userid);
        if($editUser === null) {
            // User does not exist!
            $this->redirect = "$root/password-validate.php?v=$validator&e";
            $session["ERROR"] = "Email address is not for a valid user";
            return;
        }
        $email = trim(strip_tags($post['email']));
        if($email !== $editUser->getEmail()) {
            // Email entered is invalid
            $this->redirect = "$root/password-validate.php?v=$validator&e";
            $session["ERROR"] = "Email address does not match validator";
            return;
        }

        //
        // 3. Ensure the passwords match each other
        //
        $password1 = trim(strip_tags($post['password1']));
        $password2 = trim(strip_tags($post['password2']));
        if($password1 !== $password2) {
            // Passwords do not match
            $session["ERROR"] = "Passwords did not match";
            $this->redirect = "$root/password-validate.php?v=$validator&e";
            return;
        }

        if(strlen($password1) < 8) {
            // Password too short
            $session["ERROR"] = "Password too short. Please use at least 8 characters.";
            $this->redirect = "$root/password-validate.php?v=$validator&e";
            return;
        }

        //
        // 4. Create a salted password and save it for the user.
        //
        $users->setPassword($userid, $password1);

        //
        // 5. Destroy the validator record so it can't be used again!
        //
        $validators->remove($userid);

    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    private $redirect;

}