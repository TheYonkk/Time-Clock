<?php


namespace TimeClock;


class LoginKeys extends Table
{

    /**
     * Constructor
     * @param $site The Site object
     */
    public function __construct(Site $site) {
        parent::__construct($site, "loginkey");
    }


    /** Generates a new login key and inserts it into the table
     * @param $expiration integer - The PHP time for experation
     * @return string
     */
    public function generateNewKey($expiration) : string {

        $hash = Users::randomSalt(64);
        $expiration = date("Y-m-d H:i", $expiration);


        $sql =<<<SQL
INSERT INTO $this->tableName(`key`, `created`, `expiration`)
VALUES (?, ?, ?);
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute([
            $hash, date("Y-m-d H:i"), $expiration
        ]);

        # should be the same as $hash
        return $hash;

    }

    /**
     * @return The active key string if there's one active or null if no active keys
     */
    public function getActiveKey(){

        $current = date("Y-m-d H:i");

        $sql =<<<SQL
SELECT `key` from $this->tableName
WHERE `expiration` > ?
ORDER BY `created` DESC
LIMIT 1
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute([$current]);

        if ($statement->rowCount() == 0){
            return null;
        } else {
            return $statement->fetch()["key"];
        }

    }


    /**
     * @return mixed|null returns the expiration date in PHP timestamp of the active key. None if no active keys.
     */
    public function getExpirationDate(){

        $current = date("Y-m-d H:i");

        $sql =<<<SQL
SELECT `expiration` from $this->tableName
WHERE `expiration` > ?
ORDER BY `created` DESC
LIMIT 1
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute([$current]);

        if ($statement->rowCount() == 0){
            return null;
        } else {
            return strtotime($statement->fetch()["expiration"]);
        }

    }


}


