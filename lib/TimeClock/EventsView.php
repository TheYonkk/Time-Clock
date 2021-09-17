<?php

namespace TimeClock;

/**
 * View class for the users page users.php
 */
class EventsView extends View {
    /**
     * Constructor
     * Sets the page title and any other settings.
     * @param Site $site The Site object
     * @param user $user The current user
     */
    public function __construct(Site $site, User $user, $get) {
        $this->site = $site;
        $this->user = $user;

        if (isset($get["start"]) and strip_tags($get["start"]) != ""){
            $this->start = strtotime(strip_tags($get["start"]));
        } else {
            $this->start = time() - $this->defaultSpan;
        }

        if (isset($get["end"]) and strip_tags($get["end"]) != ""){
            $this->end = strtotime(strip_tags($get["end"]));
        } else {
            $this->end = time();
        }

        if (isset($get["user"]) and strip_tags($get["user"]) != ""){
            $this->userid = strip_tags($get["user"]);
        } else {
            $this->userid = '*';
        }

        $this->setTitle("Events");

        $root = $site->getRoot();
        $this->addLink("$root/admin.php", "Home");
        $this->addLink("$root/events.php", "Events", true);
        $this->addLink("$root/users.php", "Users");
        $this->addLink("$root/user.php", "New user");
        $this->addLink("$root/login.php", "Log out");
    }

    /**
     * Present the users form
     * @return string HTML
     */
    public function present() {

        $root = $this->site->getRoot();

        $filterStart = $this->start;
        $filterEnd = $this->end;
        $filterUserID = $this->userid;

        $startFormStr = date('Y-m-d\TH:i', $this->start);
        $endFormStr = date('Y-m-d\TH:i', $this->end);

        $usersHTML = "";
        $users = new Users($this->site);
        foreach ($users->getUsers() as $user){
            $userID = $user->getID();
            $name = $user->getName();
            $usersHTML .= "<option";

            if ($userID == $this->userid){
                $usersHTML .= " selected ";
            }

            $usersHTML .= " value=$userID>$name</option>";
        }

        $html = <<<HTML
<main class="mt-auto py-3">
<div class="container">

<div class="row">
    <div class="col">
        <div id="message"></div>
    </div>
</div>

<form id="events-update" method="get" action="$root/events.php">
    <div class="row pb-4 align-items-end justify-content-evenly">
        <div class="col">
            <div class="form-group">
                <label for="start">Start date and time</label><br>
                <input type="datetime-local" class="form-control" id="start" name="start" value="$startFormStr">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="start">End date and time</label><br>
                <input type="datetime-local" class="form-control" id="end" name="end" value="$endFormStr">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="start">Filter by user</label><br>
                <select class="form-control" id="user" name="user">
                    <option value="">Select a user</option>
                    $usersHTML
                </select>
            </div>
        </div>
    </div>
    <div class="row pb-4 align-items-end justify-content-evenly">
        <div class="col-4 d-flex justify-content-center">
            <input class="btn btn-outline-secondary w-100" type="submit" name="Update" value="Update">
        </div>
    </div>
</form>

<div id="events">

    <form id="event-update" method="get" action="$root/event.php">
    
        <!-- events form filters -->
        <input type="hidden" name="filterStart" value="$filterStart">
        <input type="hidden" name="filterEnd" value="$filterEnd">
        <input type="hidden" name="filterUserID" value="$filterUserID">
        
        <table class="table">
        <thead>
            <tr>
                <th scope="col">&nbsp;</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Time in</th>
                <th scope="col">Time out</th>
                <th scope="col">Total time</th>
                <th scope="col">Notes?</th>
            </tr>
        </thead>
        <tbody>
HTML;

        $events = new Events($this->site);
        $users = new Users($this->site);

        foreach ($events->getEvents($this->start, $this->end, $this->userid) as $event){

            $user = $users->get($event->getUserID());
            $userID = $user->getId();

            $id = $event->getID();
            $name = $user->getName();
            $email = $user->getEmail();
            $startStr = $event->getClockInStr();
            $endStr = $event->getClockOutStr();
            $durationStr = $event->getDurationStr();

            if ($event->getNotes() == ""){
                $notes = "False";
            } else {
                $notes = "True";
            }


            $html .= <<<HTML
        <tr>
            <td class="col">
            <div class="btn-group-sm">
            <button class="btn btn-secondary mx-1" type="submit" name="id" id="id" value="$id">Edit</button>
            </div>
            </td>
            </div>
            <td>$name</td>
            <td>$email</td>
            <td>$startStr</td>
            <td>$endStr</td>
            <td>$durationStr</td>
            <td>$notes</td>
        </tr>
HTML;


        }

        $html .= <<<HTML
        </tbody>
    </table>
    </form>
    
    <div class="row d-flex justify-content-center mt-4">
        <div class="col-4 text-center">
            <form id="download" method="post" action="post/events.php">
                <!-- events form filters -->
                <input type="hidden" name="filterStart" value="$filterStart">
                <input type="hidden" name="filterEnd" value="$filterEnd">
                <input type="hidden" name="filterUserID" value="$filterUserID">
                <button class="btn btn-outline-primary mx-1 w-100" type="submit" name="download" id="download" value="">Download current selection</button>
            </form>
        </div>
    </div>
    
</div>
</div>
</main>
HTML;

        return $html;
    }

    private $user;
    private $userid;
    private $start;
    private $end;
    private $defaultSpan = 86400;  // one day in seconds

}