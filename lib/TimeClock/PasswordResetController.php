<?php


namespace TimeClock;


class PasswordResetController
{

    /**
     * PasswordResetController constructor.
     * @param Site $site The Site object
     * @param array $post $_POST
     */
    public function __construct(Site $site, array $post, &$session) {
        $root = $site->getRoot();
        $this->redirect = "$root/";


        //
        // 1. Ensure that an email address exists for the user
        //
        $users = new Users($site);
        $email = trim(strip_tags($post['email']));
        $user = $users->getByEmail($email);
        if($user === null) {
            // User does not exist!
            $this->redirect = "$root/password-reset.php?&e";
            $session["error"] = "Email address was not found or is invalid.";
            return;
        }

        // create validator
        $mailer = new Email();
        $users->passwordResetRequest($user->getId(), $mailer);
        $this->redirect = "$root/password-reset.php?&s";
        $session["success"] = "A reset link was sent to the specified address.";

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