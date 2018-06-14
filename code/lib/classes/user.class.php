<?php
/* -----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 * -----------------------------------------------------------------------------
 * 	2006-07-24 : initial version
 * 	2009-10-31 : rebuild
 *  2011-05-10 : rebuild by hide
 *  2015-05-12 : rebuild by hide
 *
 *  user information
 * ---------------------------------------------------------------------------- */
require_once DAO_DIR.'user_dao.php';	//	attach dao
require_once DAO_DIR.'log_dao.php';		//	attach dao

define("SESS_TIMEOUT" , 60*30);			//	Session Timeout

class userContainer
{
	public $islogin = false ;
	public $eid = null;
	public $rip = null;
	public $ua = null;


	/* --------------------------------------------------------------------------
	 * constructor
	 * ------------------------------------------------------------------------- */
	public function __construct()
	{
		$this->firsttime = time();
		$this->laptime = time();
		logsave("user","init user container");
	}
	/* --------------------------------------------------------------------------
	 * validate trigger
	 * ------------------------------------------------------------------------- */
	function validateAuth(){
		return true ;
	}
	
	/* --------------------------------------------------------------------------
	 * crypt logic
	 * ------------------------------------------------------------------------- */
	// crypt
	function crypt($s){
		return password_hash($s, PASSWORD_DEFAULT);
	}
	// crypt format check
	function iscrypted($h){
		return $h[0] == "$" ;
	}
	// password check
	function passwordVerify($plain, $hash){
		return password_verify($plain, $hash);
	}

	/* --------------------------------------------------------------------------
	 * logoff
	 * ------------------------------------------------------------------------- */
	public function logoff(){
		// clear Attribute
		global $data;
		$data->cleanAttributes();

		// 全てのプロパティーを初期化
		foreach ($this as $key => $value) {
			$this->$key = null;
		}
		$this->islogin = false;
		logsave('auth', 'request logoff , purge usercontainer');
	}
	/* --------------------------------------------------------------------------
	 * login
	 * ------------------------------------------------------------------------- */
	public function login($eid, $pwd){
		//	logoff
		$this->logoff();

	}

	/* --------------------------------------------------------------------------
	 * パスワード
	 * ------------------------------------------------------------------------- */
	// パスワードチェック
	function checkPw($eid, $pw){
		$user_dao = new user_dao();
		$db_pw = $user_dao->getPw($eid);
		return $this->passwordVerify($pw, $db_pw);
	}
	// パスワード変更
	function changePw($eid, $new_pw){
		$user_dao = new user_dao();
		$new_pw_crypt = $this->crypt($new_pw);
		return $user_dao->changePw($eid, $new_pw_crypt);
	}

	/* --------------------------------------------------------------------------
	 * アクセス権
	 * ------------------------------------------------------------------------- */
	// ページにアクセス権があるかどうかを返す
	public function hasAuth($target){
		global $module;

		print_r($module);
		$dao = new user_dao();
		$result = $dao->getAuthDiv($module, $target);
		if ($result !== false) {
			if (!stristr($result, $this->authdiv)) {
				return false;
			}
		}
		return true;
	}

	/* --------------------------------------------------------------------------
	 * セッション寿命（秒）を得る
	 * ------------------------------------------------------------------------- */
	public function getSessTimeLimit(){
		return $this->sessLifeTime ;
	}
}
