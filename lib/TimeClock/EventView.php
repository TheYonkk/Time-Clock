<?php


namespace TimeClock;


class EventView extends View
{

    private $eventid = null;

    private $filterStart = null;
    private $filterEnd = null;
    private $filterUserID = null;

    public function __construct(Site $site, $get)
    {
        parent::__construct($site);

        if (isset($get["id"]) && !is_null($get["id"])){
            $this->eventid = strip_tags($get["id"]);
        }

        if (isset($get["filterStart"])){
            $this->filterStart = strip_tags($get["filterStart"]);
        }
        if (isset($get["filterEnd"])){
            $this->filterEnd = strip_tags($get["filterEnd"]);
        }
        if (isset($get["filterUserID"])){
            $this->filterUserID = strip_tags($get["filterUserID"]);
        }



        $this->setTitle("Edit Event");

        $root = $site->getRoot();
        $this->addLink("$root/admin.php", "Home");
        $this->addLink("$root/events.php", "Events");
        $this->addLink("$root/users.php", "Users");
        $this->addLink("$root/user.php", "New user");
        $this->addLink("$root/login.php", "Log out");
    }


    public function present(){

        $root = $this->site->getRoot();

        $events = new Events($this->site);
        $users = new Users($this->site);
        $event = $events->get($this->eventid);

        if (!is_null($event)){
            $user = $users->get($event->getUserID());
            $name = $user->getName();
            $email = $user->getEmail();
            $in = $event->getClockIn();
            $out = $event->getClockOut();
            $notes = $event->getNotes();
            $id = $this->eventid;
        } else {
            $name = "Error";
            $email = "";
            $in = 0;
            $out = 0;
            $id = 0;
            $notes = "";
        }

        if ($in != 0){
            $inStr = date('Y-m-d\TH:i', $in);
        } else {
            $inStr = "";
        }

        if ($out != 0){
            $outStr = date('Y-m-d\TH:i', $out);
        } else {
            $outStr = "";
        }

        $html = "<div class='row justify-content-center'>";
        $html .= "";

        $filterStart = $this->filterStart;
        $filterEnd = $this->filterEnd;
        $filterUserID = $this->filterUserID;


        $html .= <<<HTML
<form class='col-4 text-center' method="post" action="post/event.php">
    
    <input type="hidden" name="id" value="$id">
    
    <!-- events form filters -->
    <input type="hidden" name="filterStart" value="$filterStart">
    <input type="hidden" name="filterEnd" value="$filterEnd">
    <input type="hidden" name="filterUserID" value="$filterUserID">
    
    <h2 class="h4 mt-2">$name</h2>
    <h3 class="h5 text-secondary">$email</h3>
    
    <div class="row mt-4">
        <div class="col-lg-6 col-12 form-group mt-1">
            <label for="in">Start date and time</label><br>
            <input type="datetime-local" class="form-control" id="in" name="in" value="$inStr">
        </div>
        <div class="col-lg-6 col-12 form-group mt-1">
            <label for="out">End date and time</label><br>
            <input type="datetime-local" class="form-control" id="out" name="out" value="$outStr">
        </div>
    </div>
    
    <div class="form-group mt-2">
        <label for="start">Notes</label><br>
        <textarea class="form-control" name="notes" rows="5">$notes</textarea>
    </div>
    
    <div class="form-group mt-5">
        <button type="submit" class="btn btn-success">Submit</button>
        <input type="button" class="btn btn-danger" onclick="history.back()" value="Cancel"></input>
    </div>
</form>
HTML;

      return $html;

    }



}