<?php

namespace TimeClock;

/**
 * Controller for the users page users.php
 * Utilized by post/users.php
 */
class UsersController {
    /**
     * UsersController constructor.
     * @param Site $site Site object
     * @param User $user Current user
     * @param array $post $_POST
     */
    public function __construct(Site $site, User $user, array $post) {

        $root = $site->getRoot();

        if (isset($post['action'])){

            // edit a user
            if (strip_tags($post['action']) === "edit") {
                $id = strip_tags($post['user']);
                $this->result = json_encode(['ok' => true, 'action'=>'edit', 'page' => "$root/user.php?id=$id"]);
                return;

            // delete a user
            } else if (strip_tags($post['action']) === "delete"){
                $id = strip_tags($post['user']);

                $users = new Users($site);
                $user = $users->get($id);
                $name = $user->getName();

                $this->result = json_encode(['ok' => true, 'action'=>'delete',
                    'message' => "<a href='#' class='alert-link' id='confirm-delete' value='$id'>Click here</a> to permanently delete $name. <strong>This can not be undone!!!</strong>"]);
                return;

            // confirm delete user
            } else if (strip_tags($post['action']) === "confirm-delete"){
                $id = strip_tags($post['user']);

                $users = new Users($site);
                $user = $users->get($id);

                if (!is_null($user)) {
                    $name = $user->getName();
                } else {
                    $name = "";
                }

                $success = $users->delete($id);

                if ($success) {
                    $this->result = json_encode(['ok' => true, 'action' => 'confirm-delete', 'success' => $success,
                        'message' => "$name has been permanently deleted."]);
                } else {
                    $this->result = json_encode(['ok' => true, 'action' => 'confirm-delete', 'success' => $success,
                        'message' => "There was an error deleting the selected user."]);
                }
                return;

            } else if (strip_tags($post['action']) === "reset-password"){

                $id = strip_tags($post['user']);

                $mailer = new Email();
                $users = new Users($site);
                $success = is_null($users->passwordResetRequest($id, $mailer));

                if ($success) {
                    $this->result = json_encode(['ok' => true, 'action' => 'reset-password', 'success' => $success,
                        'message' => "Password reset email successfully sent!"]);
                } else {
                    $this->result = json_encode(['ok' => true, 'action' => 'reset-password', 'success' => $success,
                        'message' => "There was an error sending the user an email."]);
                }
                return;
            }

        } else {

            $message = "";

            if (count($post) === 0){
                $message = "Post was empty";
            } else {
                $message = "Unidentified problem occurred";
            }
            $this->result = json_encode(['ok' => false, 'message' => $message]);
            return;
        }

    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }


    private $result; // ajax result encoded in JSON
}