<?php

namespace TimeClock;

/**
 * Controller for the users page users.php
 * Utilized by post/users.php
 */
class EventController {
    /**
     * UsersController constructor.
     * @param Site $site Site object
     * @param User $user Current user
     * @param array $post $_POST
     */
    public function __construct(Site $site, User $user, array $post) {
        $root = $site->getRoot();

        $this->redirect = "$root/events.php";

        if (isset($post["Cancel"])){
            return;
        }

        // if the user selected to potentially delete an event
        if (isset($post["delete"]) && isset($post['id'])){

            // confirmed delete
            if (strip_tags($post["delete"]) === "deleteConfirmed") {
                $events = new Events($site);
                $success = $events->delete(strip_tags($post['id']));

                $this->redirect = "$root/events.php?msg=";
                if ($success){
                    $this->redirect .= "Success!";
                } else {
                    $this->redirect .= "Error!";
                }

                return;
            }

            $this->redirect = "$root/event.php?msg=delete&id=" . strip_tags($post['id']);
            return;
        }

        // get the event ID that we are editing
        if(isset($post['id'])) {
            $id = strip_tags($post['id']);
        } else {
            return;
        }

        //
        // Get all of the stuff from the form
        //
        $in = strtotime(strip_tags($post['in']));
        $out = strip_tags($post['out']);
        if ($out == "" or $out == 0){
            $out = null;
        } else {
            $out = strtotime($out);
        }
        $notes = strip_tags($post['notes']);

        // filters to redirect back to the events page
        if (!is_null($post["filterStart"])){
            if ($this->eventsFilters !== "?"){
                $this->eventsFilters .= "&";
            }
            $this->eventsFilters .= "start=" . urlencode(Event::PHPToSQLTime(strip_tags($post["filterStart"])));
        }
        if (!is_null($post["filterEnd"])){
            if ($this->eventsFilters !== "?"){
                $this->eventsFilters .= "&";
            }
            $this->eventsFilters .= "end=" . urlencode(Event::PHPToSQLTime(strip_tags($post["filterEnd"])));
        }
        if (!is_null($post["filterUserID"])){
            if ($this->eventsFilters !== "?"){
                $this->eventsFilters .= "&";
            }
            $this->eventsFilters .= "user=" . strip_tags($post["filterUserID"]);
        }
        // User ID used for a new event
        if(isset($post['newUserID'])) {
            $newUserID = strip_tags($post['newUserID']);
        } else {
            $newEventID = Null;
        }


        $events = new Events($site);
        if ($id == -1){  // new event here
            $users = new Users($site);
            $user = $users->get($newUserID);
            $eventId = $events->clockIn($user);
            $event = $events->get($eventId);
        } else {
            $event = $events->get($id);
        }


        if (!is_null($event)) {

            # update everything
            $event->setClockIn($in);
            $event->setNotes($notes);
            if (!is_null($out)) {
                $event->setClockOut($out);
            }

            // determine if the current user can modify the edited event
            if ($user->getRole() == User::ADMIN) {
                $events->update($event);
            }
        }



        // success, add filters back onto redirect
        $this->redirect .= $this->eventsFilters;
    }

    /**
     * Get any redirect link
     * @return mixed Redirect link
     */
    public function getRedirect() {
        return $this->redirect;
    }


    private $redirect;	///< Page we will redirect the user to.
    private $eventsFilters = "?";  ///< filters that we will add to the events page redirect
}