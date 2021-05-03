<?php


namespace TimeClock;


class LoginController
{

    /**
     * LoginController constructor.
     * @param Site $site The Site object
     * @param array $session $_SESSION
     * @param array $post $_POST
     */
    public function __construct(Site $site, array &$session, array $post) {

        // log out any existing user
        if (isset($session[User::SESSION_NAME])) {
            unset($session[User::SESSION_NAME]);
        }

        // Create a Users object to access the table
        $users = new Users($site);

        $email = strip_tags($post['email']);
        $password = strip_tags($post['password']);
        $user = $users->login($email, $password);


        $root = $site->getRoot();
        if($user === null) {
            // Login failed
            $this->redirect = "$root/login.php?e";
            $session['error'] = "Invalid login credentials.";
            return;
        }

        // login successful
        $session[User::SESSION_NAME] = $user;

        // if they want to stay logged in
        if(isset($post['keep'])) {
            echo("KEEPER");
            $cookies = new Cookies($site);
            $token = $cookies->create($user->getId());
            $expire = time() + (86400 * 365); // 86400 = 1 day
            $cookie = array("user" => $user->getId(), "token" => $token);
            setcookie(LOGIN_COOKIE, json_encode($cookie), $expire, "/");
        }


        // redirect to index
        $this->redirect = "$root/";


    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }


    private $redirect;	// Page we will redirect the user to.

}