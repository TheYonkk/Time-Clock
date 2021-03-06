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
     * Gets all events given the input SQL filter (if any)
     */
    public function getEvents($start=null, $end=null, $userid=null){

        if (is_null($start)){
            $start = 0;
        }

        if (is_null($end)) {
            $end = time();
        }

        if (is_null($userid)){
            $userid = "*";
        }


        $startStr = date("Y-m-d H:i", $start);
        $endStr = date("Y-m-d H:i", $end);


        $users = new Users($this->site);
        $usersTable = $users->getTableName();

        $sql = <<<SQL
SELECT $this->tableName.id,$usersTable.id as userid,notes,`in`,`out`
FROM $this->tableName
INNER JOIN $usersTable
ON userid = $usersTable.id
WHERE (`in` >= ? ) AND (`in` <= ? OR `out` <= ? )
SQL;

        $inputs = array($startStr, $endStr, $endStr);

        if ($userid != "*"){
            $sql .= " AND ($usersTable.id = ?) ";
            $inputs[] = $userid;
        }

        $sql .= " ORDER BY `in` DESC";


        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute($inputs);
        if($statement->rowCount() === 0) {
            return false;
        }


        //Fetch all of the rows from our MySQL table.
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);


        $events = [];
        foreach ($rows as $row) {
            $events[] = new Event($row);
        }



        return $events;
    }



    /**
     * Update an event in the database
     * @param Event $event the event to update
     */
    public function update(Event $event){

        $sql =<<<SQL
UPDATE $this->tableName
SET userid=?,notes=?,`in`=?,`out`=?,`forgot`=?
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
        $info[] = $event->didForget(); // forgot to clock out?


        $info[] = $event->getId();

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        try {
            $ret = $statement->execute($info);
        } catch(\PDOException $e){
            print($e);
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
        return $id;

    }

    /**
     * Returns an array of tuples. Each tuple consists of a the user and
     * the duration of their current clock session in seconds
     * @param bool $sort_alphabetical Sort alphabetical or by time
     */
    public function getClockedInUsers($sort_alphabetical=False){
        $users = new Users($this->site);

        $currentUsers = array();

        foreach ($users->getUsers() as $user){
            $lastEvent = $this->getLastEvent($user);

            // they have a last event and the end time is non-existent
            if (!is_null($lastEvent) && is_null($lastEvent->getClockOut())){

                $duration = time() - $lastEvent->getClockIn();
                $currentUsers[$user->getName()] = array($lastEvent->getId(), $duration);

            }
        }

        if ($sort_alphabetical){
            ksort($currentUsers);
        } else {
            arsort($currentUsers);
        }

        return $currentUsers;
    }

    public function downloadDateRange($start, $end, $userid=null){

        $startStr = date("Y-m-d H:i", $start);
        $endStr = date("Y-m-d H:i", $end);

        $users = new Users($this->site);
        $usersTable = $users->getTableName();

        $sql = <<<SQL
SELECT name,email,`group`,`in`,`out`,notes,forgot
FROM $this->tableName
INNER JOIN $usersTable
ON userid = $usersTable.id
WHERE (`in` >= ? ) AND (`in` <= ? OR `out` <= ? )
SQL;

        $params = array($startStr, $endStr, $endStr);

        # add user id filter if desired
        if (!is_null($userid)){
            $sql .= " AND userid = ?";
            $params[] = $userid;
        }


        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute($params);
        if($statement->rowCount() === 0) {
            return false;
        }


        // Fetch all of the rows from our MySQL table.
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // Get the column names.
        $columnNames = array();
        if(!empty($rows)){
            // We only need to loop through the first row of our result
            // in order to collate the column names.
            $firstRow = $rows[0];
            foreach($firstRow as $colName => $val){
                $columnNames[] = $colName;
            }
        }

        //Setup the filename that our CSV will have when it is downloaded.
        $startF = date("Y-m-d_H-i", $start);
        $endF = date("Y-m-d_H-i", $end);
        $fileName = "timeclock_$startF" . "_to_" . "$endF.csv";

        //Set the Content-Type and Content-Disposition headers to force the download.
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        //Open up a file pointer
        $fp = fopen('php://output', 'w');

        //Start off by writing the column names to the file.
        fputcsv($fp, $columnNames);

        //Then, loop through the rows and write them to the CSV file.
        foreach ($rows as $row) {
            $row['group'] = User::getGroupStr($row['group']);
            fputcsv($fp, $row);
        }

        //Close the file pointer.
        fclose($fp);

        return true;
    }

    public function getEarliestDate(){
        $sql = <<<SQL
SELECT `in`,`out`
from $this->tableName
ORDER BY `in` ASC LIMIT 1
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $now = time();

        try {
            $ret = $statement->execute(array());
        } catch(\PDOException $e){
            return $now;
        }

        if ($ret == False){
            return $now;
        } else if($statement->rowCount() === 0) {
            return $now;
        } else {
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
            return strtotime( $row["in"] );
        }
    }


    /**
     * Get an event based on the id
     * @param $id ID of the event
     * @return User object if successful, null otherwise.
     */
    public function get($id) {

        $sql =<<<SQL
SELECT * from $this->tableName
where id=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute(array($id));
        if($statement->rowCount() === 0) {
            return null;
        }

        return new Event($statement->fetch(\PDO::FETCH_ASSOC));

    }


    /**
     * Permanently deletes an event
     * @param int $id event id to delete
     * @return true upon successful deleting
     */
    public function delete($id){
        $sql =<<<SQL
DELETE FROM $this->tableName
WHERE id=?
SQL;

        $info = array($id);

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        try {
            $ret = $statement->execute($info);
        } catch(\PDOException $e){
            return False;
        }

        // if the delete failed, 0 rows will be affected
        return $statement->rowCount() !== 0;


    }


}