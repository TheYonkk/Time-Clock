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

        $root = $site->getRoot();
        $this->addLink("$root/admin.php", "Home");
        $this->addLink("$root/events.php", "Events");
        $this->addLink("$root/users.php", "Users");
        $this->addLink("$root/user.php", "New user", True);
        $this->addLink("$root/login.php", "Log out");
    }


    public function present(){

        $users = new Users($this->site);
        $user = $users->get($this->userid);

        $html = "<main class='mt-auto py-3'><div class='container'><div class='row justify-content-center'><form class='col-6' method=\"post\" action=\"post/user.php\">";

        if (!is_null($user)){
            $name = $user->getName();
            $email = $user->getEmail();
            $group = $user->getGroup();
            $role = $user->getRole();
            $id = $user->getId();
            $apid = $user->getApid();
            $html .= "<input type=\"hidden\" name=\"id\" value=\"$id\">";
        } else {
            $name="";
            $email="";
            $group="";
            $role="";
            $apid="";
        }


      $html .= <<<HTML
    <div class="form-group py-2">
        <label for="name">Full name</label>
        <input type="input" class="form-control" id="name"  name="name" placeholder="Sparty McSpartan" value="$name">
    </div>
    
    <div class="form-group py-2">
        <label for="name">APID <span class="text-secondary">(Without the leading 'A' or '1')</span></label>
        <input type="input" class="form-control" id="apid"  name="apid" placeholder="12345678" value="$apid">
    </div>

    <div class="form-group py-2">
        <label for="email">MSU email</label>
        <input type="email" class="form-control" id="email" name="email" area-describedby="emailHelp" placeholder="sparty@msu.edu" value="$email">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    
    <div class="form-group py-2">
        
        <label for="group">Group</label>
        <select class="form-control" id="group" name="group">
HTML;

        foreach (User::getGroups() as $userGroup)
        {
          print($userGroup . "  ");
          if ($group === $userGroup){
            $html .= "<option selected>" . User::getGroupStr($userGroup) . "</option>";
          } else {
            $html .= "<option>" . User::getGroupStr($userGroup) . "</option>";
          }
        }


        $html .= <<<HTML
        </select>
    </div>  
    
    <div class="form-group py-2">
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
    
    <div class="form-group  my-5">
        <button type="submit" class="btn btn-success">Submit</button>
        <button type="submit" class="btn btn-danger">Cancel</button>
    </div>
  
</form>
</div>
</div>
</main>
HTML;

      return $html;

    }



}