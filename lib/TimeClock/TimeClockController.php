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


        // if neither in or out are set, do nothing
        if (!isset($post['clock'])){
            $this->result = json_encode(['ok' => false, 'message' => "You must select an option."]);
            return;
        }

        $user = $session[User::SESSION_NAME];
        $clock = strip_tags($post['clock']);
        $events = new Events($site);

        if ($clock === "in"){

            // check to see if the user has an open session without a clock-out
            $lastEvent = $events->getLastEvent($user);

            // if the last event does not have a clock out and the override option is not set
            if (!is_null($lastEvent) && is_null($lastEvent->getClockOut()) && !isset($post['override'])){
                $this->result = json_encode(['ok' => false, 'enableOverride' => true, 'message' => 'You have an open time session 
                that was started on <strong>' . date("m/d/Y", $lastEvent->getClockIn()) . "</strong> at <strong>" .
                    date("h:i A", $lastEvent->getClockIn()) . "</strong>! Did you forget to clock out?"]);
                return;
            }

            $success = $events->clockIn($user);

            if ($success) {
                $this->result = json_encode(['ok' => true, 'message' => 'Clock in successful!']);
                return;

            } else {
                $this->result = json_encode(['ok' => false, 'enableOverride' => false, 'message' => 'There was a problem clocking you in.']);
                return;
            }

        } else if ($clock === "out"){

            $lastEvent = $events->getLastEvent($user);

            // if there was a last event and the last event does not have a clock out time
            if (!is_null($lastEvent) && is_null($lastEvent->getClockOut())){
                $lastEvent->setClockOut(); // clock out at the current time
                $success = $events->update($lastEvent);

                if ($success) {
                    $this->result = json_encode(['ok' => true, 'message' => 'Clock out successful!']);
                    return;
                } else {
                    $this->result = json_encode(['ok' => false,  'enableOverride' => false, 'message' => 'There was a problem clocking you out.']);
                    return;
                }

            // no active session to clock out
            } else {
                $this->result = json_encode(['ok' => false, 'enableOverride' => false, 'message' => 'You do not have an active session!']);
                return;
            }

        }

        $this->result = json_encode(['ok' => false,  'enableOverride' => false, 'message' => 'Uncaught error!']);


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