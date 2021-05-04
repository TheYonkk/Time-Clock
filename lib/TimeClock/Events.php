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
     * Create a time clock event
     * @param User $user The user to create the event for
     * @return null on success or error message if failure
     */
    public function ClockIn(User $user)
    {


        // Add a record to the user table
        $sql = <<<SQL
INSERT INTO $this->tableName(userid, notes, `in`)
values(?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute();

        try {
            $ret = $statement->execute([$user->getId(), "", date("Y-m-d H:i:s")]);
        } catch(\PDOException $e){
            return "There was an error inserting a new record into the database.";
        }

        $id = $this->pdo()->lastInsertId();


    }
}