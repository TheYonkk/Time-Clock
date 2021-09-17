<?php


namespace TimeClock;


class AdminController
{

    public function __construct($site, $post){
        $this->site = $site;
        $this->redirect = $site->getRoot() . "/admin.php";

        if (isset($post['Download'])) {

            $start = strtotime(strip_tags($post['start']));
            $end = strtotime(strip_tags($post['end']));

            if ($end < $start){
                return;
            }

            $events = new Events($site);
            $success = $events->downloadDateRange($start, $end);
            if ($success) {
                $this->download = true;
            }

        # generate a login link
        } elseif (isset($post['Generate'])){

            $expire = strtotime(strip_tags($post["expire"]));

            $loginkeys = new LoginKeys($site);
            $hash = $loginkeys->generateNewKey($expire);

            $this->redirect = $site->getRoot() . "/qr.php?key=$hash";

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