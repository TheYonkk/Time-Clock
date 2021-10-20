<?php


namespace TimeClock;


class AdminView extends View
{

    public function __construct(Site $site)
    {
        parent::__construct($site);

        $this->setTitle("Site Administration");
        $this->setBlurb("Welcome to the Time Clock administration landing page. Here, you can generate reports, view currently clocked-in users, and access user controls.");

        $root = $site->getRoot();
        $this->addLink("$root/admin.php", "Home", True);
        $this->addLink("$root/events.php", "Events");
        $this->addLink("$root/users.php", "Users");
        $this->addLink("$root/user.php", "New user");
        $this->addLink("$root/login.php", "Log out");
    }

    /**
     * Present the admin lansing form
     * @return string HTML
     */
    public function present() {

        $events = new Events($this->site);
        $earliest = $events->getEarliestDate();
        $earliest = date("Y-m-d\TH:i", $earliest);
        $latest = date("Y-m-d\TH:i");

        # now + one day in seconds
        $dayLater = date("Y-m-d\TH:i", time() + 86400);


        $loginKeys = new LoginKeys($this->site);
        $key = $loginKeys->getActiveKey();
        $clockLink = $this->site->getRoot() . "?key=" . $key;
        $QRLink = $this->site->getRoot() . "/qr.php?key=" . $key;
        $QRExpire = $loginKeys->getExpirationDate();
        $QRExpireStr = date("l, F j, Y", $QRExpire) . " at " . date("g:i A", $QRExpire);

        $linksExpire = "<p>The links below will expire on $QRExpireStr.</p>";

        if (!is_null($key)) {
            $activeLinks = <<<HTML
$linksExpire
<div class="form-group py-2">
    <a class="btn btn-outline-primary mt-1" href="$clockLink" target="_blank">Timeclock Link</a>
    <a class="btn btn-outline-info mt-1" href="$QRLink" target="_blank">QR Page Link</a>
</div>
HTML;
        } else {
            $activeLinks = "<p>There are currently no active links.</p>";
        }



        $html = <<<HTML
<main class="mt-auto py-3">
    <div class="container">
    
        <div class="row">
            <div class="col">
                <div id="message"></div>
            </div>
        </div>
        
        <div class="container" id="admin">
        
            <div class="row">
            
                <!-- Basic report generator -->
                <div class="col-md-6 my-1">
                
                    <div class="row m-1">
                
                        <div class="h-50 p-5 mb-2 bg-light border rounded-3">
                            <form id="login-link" method="post" action="post/admin.php">
                                <h2>Generate a Time Clock QR-Code</h2>
                                <p>Create a QR-code Time Clock link with an expiry date. Generating this link will invalidate existing links. Default lifespan is 24 hours.</p>
                                
                                <div class="form-group py-2">
                                    <label for="expire">Expiration date and time</label><br>
                                    <input type="datetime-local" class="form-control w-50" id="expire" name="expire" value="$dayLater">
                                </div>
                                <input class="btn btn-outline-secondary" type="submit" name="Generate" value="Generate">
                            </form>
                        </div>
                        
                    </div> <!-- /row -->
                    <div class="row m-1">
                
                        <div class="h-50 p-5 bg-light border rounded-3">
                            <form id="basic-report" method="post" action="post/admin.php">
                                <h2>Generate a basic report</h2>
                                <p>Download raw shop usage data between a range of dates.</p>
                                <div class="form-group py-2">
                                    <label for="start">Start date and time</label><br>
                                    <input type="datetime-local" class="form-control w-50" id="start" name="start" value="$earliest" min="$earliest" max="$latest">
                                </div>
                                <div class="form-group pb-4">
                                    <label for="start">End date and time</label><br>
                                    <input type="datetime-local" class="form-control w-50" id="end" name="end" value="$latest" min="$earliest" max="$latest">
                                </div>
                                <input class="btn btn-outline-secondary" type="submit" name="Download" value="Download">
                            </form>
                        </div>
                        
                    </div> <!-- /row -->
            
                </div>
                
                
                <div class="col-md-6 my-1">
                
                    <!-- links -->
                    <div class="row m-1">
                
                        <div class="h-50 p-5 mb-2 bg-light border rounded-3">
                            <h2>Active Links</h2>
                            <p>Timeclock links that are currently active</p>
                            
                            $activeLinks
  
                        </div>
                        
                    </div> <!-- /row -->
                
                   <!-- report generation --> 
                    <div class="row m-1">
                        <div class="h-100 p-5 bg-light border rounded-3">
                            <h2>Current usage</h2>
                            <p>
HTML;
        $num_users = count($events->getClockedInUsers());
        if ($num_users === 1){
            $html .= "There is 1 user currently clocked in.";
        } else {
            $html .= "There are " . $num_users . " users currently clocked in.";
        }

        $html .= <<<HTML
                            </p>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                      <th scope="col"></th>
                                      <th scope="col">User</th>
                                      <th scope="col">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
HTML;
        $events = new Events($this->site);
        foreach($events->getClockedInUsers() as $name => $info){

            $duration = $info[1];
            $id = $info[0];
//            $user = $pair[0];
//            $duration = $pair[1];

//            $name = $user->getName();
            $timestr = "";
            $continue = false;

            $days = gmdate("z", $duration);
            if ($days !== "0"){
                $s = ($days === "01")?'':'s';
                $timestr .= $days . " day$s, ";
                $continue = true;
            }

            $hours = gmdate("G", $duration);
            if ($hours !== "0" or $continue){
                $s = ($hours === "1")?'':'s';
                $timestr .= $hours . " hour$s, ";
                $continue = true;
            }

            $mins = gmdate("i", $duration);
            if ($mins !== "00" or $continue){
                $s = ($mins === "01")?'':'s';
                $timestr .= $mins . " minute$s, ";
                $continue = true;
            }

            $secs = gmdate("s", $duration);
            if ($secs !== "00" or $continue){
                $s = ($secs === "01")?'':'s';
                $timestr .= $secs . " second$s";
            }

            $root = $this->site->getRoot();
            $editbtn = "<a class='btn btn-outline-secondary py-0' href='$root/event.php?id=$id'>Edit</a>";

            $html .= "<tr><td>$editbtn</td><td>$name</td><td>$timestr</td></tr>";
        }

        $html .= <<<HTML
                                </tbody>
                            </table>     
                        </div> <!-- box -->
                    </div> <!-- row --> 
                </div> <!-- col --> 
            </div> <!-- row --> 
        </div> <!-- container --> 
    </div> <!-- container --> 
</main>
HTML;

        return $html;
    }

}