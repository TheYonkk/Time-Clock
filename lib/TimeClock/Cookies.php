<?php


namespace TimeClock;


class Cookies extends Table
{

  /**
   * Constructor
   * @param $site The Site object
   */
  public function __construct(Site $site) {
    parent::__construct($site, "cookie");
  }

  /**
   * Generate a random salt string of characters for password salting
   * @param $len Length to generate, default is 16
   * @return Salt string
   */
  public static function randomSalt($len = 16) {
    $bytes = openssl_random_pseudo_bytes($len / 2);
    return bin2hex($bytes);
  }

  /**
   * Create a new cookie token
   * @param $user User ID to create token for
   * @return New 32 character random string
   */
  public function create($user) {

    $table = $this->getTableName();

    $sql = <<<SQL
INSERT INTO $table(`userid`, `salt`, `hash`, `date`)
values(?, ?, ?, ?)
SQL;

    $salt = $this->randomSalt();
    $token = $this->randomSalt(32);
    $hash = hash("sha256", $token . $salt);
    $date = date("Y-m-d H:i:s");

    $stmt = $this->pdo()->prepare($sql);
    $ret = $stmt->execute([$user, $salt, $hash, $date]);

    return $token;

  }

  /**
   * Validate a cookie token
   * @param $user User ID
   * @param $token Token
   * @return null|string If successful, return the actual
   *   hash as stored in the database.
   */
  public function validate($user, $token) {

    $table = $this->getTableName();

    $sql = <<<SQL
SELECT salt,hash
from $table
where userid=?
SQL;

    echo("validating\n");

    $stmt = $this->pdo()->prepare($sql);
    $ret = $stmt->execute([$user]);

    if ($ret == False){
      return null;
    } else if($stmt->rowCount() === 0) {
      return null;
    }

    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
      $salt = $row['salt'];
      $hash = $row['hash'];

        echo("salt: " . $salt . "\n");
        echo("hash: " . $hash . "\n");
        echo("calculated: " . hash("sha256", $token . $salt) . "\n");

      if ($hash === hash("sha256", $token . $salt)) {
        return $hash;
      }
    }

    return null;

  }

  /**
   * Delete a hash from the database
   * @param $hash Hash to delete
   * @return bool True if successful
   */
  public function delete($hash) {

    $table = $this->getTableName();

    $sql = <<<SQL
delete from $table
where hash=?
SQL;


    $stmt = $this->pdo()->prepare($sql);


    try {
      $ret = $stmt->execute(array($hash));
    } catch(\PDOException $e){
      return false;
    }

    return $ret;

  }

}