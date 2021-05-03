<?php


namespace TimeClock;


class Validators extends Table
{

    /**
     * Constructor
     * @param $site The Site object
     */
    public function __construct(Site $site) {
        parent::__construct($site, "validator");
    }


    /**
     * Generate a random validator string of characters
     * @param $len Length to generate, default is 32
     * @return Validator string
     */
    public function createValidator($len = 32) {
        $bytes = openssl_random_pseudo_bytes($len / 2);
        return bin2hex($bytes);
    }

    /**
     * Create a new validator and add it to the table.
     * @param $userid User this validator is for.
     * @return The new validator.
     */
    public function newValidator($userid) {
        $validator = $this->createValidator();

        $table = $this->getTableName();

        // Write to the table
        $sql = <<<SQL
INSERT INTO $table(validator, userid, date)
values(?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$validator, $userid, date("Y-m-d H:i:s")]);

        return $validator;
    }


    /**
     * Determine if a validator is valid. If it is,
     * return the user ID for that validator.
     * @param $validator Validator to look up
     * @return User ID or null if not found.
     */
    public function get($validator) {
        $sql = <<<SQL
SELECT userid from $this->tableName
where validator=?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$validator]);

        if($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC)['userid'];
    }

    /**
     * Remove any validators for this user ID.
     * @param $userid The USER ID we are clearing validators for.
     */
    public function remove($userid) {
        $sql = <<<SQL
DELETE FROM $this->tableName
WHERE userid=?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$userid]);


    }


}