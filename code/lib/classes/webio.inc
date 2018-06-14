<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	
 *	webio
 *----------------------------------------------------------------------------*/
class WEBIO {
	var	$cookie ;		//	cookie file location
	var	$curl   ;		//	cURL object
	var	$ref    ;		//	reference url
	var $ua     ;		//	user agent
	var	$buff   ;		//	html buffer

	function __construct(){
//	function WEBIO(){
		logsave("WEBIO"  ,  "init cURL");

		$this->cookie=tempnam(sys_get_temp_dir(),'cookie_');
		logsave("WEBIO"  ,  "cookie file = " . $this->cookie);

		$this->ref = "" ;
		$this->ua  = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
	
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,   true);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR,        $this->cookie);
		curl_setopt($this->curl, CURLOPT_COOKIEFILE,       $this->cookie);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION,   true); 
		curl_setopt($this->curl, CURLOPT_REFERER,          $this->ref);
		curl_setopt($this->curl, CURLOPT_USERAGENT,        $this->ua); 
	}

	function get($url){
		$this->ref = $url ;
		logsave("WEBIO"  , "Get Request URL = $url");
		curl_setopt($this->curl, CURLOPT_POST, false);
		curl_setopt($this->curl, CURLOPT_URL,$url);
		$this->buff = curl_exec($this->curl); 
		logsave("WEBIO"  , "Result = " . strlen($this->buff) . " bytes");
		curl_setopt($this->curl, CURLOPT_REFERER,          $this->ref);
		return $this->buff ;
	}

	function post($url,$param){
		$this->ref = $url ;
		logsave("WEBIO"  , "Post Request URL = $url");

		curl_setopt($this->curl, CURLOPT_URL,$url);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS,       http_build_query($param));

		$this->buff = curl_exec($this->curl); 
		logsave("WEBIO"  , "Result = " . strlen($this->buff) . " bytes");
		curl_setopt($this->curl, CURLOPT_REFERER,          $this->ref);
		return $this->buff ;
	}
}
/*--------------------------------------------------------------------------*/
