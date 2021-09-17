<?php


namespace TimeClock;


class QRView extends View
{

    public function __construct($site, &$session, $get){
        parent::__construct($site);

        $this->session = $session;
        $this->get = $get;

    }

    public function present() {

        $html = "";

        $root = $this->site->getRoot();
        $base = $this->site->getBase();

        if (isset($this->get["key"]) and !is_null($this->get["key"])){
            $key = strip_tags($this->get["key"]);
        } else {
            $key = null;
        }



        if (!is_null($key)) {

            $QRPixels = 300;

            $loginLink = "$base" . "$root?key=$key";
            $image = "https://chart.googleapis.com/chart?cht=qr&chs=" . $QRPixels . "x" . $QRPixels . "&chl=" . urlencode($loginLink);

            $loginkeys = new LoginKeys($this->site);
            $lastHash = $loginkeys->getActiveKey();

            $html .= <<<HTML
    
    <main class="form-signin">
    
        <h1 class="h3 mb-3 fw-normal">MSU Formula Racing Time Clock Link</h1>
        <p>Use your smartphone to scan the QR-code below. You will then be taken to the time clock page.</p>
    
        <img class="mb-4" src="$image" alt="" width="$QRPixels" height="$QRPixels">
            
    </main>
    
HTML;

        # missing key
        } else {
            $html .= <<<HTML
    <main class="form-signin">
        
        <h1 class="h3 mb-3 fw-normal">Error</h1>
        <p>Login key not found in URL. Are you trying to cheat the system?</p>
    
    </main>
HTML;

        }


        return $html;
    }

    private $session;
    private $get;
    private $error = False;

}