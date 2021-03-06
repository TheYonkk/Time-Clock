<?php


namespace TimeClock;


class Users extends Table
{

    /**
     * Constructor
     * @param $site The Site object
     */
    public function __construct(Site $site) {
        parent::__construct($site, "user");
    }


    /**
     * Test for a valid login.
     * @param $email User email
     * @param $password Password credential
     * @return User object if successful, null otherwise.
     */
    public function login($email, $password) {

        $sql =<<<SQL
SELECT * from $this->tableName
where email=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute([$email]);
        if($statement->rowCount() === 0) {
            return null;
        }

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        // Get the encrypted password and salt from the record
        $hash = $row['password'];
        $salt = $row['salt'];

        // Ensure it is correct
        if($hash !== hash("sha256", $password . $salt)) {
            return null;
        }

        return new User($row);

    }

    /**
     * Determine if a user exists in the system.
     * @param $email An email address.
     * @return true if $email is an existing email address
     */
    public function exists($email) {
        $sql =<<<SQL
SELECT * from $this->tableName
where email=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute(array($email));
        if($statement->rowCount() === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Create a new user.
     * @param User $user The new user data
     * @param Email $mailer An Email object to use
     * @return null on success or error message if failure
     */
    public function add(User $user, Email $mailer) {


        // Ensure we have no duplicate email address
        if($this->exists($user->getEmail())) {
            return "Email address already exists.";
        }

        // Add a record to the user table
        $sql = <<<SQL
INSERT INTO $this->tableName(email, name, role, `group`, `apid`)
values(?, ?, ?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute([
            $user->getEmail(), $user->getName(), $user->getRole(), $user->getGroup(), $user->getApid()
        ]);
        $id = $this->pdo()->lastInsertId();

        // Create a validator and add to the validator table
        $validators = new Validators($this->site);
        $validator = $validators->newValidator($id);


        // Send email with the validator in it
        $link = "https://cse.msu.edu"  . $this->site->getRoot() .
            '/password-validate.php?v=' . $validator;

        $from = $this->site->getEmail();
        $name = $user->getName();
        $root = $this->site->getRoot();

        $subject = "Confirm your email";
        $message = <<<MSG
<html>
<head>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
<div id="email">
<h1>Greetings, $name,</h1>

<p>An account has been created for you on the MSU Formula Racing shop time clock, <a href="https://cse.msu.edu$root">https://cse.msu.edu$root</a>.</p>

<p>Since there are over 100 new members and only a handful of leaders, the time clock will be used as a log for your participation. To get credit, 
you must swipe your MSU student ID upon entering and leaving the shop. For machining at the engineering building, 
the process will be slightly different and will be explained at a later date.</p>

<p>This system is brand new and is continuously under development. If you have any trouble, please contact the team's
software lead, Dave Yonkers (<a href="mailto:yonkers4@msu.edu">yonkers4@msu.edu</a>).</p>

<p><a class="btn btn-primary" href="$link">Finish account setup.</a></p>
<p class="text-secondary fst-italic">Please note: this link is unique to you. Your passwords are encrypted and can not been seen by 
anyone.</p>
</div>
</body>

<style>

html, body {
  display: flex;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}

#email {
    width: 100%;
    max-width: 500px;
    padding: 15px;
    margin: auto;
}
  
</style>
</html>
MSG;
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso=8859-1\r\nFrom: $from\r\n";
        $mailer->mail($user->getEmail(), $subject, $message, $headers);


    }



    /**
     * Generate a password reset for a user
     * @param int $userID the user ID
     * @param Email $mailer An Email object to use
     * @return null on success or error message if failure
     */
    public function passwordResetRequest($userID, $mailer){

        $user = $this->get($userID);

        // Create a validator and add to the validator table
        $validators = new Validators($this->site);
        $validator = $validators->newValidator($userID);

        // Send email with the validator in it
        $link = "https://cse.msu.edu"  . $this->site->getRoot() .
            '/password-validate.php?v=' . $validator;

        $from = $this->site->getEmail();
        $name = $user->getName();
        $root = $this->site->getRoot();

        $subject = "Reset your password";
        $message = <<<MSG
<html>
<head>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
<div id="email">
<h1>Greetings, $name,</h1>

<p>The Timeclock has generated a password reset link for you. Click the link below to set a new password for your account.</p>

<p><a class="btn btn-primary" href="$link">Reset password.</a></p>
<p class="text-secondary">Please note: this link is unique to you. If you did not request a password reset, you may disregard this email.</p>
</div>
</body>

<style>

html, body {
  display: flex;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}

#email {
    width: 100%;
    max-width: 500px;
    padding: 15px;
    margin: auto;
}
  
</style>
</html>
MSG;
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso=8859-1\r\nFrom: $from\r\n";
        $mailer->mail($user->getEmail(), $subject, $message, $headers);

    }




    /**
     * Get a user based on the id
     * @param $id ID of the user
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

        return new User($statement->fetch(\PDO::FETCH_ASSOC));

    }

    /**
     * Get a user based on the id
     * @param $email Email of the user
     * @return User object if successful, null otherwise.
     */
    public function getByEmail($email) {

        $sql =<<<SQL
SELECT * from $this->tableName
where email=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        $statement->execute(array($email));
        if($statement->rowCount() === 0) {
            return null;
        }

        return new User($statement->fetch(\PDO::FETCH_ASSOC));

    }


    /**
     * Modify a user record based on the contents of a User object
     * @param User $user User object for object with modified data
     * @return true if successful, false if failed or user does not exist
     */
    public function update(User $user) {
        $sql =<<<SQL
UPDATE $this->tableName
SET email=?,name=?,role=?,`group`=?, `apid`=?
WHERE id=?
SQL;

        $info = array($user->getEmail(), $user->getName(), $user->getRole(),
            $user->getGroup(), $user->getApid(), $user->getId());

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
     * Permanently deletes a user
     * @param int $userid to delete
     * @return true upon successful deleting
     */
    public function delete($userid){
        $sql =<<<SQL
DELETE FROM $this->tableName
WHERE id=?
SQL;

        $info = array($userid);

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


    /**
     * Set the password for a user
     * @param $userid The ID for the user
     * @param $password New password to set
     */
    public function setPassword($userid, $password) {

        $salt = $this->randomSalt();
        $hash = hash('sha256', $password . $salt);


        $sql = <<<SQL
UPDATE $this->tableName
SET password=?,salt=?
WHERE id=?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$hash, $salt, $userid]);

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


    public function getUsers(){


        $sql = <<<SQL
SELECT *
from $this->tableName
ORDER BY `name` ASC
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);

        try {
            $ret = $statement->execute(array());
        } catch(\PDOException $e){
            return array();
        }

        if ($ret == False){
            return array();
        } else if($statement->rowCount() === 0) {
            return array();
        } else {
            $results = array();
            foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $user = new User($row);
                $results[] = $user;
            }
            return $results;
        }
    }

}


