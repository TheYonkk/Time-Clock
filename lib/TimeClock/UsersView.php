<?php

namespace TimeClock;

/**
 * View class for the users page users.php
 */
class UsersView extends View {
    /**
     * Constructor
     * Sets the page title and any other settings.
     * @param Site $site The Site object
     * @param user $user The current user
     */
    public function __construct(Site $site, User $user) {
        $this->site = $site;
        $this->user = $user;

        $this->setTitle("Users");

        $root = $site->getRoot();
        $this->addLink("$root/admin.php", "Home");
        $this->addLink("$root/users.php", "Users", True);
        $this->addLink("$root/user.php", "New user");
        $this->addLink("$root/login.php", "Log out");
    }

    /**
     * Present the users form
     * @return string HTML
     */
    public function present() {
        $html = <<<HTML
<main class="mt-auto py-3">
<div class="container">

<div class="row">
<div class="col">
<div id="message"></div>
</div>
</div>

<div id="users">

    <table class="table">
    <thead>
        <tr>
            <th scope="col">&nbsp;</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Group</th>
            <th scope="col">Role</th>
        </tr>
    </thead>
    <tbody>
HTML;

        $users = new Users($this->site);

        foreach ($users->getUsers() as $user){
            $name = $user->getName();
            $email = $user->getEmail();
            $id = $user->getID();
            $role = $user->getRole();
            $group = $user->getGroup();


            if ($role == User::ADMIN) {
                $rolestr = "Admin";
            } else {
                $rolestr = "User";
            }

            if ($group === User::FORMULA){
                $groupstr = "Formula";
            } else if ($group === User::BAJA){
                $groupstr = "Baja";
            } else if ($group === User::SOLAR){
                $groupstr = "Solar";
            } else {
                $groupstr = "Other";
            }

            $html .= <<<HTML
        <tr>
        <div class="row">
            <td class="col-4">
            <div class="btn-group-sm">
            <button class="btn btn-secondary mx-1" type="button" name="edit" id="edit" value="$id">Edit</button>
            <button class="btn btn-warning mx-1" type="button" name="reset-password" id="reset-password" value="$id">Send password reset</button>
HTML;

            // do not let a user delete themselves
            if ($this->user->getId() !== $id){
                $html .= "<button class='btn btn-danger mx-1' type='button' name='delete' id='delete' value='$id'>Delete</button>";
            }

            $html .= <<<HTML
            </div>
            </td>
            </div>
            <td>$name</td>
            <td>$email</td>
            <td>$groupstr</td>
            <td>$rolestr</td>
        </tr>
HTML;


        }

        $html .= <<<HTML
        </tbody>
    </table>
</div>
</div>
</main>
HTML;

        return $html;
    }

    private $user;

}