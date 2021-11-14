<?php


namespace TimeClock;


class Event
{

    /**
     * Event constructor.
     * @param $row array A row as returned from the Events table
     *  position 1: event ID
     *  position 2:
     */
    public function __construct($row){

        $this->id = $row["id"];
        $this->userID = $row["userid"];
        $this->notes = $row["notes"];
        $this->in = strtotime( $row["in"] );
        $this->forgot = $row['forgot'];

        if (!is_null($row["out"])){
            $this->out = strtotime( $row["out"] );
        }

    }

    public function __toString(): string
    {
        $arr = array("id"=>$this->id, "userID"=>$this->userID, "notes"=>$this->notes,
            "in"=>$this->getClockInStr(), "out"=>$this->getClockOutStr());
        return print_r($arr);

    }

    public function getClockInStr() : string {
        return date("Y-m-d H:i:s", $this->in);
    }

    public function getClockOutStr() : string {
        if (is_null($this->out)){
            return "";
        } else {
            return date("Y-m-d H:i:s", $this->out);
        }
    }

    /**
     * @return bool
     */
    public function didForget()
    {
        return $this->forgot;
    }

    /**
     * @param bool $forgot
     */
    public function setForgot(bool $forgot)
    {
        $this->forgot = $forgot;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return time
     */
    public function getClockIn()
    {
        return $this->in;
    }

    /**
     * @param time $in
     */
    public function setClockIn($in)
    {
        $this->in = $in;
    }

    /**
     * @return time
     */
    public function getClockOut()
    {
        return $this->out;
    }

    /**
     * @param time|null $out if out is null, the current time is used
     */
    public function setClockOut($out=null)
    {
        if (is_null($out)){
            $out = time();
        }
        $this->out = $out;
    }

    /**
     * Returns a string like "420 days, 69 minutes, 13 seconds"
     * @return string A nicely formatted string with commas!
     */
    public function getDurationStr(){

        if (is_null($this->out)){
            return "";
        }

        $duration = $this->out - $this->in;

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

        return $timestr;
    }

    public static function PHPToSQLTime($timestamp){
        return date("Y-m-d H:i", $timestamp);
    }



    private $id;  // event ID
    private $userID;  // user this event belongs to
    private $notes;
    private $in;
    private $out = null;
    private $forgot = false;  # forgot to clock in/out?



}