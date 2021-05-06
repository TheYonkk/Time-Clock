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
        $earliest = date("Y-m-d H:i", $earliest);
        $latest = date("Y-m-d H:i");


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
            <div class="col-6">
            
                <div class="h-100 p-5 bg-light border rounded-3">
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
        
            </div>
        </div>
        
        </div>
    
    </div>
</main>
HTML;

        return $html;
    }

}