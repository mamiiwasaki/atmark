<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.0
 *-----------------------------------------------------------------------------
 * 2016-02-04 : initial version Hide
 *	
 * ログイン認証、権限管理
 * 
 *----------------------------------------------------------------------------*/
class user_dao extends DBIO {

	// authlimit取得
	// （ログインに５回連続で失敗した時に６０秒後の時間がセットされる）の時間内はログインできない
	function getAuthLimit($eid){
		$sql = "select authlimit from SYS_USERINDEX where employee_number=?";
		$prm = array($eid);
		return $this->query($sql, $prm);
	}

	//  -----------------------------------------------------------------------
	//	ユーザ情報取得
	//  -----------------------------------------------------------------------
	function getUserInfo($eid){
		// TODO 社員番号のフォーマットはASP先で変わるのでどうするか。
		//	escape
		//$eid = (int)$eid ;		//	number format
		//$eid = sprintf("%07d", $eid);

		$sql = "select * from T_EMPLOYEE where EMPLOYEE_NUMBER=? and DEL_FLG=0 and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?)";
		$prm = array($eid, date("Y-m-d"));
		//logsave("test", getSql($sql, $prm));
		return $this->getRowAssoc($sql,$prm);
	}


	//  -----------------------------------------------------------------------
	//	SYS_USERINDEXを更新する
	//  -----------------------------------------------------------------------
	function updateSysUserindex($eid){
		// 従業員情報取得
		$sql = "select * from T_EMPLOYEE where EMPLOYEE_NUMBER=? and DEL_FLG=0 and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?)" ;
		$prm = array($eid, date("Y-m-d"));
		$master_row = $this->getRowAssoc($sql, $prm);

		// 既存データがあるかチェック
		$sql = "select * from SYS_USERINDEX where employee_number=?";
		$this->query($sql, array($eid));
		// TODO
		$is_exists = (boolean) $this->fetch();

		if($is_exists){
			//	SYS_USERINDEXを更新する
			logsave("user_dao","update exist accout data eid=$eid");
			$sql = "update SYS_USERINDEX set company_code=?,section_code=?,office_code=?,auth_code=?,update_time=?,full_name=? where employee_number=?" ;
			$prm = array(
				$master_row["COMPANY_CODE"],
				$master_row["SECTION_CODE"],
				$master_row["OFFICE_CODE"],
				$master_row["AUTH_CODE"],
				date("His"),
				$master_row["FULL_NAME"],
				$eid);
			//logsave("test", getSql($sql, $prm));
			$this->query($sql,$prm);
		} else {
			// SYS_USERINDEXがまだ初期化されていないので初期化する
			logsave("user_dao","initialize exist accout data eid=".$eid);
			$sql = "insert into SYS_USERINDEX set employee_number=?,company_code=?,section_code=?,office_code=?,auth_code=?,update_time=?,full_name=?" ;
			$prm = array(
				$eid,
				$master_row["COMPANY_CODE"],
				$master_row['SECTION_CODE'],
				$master_row["OFFICE_CODE"],
				$master_row["AUTH_CODE"],
				date("His"),
				$master_row["FULL_NAME"]);
			$this->query($sql,$prm);
		}
	}

	//  -----------------------------------------------------------------------
	//	ログインに成功したら、SYS_USERINDEXのペナルティーを解除する
	//  -----------------------------------------------------------------------
	function resetPenalty($eid){
		logsave("user_dao","reset penalty");
		//	update
		$sql = "update SYS_USERINDEX set authretry=0 , authlimit=0 where employee_number=?" ;
		$prm = array($eid);
		$result = $this->query($sql,$prm);
	}

	//  -----------------------------------------------------------------------
	//	SYS_USERINDEXにペナルティをセットする
	//	ログイン失敗ごとにauthretryをカウントアップし、
	//	authretryが５回以上になったらauthlimitに６０秒後の時間をセットする
	//  -----------------------------------------------------------------------
	function setPenalty($eid){
		logsave("user_dao","set penalty");
		//	get
		$sql = "select authretry from SYS_USERINDEX where employee_number=?" ;
		$prm = array($eid);
		$authretry = $this->getOne($sql, $prm);

		$retrycount = (int)$authretry + 1;

		if($retrycount > AUTH_LIMIT){
			//	penalty time setup
			$lim = time() + AUTH_PENALTY_TIME ;		//	re-auth limit time
			$sql = "update SYS_USERINDEX set authlimit=? where employee_number=?" ;
			$prm = array($lim, $eid);
			$result = $this->query($sql,$prm);
		}else{
			//	penalty count
			$sql = "update SYS_USERINDEX set authretry=? where employee_number=?" ;
			$prm = array($retrycount, $eid);
			$result = $this->query($sql,$prm);
		}
	}

	//  -----------------------------------------------------------------------
	// 権限を取得する
	//  -----------------------------------------------------------------------
	function getAuthDiv($application, $module){
		$sql = "select permission from SYS_FUNCTIONS where application=? and module=?" ;
		$prm = array($application , $module . ".html") ;
		return $this->getOne($sql, $prm);
	}
	//  -----------------------------------------------------------------------
	// パスワードを取得する
	//  -----------------------------------------------------------------------
	function getPw($eid){
		$sql = "select PASSWORD from T_EMPLOYEE where EMPLOYEE_NUMBER=?" ;
		$prm = array($eid);
		return $this->getOne($sql, $prm);
	}
	//  -----------------------------------------------------------------------
	// パスワードを変更する
	//  -----------------------------------------------------------------------
	function changePw($eid, $new_pw){
		$sql = "update T_EMPLOYEE set PASSWORD=? where EMPLOYEE_NUMBER=?";
		$prm = array($new_pw, $eid);
		$this->query($sql, $prm);
	}
}
