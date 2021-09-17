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



        $events = new Events($site);
        $event =$events->get($id);

        if (is_null($event)){
            return;
        }

        # update everything
        $event->setClockIn($in);
        $event->setNotes($notes);
        if (!is_null($out)){
            $event->setClockOut($out);
        }

        // determine if the current user can modify the edited event
        if ($user->getRole() == User::ADMIN) {
            $events->update($event);
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