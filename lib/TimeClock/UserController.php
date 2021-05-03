<?php

namespace TimeClock;

/**
 * Controller for the users page users.php
 * Utilized by post/users.php
 */
class UserController {
    /**
     * UsersController constructor.
     * @param Site $site Site object
     * @param User $user Current user
     * @param array $post $_POST
     */
    public function __construct(Site $site, User $user, array $post) {
        $root = $site->getRoot();

        $this->redirect = "$root/users.php";

        if (isset($post["Cancel"])){
            return;
        }

        //
        // Determine if this is new user or editing an
        // existing user. We determine that by looking for
        // a hidden form element named "id". If there, it
        // gives the ID for the user we are editing. Otherwise,
        // we have no user, so I'll use an ID of 0 to indicate
        // that we are adding a new user.
        //
        if(isset($post['id'])) {
            $id = strip_tags($post['id']);
        } else {
            $id = 0;
        }

        //
        // Get all of the stuff from the from
        //
        $email = strip_tags($post['email']);
        $name = strip_tags($post['name']);

        switch($post['role']) {
            case "Admin":
                $role = User::ADMIN;
                break;

            default:
                $role = User::USER;
                break;
        }

        switch($post['group']) {
            case "Formula":
                $group = User::FORMULA;
                break;

            case "Baja":
                $group = User::BAJA;
                break;

            case "Solar":
                $group = User::SOLAR;
                break;

            default:
                $group = User::OTHER;
                break;
        }

        $row = ['id' => $id,
            'email' => $email,
            'name' => $name,
            'password' => null,
            'salt' => null,
            'group' => $group,
            'role' => $role
        ];
        $editUser = new User($row);


        $users = new Users($site);
        if($id == 0) {
            // This is a new user
            $mailer = new Email();
            $users->add($editUser, $mailer);

        } else {

            // determine if the current user can modify the edited user
            if ($user->getRole() == User::ADMIN){
                $users->update($editUser);
            }

        }
    }

    /**
     * Get any redirect link
     * @return mixed Redirect link
     */
    public function getRedirect() {
        return $this->redirect;
    }


    private $redirect;	///< Page we will redirect the user to.
}