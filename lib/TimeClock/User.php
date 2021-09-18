<?php


namespace TimeClock;


class User
{

  const ADMIN = "A";
  const USER = "U";

  const FORMULA = "F";
  const BAJA = "B";
  const SOLAR = "S";
  const OTHER = "O";

  const SESSION_NAME = 'user';


  /**
   * Determine if user is a staff member
   * @return bool True if user is a staff member
   */
  public function isStaff() {
    return $this->role === self::ADMIN;
  }


  /**
   * Constructor
   * @param $row Row from the user table in the database
   */
  public function __construct($row) {
    $this->id = $row['id'];
    $this->email = $row['email'];
    $this->name = $row['name'];
    $this->role = $row['role'];
    $this->group = $row['group'];
    $this->apid = $row['apid'];
  }


  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }


  /**
   * @return mixed
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getRole()
  {
    return $this->role;
  }

  public function getGroup()
  {
    return $this->group;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }


  /**
   * @param mixed $role
   */
  public function setRole($role)
  {
      $this->role = $role;
  }		// User role

  /**
   * @param mixed $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }

    /**
     * @return mixed
     */
    public function getApid()
    {
        return $this->apid;
    }

    /**
     * @param mixed $apid
     */
    public function setApid($apid)
    {
        $this->apid = $apid;
    }




  private $id;		// The internal ID for the user
  private $email;		// Email address
  private $name; 		// full name
  private $role;
  private $group;
  private $apid;

}