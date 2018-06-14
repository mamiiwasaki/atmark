<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *
 *  template Trait
 *----------------------------------------------------------------------------*/
trait daoUtilTrait
{
	// -------------------------------------------------------------------------
	//	データ取得
	// -------------------------------------------------------------------------
	function get($id = null, $table_name='', $pkey_name='', $orderby='', $where_str='') {
		// table_name, pkey_name
		if (empty($table_name)) $table_name = $this->table_name;
		if (empty($pkey_name)) $pkey_name = $this->pkey_name;

		if($id != null){
			$sql = "SELECT * FROM $table_name WHERE $pkey_name=?";
			logsave("dao get", getSql($sql, array($id)));
			return $this->getRowAssoc($sql, array($id));
		}
		// all
		$sql = "select * from $table_name ";
		if(!empty($sql)){
			$sql .= $where_str;
		}
		if($orderby !== ''){
			$sql .= "order by ".$orderby;
		}
		logsave("dao get", getSql($sql));
		return $this->getAllAssoc($sql);
	}

	// -------------------------------------------------------------------------
	//	データの登録・更新
	// -------------------------------------------------------------------------
	function save($post, $table_name='', $pkey_name=''){
		global $user;

		// table_name, pkey_name
		if (empty($table_name)) $table_name = $this->table_name;
		if (empty($pkey_name)) $pkey_name = $this->pkey_name;

		$pkey = (!empty($post[$pkey_name])) ? $post[$pkey_name] : '';
		//logsave("test pkey", $pkey_name."---".$pkey);

		// ID(autoincrement以外のプライマリキーの場合、DB参照して既存がないかチェックする)
		$insert_flg = false;
		if($pkey_name !== 'ID'){
			$cnt = $this->getOne("select $pkey_name from $table_name where $pkey_name=?", array($pkey));
			if(empty($cnt)) $insert_flg = true;
		} else {
			if(empty($pkey)) $insert_flg = true;
		}

		// update
		$post["UPDATE_DATE"] = date("Ymd");
		$post["UPDATE_TIME"] = date("His");
		$post["UPDATE_CHARGE"] = $user->eid;
		//$post["UPDATE_TASK"] = CONTENTS_NAME;
		// insert
		if($insert_flg){
			$post["CREATE_DATE"] = date("Ymd");
			$post["CREATE_TIME"] = date("His");
			$post["CREATE_CHARGE"] = $user->eid;
		}

		// カラムを取得
		$fields_arr = $this->getFields($table_name);

		$set_key = array();
		$save_data = array();

		// テーブルにないカラムはスルーする。
		foreach($post as $k=>$v){
			if(empty($pkey) && $k===$pkey_name) continue;
			if(array_key_exists($k, $fields_arr)){
				$save_data[$k] = $v;
				$set_key[] = "$k=?";
			}
		}
		// log
		$log_dao = new log_dao();

		// insert or update
		if($insert_flg){
			///////////////////////
			// insert
			$sql = "insert into $table_name set ".implode(",", $set_key);
			logsave("dao insert", getSql($sql, array_values($save_data)));
			$log_dao->insert($table_name, $user->eid);

		} else {
			///////////////////////
			// update
			$sql = "update $table_name set ".implode(",", $set_key)." where ".$pkey_name."='".$pkey."'";
			logsave("dao update", getSql($sql, array_values($save_data)));
			$log_dao->update($table_name, $user->eid, $pkey);
		}
		// query
		$this->query($sql, array_values($save_data));
		// insertの場合、IDを取得
		// TODO
		if(empty($pkey)){
			$pkey = $this->getOne("select LAST_INSERT_ID();");
		}

		// IDを返す
		return $pkey;
	}

	// -------------------------------------------------------------------------
	//	データの削除（フラグ）
	// -------------------------------------------------------------------------
	function delete($id, $table_name='', $pkey_name=''){
		// table_name, pkey_name
		if (empty($table_name)) $table_name = $this->table_name;
		if (empty($pkey_name)) $pkey_name = $this->pkey_name;
		$sql = "update $table_name set DEL_FLG=1, UPDATE_DATE=CURDATE(), UPDATE_TIME=CURTIME() where $pkey_name=?";
		logsave("dao delete", getSql($sql, array($id)));
		$this->query($sql, array($id));
		$log_dao = new log_dao();
		$log_dao->del($table_name, "", $id);
	}

	// -------------------------------------------------------------------------
	//	既存データがあるか
	// -------------------------------------------------------------------------
	function isExists($colname, $code, $id=null){
		if(empty($id)){
			// 登録
			$sql = "select $this->pkey_name from $this->table_name where $colname=?";
			$prm = array($code);
		} else {
			// 更新
			$sql = "select $this->pkey_name from $this->table_name where $colname=? and ID<>?";
			$prm = array($code, $id);
		}
		$res = $this->getOne($sql, $prm);
		return (!empty($res)) ? true : false;
	}
	// -------------------------------------------------------------------------
	//	１つのカラムを返す
	// -------------------------------------------------------------------------
	function getName($colname, $val, $key="", $table_name=""){
		if (empty($key)) $key = $this->pkey_name;
		if (empty($table_name)) $table_name = $this->table_name;

		$sql = "select $colname from $table_name ";

		if(!is_array($key)){
			$sql .= "where $key=?";
			$prm = array($val);
		} else {
			// 配列
			$tmp = array();
			foreach($key as $keyname){
				$tmp[] = $keyname."=?";
			}
			$sql .= "where ".implode(" and ", $tmp);
			$prm = $val;
		}
		logsave("dao getName", getSql($sql, $prm));
		return $this->getOne($sql, $prm);
	}
	/*
	* チェックボックスで「未選択」がある場合の処理
	*/
	function setSearchUnk($search_data, $colname, &$search_key, &$search_val){
		if(!is_array($search_data)) return ;	// 必ず配列でくるはず
		$tmpa = array();
		if(!empty($search_data)){
			foreach($search_data as $state){
				//logsave("testbbb", $state);
				if($state === "unk"){
					$tmpa[] = " ( $colname = ? OR $colname IS NULL ) ";
					$search_val[] = "";
				} else if($state !=""){
					$tmpa[] = " $colname = ? " ;
					$search_val[] = $state;
				}
			}
		}
		if(!empty($tmpa)){
			$search_key[] = "(" . implode(" OR " , $tmpa) . ")" ;
		} else {
			//$search_key[] = "false"; // 何もチェックされていなかったら何も表示しない
		}
	}
}