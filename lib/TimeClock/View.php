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
        $html = '<header class="main">';
        $html .= "<h1>$this->title</h1>";
        $html .= '</header>';

        return $html;
    }



    /**
     * Create the HTML for the page footer
     * @return string HTML for the standard page footer
     */
    public function footer()
    {
        return <<<HTML
<footer><p>Copyright © 2021 Dave Yonkers. All rights reserved.</p></footer>
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
	public function getRedirect() {
		return $this->redirect;
	}



	protected $site;		///< The Site object
	private $title = "";	///< The page title

	protected $redirect = null;	///< Optional redirect?
}