<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.0
 *-----------------------------------------------------------------------------
 * 2016-06-08 : initial version Ushizawa
 *
 * ログ関連DAO
 *----------------------------------------------------------------------------*/
class log_dao extends DBIO {
	var $table_name = "T_LOG";
	var $log_type = 1;
	var $total_rec = 0;

	// -------------------------------------------------------------------------
	//	検索
	// log_type 1: log, 2:tcd log
	// -------------------------------------------------------------------------
	function search($postdata, $limit="50"){
		$page = filter_input(1, 'page') ? filter_input(1, 'page') : 1;
		$offset = ($page-1) * $limit;

		//	preparate
		$search_key = array();
		$search_val = array();

		//	1 KEYWORD
		// 社員番号、社員名、営業所名、タイトル、内容
		if(!empty($postdata["FREE_WORD"])){
			$free_word = mb_convert_kana($postdata["FREE_WORD"], "ns");
			$res = explode(" ", $free_word);	// 空白区切り

			$tmp = array();
			$str = "(a.UPDATE_CHARGE like ? or a.NOTE like ? or a.FILE_NAME like ? or b.FULL_NAME collate utf8_unicode_ci like ? or b.PHONETIC_SYMBOL collate utf8_unicode_ci like ? )";

			$p_cnt = substr_count($str, "?");

			foreach($res as $k){
				$tmp[] = $str;
				for($i=0; $i<$p_cnt; $i++){
					$search_val[] = "%".$k."%" ;
				}
			}
			$search_key[] = implode(" AND " , $tmp);
		}

		// 作成日（から）
		if(!empty($postdata["DATE_FROM"])){
			$search_key[] = "UPDATE_DATETIME >= ? " ;
			$search_val[] = $postdata["DATE_FROM"]." 00:00:00";
		}
		// 作成日（まで）
		if(!empty($postdata["DATE_TO"])){
			$search_key[] = "UPDATE_DATETIME <= ?" ;
			$search_val[] = $postdata["DATE_TO"] ." 23:59:59";
		}

		//  会社
		if(!empty($postdata["COMPANY_CODE"])){
			$search_key[] = "b.COMPANY_CODE = ?" ;
			$search_val[] = $postdata["COMPANY_CODE"];
		}
		// 営業所
		if(!empty($postdata["OFFICE_CODE"])){
			$search_key[] = "b.OFFICE_CODE = ?";
			$search_val[] = $postdata["OFFICE_CODE"];
		}
		// ログタイプ
		if(!empty($postdata["LOG_TYPE"])){
			$search_key[] = "LOG_TYPE = ?";
			$search_val[] = $postdata["LOG_TYPE"];
		}
		// テーブル
		if(!empty($postdata["TABLE_NAME"])){
			$search_key[] = "TABLE_NAME = ?";
			$search_val[] = $postdata["TABLE_NAME"];
		}
	//	pr($postdata);
		// 条件式
		if(!empty($search_key)){
			$where_str = implode(" AND ", $search_key);
		}
		// SQL文
		$sql = "select a.*, a.UPDATE_CHARGE as LOG_EMPLOYEE_NUMBER, b.FULL_NAME, c.OFFICE_NAME, b.OFFICE_CODE 
				from $this->table_name a 
				left join T_EMPLOYEE b on a.UPDATE_CHARGE=b.EMPLOYEE_NUMBER 
				left join M_OFFICE c on b.OFFICE_CODE=c.OFFICE_CODE ";
		// where
		if(!empty($where_str)){
			$sql .= " WHERE $where_str";
		}
		// order by
		$sql .= " ORDER BY UPDATE_DATETIME DESC";

		//logsave("dao",  getSql($sql, $search_val));
		// ページングのために結果件数を取得
		$this->total_rec = parent::getOne("select count(*) from ($sql)t", $search_val);
		// 一覧取得
		return parent::getAllAssoc($sql." limit $limit offset $offset", $search_val);
	}

	// -------------------------------------------------------------------------
	//	データの登録
	// -------------------------------------------------------------------------
	function save($log_type, $table_name="", $eid="", $note="", $update_charge=""){

		$save_data = array();

		// 更新者
		if(empty($update_charge)){
			global $user;
			$update_charge = $user->eid;
		}
		$save_data["UPDATE_DATETIME"] = date("YmdHis");
		$save_data["UPDATE_CHARGE"] = $update_charge;
	//	$save_data["IP"] = $_SERVER['REMOTE_ADDR'];
	//	$save_data["SESSID"] = substr(str_replace("PHPSESSID=", "", $_SERVER['HTTP_COOKIE']),0,32);

		$save_data["LOG_TYPE"] = $log_type;
		$save_data["FILE_NAME"] = $_SERVER["PHP_SELF"];
		if(!empty($table_name)) $save_data["TABLE_NAME"] = $table_name;
		if(!empty($eid)) $save_data["EMPLOYEE_NUMBER"] = $eid;
		if(!empty($note)) $save_data["NOTE"] = $note;

		$place_holders = implode(',', array_fill(0, count($save_data), '?'));
		$sql = "insert into $this->table_name (".implode(",", array_keys($save_data)).") values (".$place_holders.")";
		//echo get_sql($sql, $save_data);exit;
		parent::query($sql, array_values($save_data));
	}

	// -------------------------------------------------------------------------
	//	データの登録
	// -------------------------------------------------------------------------
	function save_update($log_type, $data_from, $data_to){
		$table_name = "LOG_UPDATE_TABLE";

		global $user;
		$save_data = array();

		$save_data["UPDATE_DATETIME"] = date("Y-m-d His");
		$save_data["UPDATE_CHARGE"] = $user->eid;
		$save_data["IP"] = $_SERVER['REMOTE_ADDR'];
		$save_data["SESSID"] = str_replace("PHPSESSID=", "", $_SERVER['HTTP_COOKIE']);

		$save_data["LOG_TYPE"] = $log_type;
		$save_data["FILE_NAME"] = $_SERVER["REQUEST_URI"];
		$save_data["EMPLOYEE_NUMBER"] = "";
		$place_holders = implode(',', array_fill(0, count($save_data), '?'));
		$sql = "insert into $table_name (".implode(",", array_keys($save_data)).") values (".$place_holders.")";

		parent::query($sql, array_values($save_data));
	}

	// ログイン
	function login(){
		$this->save(1);
	}
	// ログアウト
	function logout(){
		$this->save(2);
	}
	function insert($table_name="", $eid="", $note=""){
		$this->save(4, $table_name, $eid, $note);
	}
	function update($table_name="", $eid="", $note=""){
		$this->save(5, $table_name, $eid, $note);
	}
	function del($table_name="", $eid="", $note=""){
		$this->save(6, $table_name, $eid, $note);
	}
	// export
	function export($note=""){
		$this->save(7, "", "", $note);
	}
	// import
	function import($note=""){
		$this->save(8, "", "", $note);
	}
	// import
	function batch($note=""){
		$this->save(9, "", "", $note, "batch");
	}
}