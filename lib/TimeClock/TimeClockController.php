<?php


namespace TimeClock;


class TimeClockController
{

    /**
     * LoginController constructor.
     * @param Site $site The Site object
     * @param array $session $_SESSION
     * @param array $post $_POST
     */
    public function __construct(Site $site, array &$session, array $post) {

        $root = $site->getRoot();

        // redirect to index
        $this->redirect = "$root/";

        // if neither in or out are set, do nothing
        if (!isset($post['clock'])){
            return;
        }

        $user = $session[User::SESSION_NAME];
        $clock = strip_tags($post['clock']);
        $events = new Events($site);

        if ($clock === "in"){
            $success = $events->clockIn($user);


        } else if ($clock === "out"){

            $lastEvent = $events->getLastEvent($user);

            // if there was a last event and the last event does not have a clock out time
            if (!is_null($lastEvent) && is_null($lastEvent->getClockOut())){
                $lastEvent->setClockOut(); // clock out at the current time
                $success = $events->update($lastEvent);
            }

        }


    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }


    private $redirect;	// Page we will redirect the user to.

}