<?php


namespace TimeClock;


class Events extends Table
{

    /**
     * Constructor
     * @param $site The Site object
     */
    public function __construct(Site $site) {
        parent::__construct($site, "event");
    }


    /**
     * Gets the last event for a user if it exists
     * @param User $user The user to search events fo
     * @return Event|null the last event or null if it does not exist
     */
    public function getLastEvent(User $user){
        $sql = <<<SQL
SELECT * FROM $this->tableName
WHERE userid=?
ORDER BY `in` DESC LIMIT 1
SQL;


        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute(array($user->getId()));
        if($statement->rowCount() === 0) {
            return null;
        }

        return new Event($statement->fetch(\PDO::FETCH_ASSOC));

    }

    /**
     * Update an event in the database
     * @param Event $event the event to update
     */
    public function update(Event $event){

        $sql =<<<SQL
UPDATE $this->tableName
SET userid=?,notes=?,`in`=?,`out`=?
WHERE id=?
SQL;

        // fill the query parameters
        $info = array($event->getUserID(), $event->getNotes(),
            date("Y-m-d H:i:s", $event->getClockIn()));
        if (is_null($event->getClockOut())){
            $info[] = null;
        } else {
            $info[] = date("Y-m-d H:i:s", $event->getClockOut());
        }
        $info[] = $event->getId();


        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        try {
            $ret = $statement->execute($info);
        } catch(\PDOException $e){
            return False;
        }

        if ($ret == False){
            return False;
        } else if($statement->rowCount() === 0) {
            return False;
        } else {
            return True;
        }
    }


    /**
     * Create a time clock event
     * @param User $user The user to create the event for
     * @return null on success or error message if failure
     */
    public function clockIn(User $user)
    {


        // Add a record to the user table
        $sql = <<<SQL
INSERT INTO $this->tableName(userid, notes, `in`)
values(?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);

        try {
            $ret = $statement->execute([$user->getId(), "", date("Y-m-d H:i:s")]);
        } catch(\PDOException $e){
            return false;
        }

        $id = $this->pdo()->lastInsertId();
        return true;



    }

    public function clockOut(User $user){
        return $this->getLastEvent($user);
    }
}