<?php


namespace TimeClock;


class UserView extends View
{

    public function __construct(Site $site)
    {
        parent::__construct($site);
        $this->setTitle("Create an Account");
    }


    public function present(){

      $html = <<<HTML
<form method="post" action="post/user.php">

    <div class="form-group">
        <label for="name">Full name</label>
        <input type="input" class="form-control" id="name"  name="name" placeholder="Sparty McSpartan">
    </div>

    <div class="form-group">
        <label for="email">MSU email</label>
        <input type="email" class="form-control" id="email" name="email" area-describedby="emailHelp" placeholder="sparty@msu.edu">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    
    <div class="form-group">
        
        <label for="group">Group</label>
        <select class="form-control" id="group" name="group">
          <option selected>Formula</option>
          <option>Other</option>
        </select>
    </div>  
    
    <div class="form-group">
    <label for="role">Role</label>
        <select class="form-control" id="role" name="role">
          <option selected>User</option>
          <option>Admin</option>
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