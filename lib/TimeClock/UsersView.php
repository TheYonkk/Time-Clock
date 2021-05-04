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
     */
    public function __construct(Site $site) {
        $this->site = $site;

        $this->setTitle("Users");
    }

    /**
     * Present the users form
     * @return string HTML
     */
    public function present() {
        $html = <<<HTML
<form method="post" action="post/users.php">
    <p>
    <input class="btn btn-primary" type="submit" name="add" id="add" value="Add">
    <input class="btn btn-secondary" type="submit" name="edit" id="edit" value="Edit">
    <input class="btn btn-danger" type="submit" name="delete" id="delete" value="Delete">
    </p>

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
            <td><input type="radio" name="user" value="$id"></td>
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
</form>
HTML;

        return $html;
    }

}