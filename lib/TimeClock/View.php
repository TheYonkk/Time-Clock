<?php


namespace TimeClock;

/**
 * Base class for all views
 */
class View {
	/**
	 * View constructor.
	 * @param Site $site. The Site object
	 */
	public function __construct(Site $site) {
		$this->site = $site;


	}

    /**
     * Create the HTML for the page header
     * @return string HTML for the standard page header
     */
    public function header() {
        $root = $this->site->getRoot();

        $html = <<<HTML
<div class="container">
  <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
    <a href="$root" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
      <img class="bi me-2" width="40" height="40" src="dist/img/SR_Badge.svg">
      <span class="fs-4">Time Clock</span>
    </a>
    
HTML;

        if(count($this->links) > 0) {
            $html .= '<ul class="nav nav-pills">';
            foreach($this->links as $link) {

                $active = $link['active'] ? 'active':'';

                $html .= '<li class="nav-item"><a href="' .
                    $link['href'] . '" class="nav-link ' .
                    $active . '">' .
                    $link['text'] . '</a></li>';
            }
            $html .= '</ul>';
        }

        $html .= <<<HTML
  </header>
  <div class="container text-center">
        <div class="row my-2">
            <div class="col-3"></div>
            <div class="col-6">
                <h1>$this->title</h1>
            </div>
            <div class="col-3"></div>
        </div>
HTML;

        if ($this->blurb !== ""){
            $html .= <<<HTML
    <div class="row my-2">
    <div class="col-3"></div>
        <div class="col-6">
            <p>$this->blurb</p>
        </div>
        <div class="col-3"></div>
    </div>
HTML;

        }

        $html .= <<<HTML
    
  </div>
</div>
HTML;


        return $html;
    }



    /**
     * Create the HTML for the page footer
     * @return string HTML for the standard page footer
     */
    public function footer()
    {
        return <<<HTML
<footer class="footer mt-auto py-3 bg-light">
  <div class="container">
    <span class="text-muted">&copy; Dave Yonkers</span>
  </div>
</footer>
HTML;
    }

	/**
	 * Create the HTML for the contents of the head tag
	 * @return string HTML for the page head
	 */
	public function head() {
		return <<<HTML
<meta charset="utf-8">
<title>$this->title</title>

<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">


<!-- <script src="dist/main.js"></script> -->
<link rel="stylesheet" href="lib/timeclock.css">

<!-- custom JS -->
<script src="dist/main.js"></script>
HTML;
	}

	public function boostrapJS(){
	    return '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>';
    }

	/**
	 * Set the page title
	 * @param $title New page title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Get the redirect location link.
	 * @return page to redirect to.
	 */
	public function getProtectRedirect() {
		return $this->protectRedirect;
	}

    /**
     * Add a link to the navbar
     * @param string $href the link
     * @param string $text the text to display
     * @param Boolean $active If the link should be shown as active
     */
	public function addLink($href, $text, $active=False){
        $this->links[] = ["href" => $href, "text" => $text, "active" => $active];
    }

    /**
     * Protect a page for staff only access
     *
     * If access is denied, call getProtectRedirect
     * for the redirect page
     * @param $site The Site object
     * @param $user The current User object
     * @return bool true if page is accessible
     */
    public function protect($site, $user) {
        if($user->isStaff()) {
            return true;
        }

        $this->protectRedirect = $site->getRoot() . "/";
        return false;
    }


    /**
     * The blurb, or, more formally, page description
     * @param $blurb
     */
    public function setBlurb($blurb){
        $this->blurb = $blurb;
    }



	protected $site;		///< The Site object
	private $title = "";	///< The page title
    private $blurb = "";
    private $links = array();

	protected $protectRedirect = null;	///< Optional redirect?
}