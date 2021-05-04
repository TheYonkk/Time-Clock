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

        if (!is_null($row["out"])){
            strtotime( $row["out"] );
        }

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
     * @param time|null $out
     */
    public function setClockOut($out)
    {
        $this->out = $out;
    }





    private $id;  // event ID
    private $userID;  // user this event belongs to
    private $notes;
    private $in;
    private $out = null;



}