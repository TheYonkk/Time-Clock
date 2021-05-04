<?php


namespace TimeClock;


class UserView extends View
{

    private $userid = null;

    public function __construct(Site $site, $get)
    {
        parent::__construct($site);

        if (isset($get["id"]) && !is_null($get["id"])){
            $this->userid = strip_tags($get["id"]);
        }

        if (!is_null($this->userid)){
            $this->setTitle("Edit Account");
        } else {
            $this->setTitle("Create an Account");
        }
    }


    public function present(){

        $users = new Users($this->site);
        $user = $users->get($this->userid);

        $html = "<form method=\"post\" action=\"post/user.php\">";

        if (!is_null($user)){
            $name = $user->getName();
            $email = $user->getEmail();
            $group = $user->getGroup();
            $role = $user->getRole();
            $id = $user->getId();
            $html .= "<input type=\"hidden\" name=\"id\" value=\"$id\">";
        } else {
            $name="";
            $email="";
            $group="";
            $role="";
        }


      $html .= <<<HTML
    <div class="form-group">
        <label for="name">Full name</label>
        <input type="input" class="form-control" id="name"  name="name" placeholder="Sparty McSpartan" value="$name">
    </div>

    <div class="form-group">
        <label for="email">MSU email</label>
        <input type="email" class="form-control" id="email" name="email" area-describedby="emailHelp" placeholder="sparty@msu.edu" value="$email">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    
    <div class="form-group">
        
        <label for="group">Group</label>
        <select class="form-control" id="group" name="group">
HTML;

        // formula as default, too
        if ($group === User::FORMULA || $group === ""){
            $html .= "<option selected>Formula</option>";
        } else {
            $html .= "<option>Formula</option>";
        }

        if ($group === User::BAJA){
            $html .= "<option selected>Baja</option>";
        } else {
            $html .= "<option>Baja</option>";
        }

        if ($group === User::SOLAR){
            $html .= "<option selected>Solar</option>";
        } else {
            $html .= "<option>Solar</option>";
        }

        if ($group === User::OTHER){
            $html .= "<option selected>Other</option>";
        } else {
            $html .= "<option>Other</option>";
        }

        $html .= <<<HTML
        </select>
    </div>  
    
    <div class="form-group">
    <label for="role">Role</label>
        <select class="form-control" id="role" name="role">
HTML;

        // user as default, too
        if ($role === User::USER || $role === ""){
            $html .= "<option selected>User</option>";
        } else {
            $html .= "<option>User</option>";
        }

        if ($role === User::ADMIN){
            $html .= "<option selected>Admin</option>";
        } else {
            $html .= "<option>Admin</option>";
        }


        $html .= <<<HTML
        </select>
    </div> 
    
    <div class="form-group">
        <button type="submit" class="btn btn-success">Submit</button>
        <button type="submit" class="btn btn-danger">Cancel</button>
    </div>
  
</form>
HTML;

      return $html;

    }



}