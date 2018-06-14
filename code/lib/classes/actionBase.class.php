<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2009-10-31 : rebuild
 *  2011-05-10 : rebuild by hide
 *  2015-05-12 : rebuild by hide
 *
 *  action base
 *----------------------------------------------------------------------------*/
class actionBase
{
	public $me = array();
	public $viewitem = array();
	public $debug_echo = false;
	public $debug_status = false;
	public $debug_query = false;
	public $maintitle = 'N/A';
	public $subtitle = 'N/A';
	public $nooutput = false;

	/*--------------------------------------------------------------------------
	 * dispatcher
	 *-------------------------------------------------------------------------*/
	/**
	 * @param $data
	 * @param $user
	 * @param $factory
	 *
	 * @return bool
	 */
	public function dispatch(&$data, &$user, &$factory){
		return true;
	}
	/*--------------------------------------------------------------------------
	 * renderer
	 *-------------------------------------------------------------------------*/
	/**
	 * @param $data
	 * @param $user
	 * @param $factory
	 */
	public function renderer(&$data, &$user, &$factory){
		//	no output mode
		if ($this->nooutput){
			return;
		}

		//	default item
		$this->viewitem['LOCATION'] = $_SERVER['SCRIPT_FILENAME'];
		$this->viewitem['SERVER'] = $_SERVER['SERVER_NAME'];
		$this->viewitem['NOW'] = date('Y/m/d H:i:s');
        $this->viewitem['CONTENTS_NAME'] = CONTENTS_NAME;
        $this->viewitem['RELEASE'] = RELEASE;

		// head
		$this->viewitem['HEAD'] = '
		    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta http-equiv="content-language" content="ja">
			<meta name="description" content="" />
			<meta name="keywords" content="" />
			<link rel="icon" href="/images/chara/animal_neko.png" type="image/png">
			<link href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,700,500,900" rel="stylesheet" type="text/css">
			<script src="/js/jquery.min.js"></script>
				
				<link rel="stylesheet" href="/css/style.css?RELEASE='.RELEASE.'" />
				<link rel="stylesheet" href="/css/icons.css" />
			';
		// navi
		$this->viewitem['NAVI'] = '
			<ul>
				<li><a href="/index.html">HOME</a></li>
				<li><a href="/games/index.html">games</a></li>
				<li><a href="/studies/index.html">studies</a></li>
				<li><a href="/hobbies/index.html">hobbies</a></li>
			</ul>
		';//<li class="active">
		// tweet
		$this->viewitem['TWEET'] = '<div class="container">
				<section>
					<blockquote>&ldquo;Let’s go wherever you want to go.&rdquo;</blockquote>
				</section>
			</div>';
		// footer
		$this->viewitem['FOOTER'] = '<div class="container">
				<section>
					<header>
						<h2>Get in touch</h2>
						<span class="byline">Integer sit amet pede vel arcu aliquet pretium</span>
					</header>
					<ul class="contact">
						<li><a href="#" class="fa fa-twitter"><span>Twitter</span></a></li>
						<li class="active"><a href="#" class="fa fa-facebook"><span>Facebook</span></a></li>
						<li><a href="#" class="fa fa-dribbble"><span>Pinterest</span></a></li>
						<li><a href="#" class="fa fa-tumblr"><span>Google+</span></a></li>
					</ul>
				</section>
			</div>';
		// copy right
		$this->viewitem['COPY_RIGHT'] = '<div id="copyright">
			© 2018 '.CONTENTS_NAME.'
		</div>';

		//	load basefile
		$buff = implode('', file(realpath($_SERVER['SCRIPT_FILENAME'])));

		//	viewitem remap
		$akey = array_keys($this->viewitem);
		for ($i = 0; $i < count($akey); ++$i) {
			$rpl = '___'.$akey[$i].'___';
			$buff = str_replace($rpl, $this->viewitem[$akey[$i]], $buff);
		}

		//	output
		echo $buff;
	}
}