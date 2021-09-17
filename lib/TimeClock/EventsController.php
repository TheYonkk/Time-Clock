<?php


namespace TimeClock;


class EventsController
{

    public function __construct($site, $post){
        $this->site = $site;

        $start = strip_tags($post['filterStart']);
        $end = strip_tags($post['filterEnd']);
        $userid = strip_tags($post['filterUserID']);

        # perform transformations to get the data in the form that we need
        $startStr = urlencode(date('Y-m-d\TH:i', $start));
        $endStr = urlencode(date('Y-m-d\TH:i', $end));


        $this->redirect = $site->getRoot() . "/events.php?start=$startStr" . "&end=$endStr" . "&user=$userid";

        if (isset($post['download'])) {


            if ($end < $start){
                return;
            }

            echo "user ID: " . $userid;

            if ($userid === "" or $userid === "*"){
                $userid = null;
            }

            $events = new Events($site);
            $success = $events->downloadDateRange($start, $end, $userid);
            if ($success) {
                $this->download = true;
            }

        }


    }

    public function getRedirect(){
        return $this->redirect;
    }

    public function hasDownload(){
        return $this->download;
    }

    private $redirect;
    private $site;
    private $download=false;

}