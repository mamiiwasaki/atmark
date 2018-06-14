<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.0
 *-----------------------------------------------------------------------------
 * 2016-02-04 : initial version Hide
 *	
 * マスタ管理
 * 
 *----------------------------------------------------------------------------*/
require_once CLASSES_DIR . 'dao_util.trait.php';

class master_dao extends DBIO {
	use daoUtilTrait;

	// ありなし
	function getArinashi(){
		return array(1=>'有', 2=>'無');
		//1:有 2:無
	}

	/*
	 * 顧客名を返す
	 */
	function getCustomerName($p_code, $c_code=''){
		if(empty($c_code)){
			// 顧客コードのみ
			$sql = "select COMPANY_NAME from T_CUSTOMER where DEL_FLG=0 and CUSTOMER_CODE=?";
			return $this->getOne($sql, array($p_code));
		} else {
			// 顧客コード＋事業所コード
			$sql = "select concat(COMPANY_NAME, ' (', BUSINESS_ESTABLISHMENT_NAME, ')') from T_CUSTOMER where DEL_FLG=0 and CUSTOMER_CODE=? and BUSINESS_ESTABLISHMENT_CODE=?";
			return $this->getOne($sql, array($p_code, $c_code));
		}
	}
	/*
	 * 社員名を返す
	 */
	function getEmployeeName($eid){
		$sql = "select FULL_NAME from T_EMPLOYEE where EMPLOYEE_NUMBER=?";
		return $this->getOne($sql, array($eid));
	}
	/*
	 * 管理権限（承認権限） 保持者のリストを配列で返す
	 */
	function getAuthAdminList(){
		$sql = "select * from T_EMPLOYEE where DEL_FLG=0 and AUTH_ADMIN='1' and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?) order by EMPLOYEE_NUMBER";
		$prm = array(date("Y-m-d"));
		$all = $this->getAll($sql, $prm);
		$arr = array();
		foreach($all as $val){
			$arr[$val['EMPLOYEE_NUMBER']] = $val['FULL_NAME'];
		}
		return $arr;
	}
	/*
	 * 基幹権限 保持者のリストを配列で返す
	 */
	function getAuthSystemList(){
		$sql = "select * from T_EMPLOYEE where DEL_FLG=0 and AUTH_SYSTEM='1' and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?) order by EMPLOYEE_NUMBER";
		$prm = array(date("Y-m-d"));
		$all = $this->getAll($sql, $prm);
		$arr = array();
		foreach($all as $val){
			$arr[$val['EMPLOYEE_NUMBER']] = $val['FULL_NAME'];
		}
		return $arr;
	}
	/*
	 * 従業員情報編集権限のリストを配列で返す
	 */
	function getAuthEditList(){
		$sql = "select * from T_EMPLOYEE where DEL_FLG=0 and AUTH_EDIT_EMPLOYEE='1' and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?) order by EMPLOYEE_NUMBER";
		$prm = array(date("Y-m-d"));
		$all = $this->getAll($sql, $prm);
		$arr = array();
		foreach($all as $val){
			$arr[$val['EMPLOYEE_NUMBER']] = $val['FULL_NAME'];
		}
		return $arr;
	}
	/*
	 * テクノロジストのリストを配列で返す
	 */
	function getTechnologist($office_code=null){
		$sql = "select * from T_EMPLOYEE where DEL_FLG=0 and CLASSIFICATION='2' and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?)";
		$prm = array(date("Y-m-d"));
		if(!empty($office_code)){
			$sql .= " and OFFICE_CODE=?";
			$prm[] = $office_code;
		}
		$sql .= " order by EMPLOYEE_NUMBER";

		$all = $this->getAll($sql, $prm);
		$arr = array();
		foreach($all as $val){
			$arr[$val['EMPLOYEE_NUMBER']] = $val['FULL_NAME'];
		}
		return $arr;
	}

	/*
	 * 社員分類別: 従業員名（ふりがな）から、候補を返す
	 * 1: 間接社員 2: 技術社員
	 */
	function getSuggestClassification($val, $classification){
		$val = str_replace(array("　"," "), "", $val);
		$prm = array($val."%", mb_convert_kana($val, "n", CHARSET)."%", $classification, date('Y/m/d'));
		$sql = "select EMPLOYEE_NUMBER, FULL_NAME from T_EMPLOYEE 
				where DEL_FLG=0 and (replace(replace(FULL_NAME,'　',''),'','') like ? 
				or replace(replace(PHONETIC_SYMBOL,'　',''),'','') like ?) 
				and CLASSIFICATION=? and (RESIGNATION_DAY Is Null or RESIGNATION_DAY='' or RESIGNATION_DAY >?) 
				order by PHONETIC_SYMBOL";
		logsave("dao getSuggestClassification", getSql($sql, $prm));
		return $this->getAllassoc($sql, $prm);
	}

	/*
	 * 権限別: 従業員名（ふりがな）から、候補を返す
	 * ADMIN: 管理権限 SYSTEM: 基幹権限 READ: 閲覧権限 EDIT_EMPLOYEE: 従業員情報編集権限
	 */
	function getSuggestSystem($val, $auth){
		$val = str_replace(array("　"," "), "", $val);
		$prm = array($val."%", mb_convert_kana($val, "n", CHARSET)."%", 1, date('Y/m/d'));
		$sql = "select EMPLOYEE_NUMBER, FULL_NAME from T_EMPLOYEE 
				where DEL_FLG=0 and (replace(replace(FULL_NAME,'　',''),'','') like ? 
				or replace(replace(PHONETIC_SYMBOL,'　',''),'','') like ?) 
				and AUTH_{$auth}=? and (RESIGNATION_DAY Is Null or RESIGNATION_DAY='' or RESIGNATION_DAY >?) 
				order by PHONETIC_SYMBOL";
		logsave("dao getSuggestSystem", getSql($sql, $prm));
		return $this->getAllassoc($sql, $prm);
	}

	/*
	 * 契約名を返す
	 */
	function getContractName($cno){
		$sql = "select CONTRACT_NAME from T_CONTRACT where CONTRACT_NUMBER=?";
		return $this->getOne($sql, array($cno));
	}
	/*
	 * 添付ファイルの名称を返す
	 */
	function getFileTitle($id){
		return $this->getOne("select TITLE from T_FILE where ID=?", array($id));
	}
	function getFileName($id){
		return $this->getOne("select FILE1 from T_FILE where ID=?", array($id));
	}
	
	// -------------------------------------------------------------------------
	//	現在の社員数取得
	// -------------------------------------------------------------------------
	function getEmployeeCnt(){
		$sql = "select count(*) from T_EMPLOYEE "
			. "where DEL_FLG=0 and (RESIGNATION_DAY is null or RESIGNATION_DAY='' or RESIGNATION_DAY >?)";
		return $this->getOne($sql, array(date('Y-m-d')));
	}
	/*
	 * 4月から始まる月の配列
	 * // TODO 自社で年度開始月を変更できるようにした関係で、utilにgetMonthOrder作った。今後はそっちを使う。
	 */
	function getMonthOrder(){
		return array('04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03');
	}
	// -------------------------------------------------------------------------
	//	権限マスタ
	// -------------------------------------------------------------------------
	function getAuth($code = null){
		if($code != null){
			return $this->getOne("select AUTH_NAME from M_AUTH where AUTH_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select * from M_AUTH where DEL_FLG=0 order by SORT_NUMBER");
		$arr = array();
		foreach($result as $val){
			$arr[$val["AUTH_CODE"]] = $val["AUTH_NAME"];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	会社マスタ
	// -------------------------------------------------------------------------
	function getCompany($code = null){
		if($code != null){
			return $this->getOne("SELECT COMPANY_NAME_S FROM M_COMPANY WHERE COMPANY_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select * from M_COMPANY where DEL_FLG=0 order by COMPANY_CODE");
		$arr = array();
		foreach($result as $val){
			$arr[$val["COMPANY_CODE"]] = $val["COMPANY_NAME_S"];
		}
		return $arr;
	}
	function getCompanyInfo($code='001'){
		return $this->getRowAssoc("select * from M_COMPANY where COMPANY_CODE=?", array($code));
	}

	// -------------------------------------------------------------------------
	//	営業所マスタ
	// -------------------------------------------------------------------------
	function getOffice($company_code=null, $code = null){
		// TODO
		$company_code = '001';
		if($code != null){
			$sql = "select OFFICE_NAME from M_OFFICE where COMPANY_CODE=? and OFFICE_CODE=?";
			return $this->getOne($sql, array($company_code, $code));
		}
		if($company_code != null){
			// companyごと
			$result = $this->getAllAssoc("select * from M_OFFICE where DEL_FLG=0 and COMPANY_CODE=? order by OFFICE_CODE", array($company_code));

		} else {
			// all
			$result = $this->getAllAssoc("select * from M_OFFICE where DEL_FLG=0 order by COMPANY_CODE, OFFICE_CODE");
		}
		$arr = array();
		foreach($result as $val){
			$arr[$val["OFFICE_CODE"]] = $val["OFFICE_NAME"];
		}
		return $arr;
	}

	// 会社コードで連想配列にする
	function getOfficeAll(){
		// all
		$result = $this->getAllAssoc("select * from M_OFFICE where DEL_FLG=0 order by COMPANY_CODE, SORT_NUMBER, OFFICE_CODE");

		$arr = array();
		foreach($result as $val){
			$arr[$val["COMPANY_CODE"]][$val["OFFICE_CODE"]] = $val["OFFICE_NAME"];
		}
		return $arr;
	}
	// 削除された営業所も
	// 削除フラグみない
	function getOfficeWhole($company_code='001'){
		$result = $this->getAllAssoc("select * from M_OFFICE where COMPANY_CODE=? order by SORT_NUMBER, OFFICE_CODE", array($company_code));

		$arr = array();
		foreach($result as $val){
			$arr[$val["OFFICE_CODE"]] = $val["OFFICE_NAME"];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	部署マスタ
	// -------------------------------------------------------------------------
	function getSection($company_code = null, $code = null){
		if($code != null){
			$sql = "select SECTION_NAME from M_SECTION where SECTION_CODE=?";
			return $this->getOne($sql, array($code));
		}
		if($company_code != null){
			// companyごと
			$result = $this->getAllAssoc("select * from M_SECTION where DEL_FLG=0 and COMPANY_CODE=? order by SECTION_CODE", array($company_code));
		} else {
			// all
			$result = $this->getAllAssoc("select * from M_SECTION where DEL_FLG=0 and COMPANY_CODE=001 order by COMPANY_CODE, SECTION_CODE");
		}

		$arr = array();
		foreach($result as $val){
			$arr[$val["SECTION_CODE"]] = $val["SECTION_NAME"];
		}
		return $arr;
	}
	// 削除された部署も
	// 削除フラグみない
	function getSectionWhole($company_code='001'){
		$result = $this->getAllAssoc("select * from M_SECTION where COMPANY_CODE=? order by SORT_NUMBER, SECTION_CODE", array($company_code));

		$arr = array();
		foreach($result as $val){
			$arr[$val["SECTION_CODE"]] = $val["SECTION_NAME"];
		}
		return $arr;
	}
	// 会社コードで連想配列にする
	function getSectionAll(){
		// all
		$result = $this->getAllAssoc("select * from M_SECTION where DEL_FLG=0 order by COMPANY_CODE, SECTION_CODE");

		$arr = array();
		foreach($result as $val){
			$arr[$val["COMPANY_CODE"]][$val["SECTION_CODE"]] = $val["SECTION_NAME"];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	銀行マスタ
	// -------------------------------------------------------------------------
	function getAccount($code = null){
		if($code != null){
			return $this->getOne("select BANK_NAME from M_ACCOUNT where DEL_FLG=0 and ACCOUNT_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select * from M_ACCOUNT where DEL_FLG=0 order by ACCOUNT_CODE");
		$arr = array();
		foreach($result as $val){
			$arr[$val["ACCOUNT_CODE"]] = $val["BANK_NAME"].' '.$val["BRANCH_NAME"].' '.$val["ACCOUNT_TYPE"].' '.$val["ACCOUNT_NUMBER"];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	銀行マスタ一覧
	// -------------------------------------------------------------------------
	function getAccountList($code = null){
		if($code != null){
			return $this->getRow("select * from M_ACCOUNT where ACCOUNT_CODE=?", array($code));
		}
		// all
		return $this->getAllAssoc("select * from M_ACCOUNT where DEL_FLG=0 order by ACCOUNT_CODE");
	}
	// -------------------------------------------------------------------------
	//	従業員区分マスタ
	// -------------------------------------------------------------------------
	function getClassification($code=null){
		$arr = array(1=>"間接社員", 2=>"技術社員");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	雇用形態マスタ
	// -------------------------------------------------------------------------
	function getField($code=null){
		$arr = array(1=>"正社員", 2=>"契約社員", 3=>"一般登録", 4=>"パートタイム");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	分野マスタ
	// -------------------------------------------------------------------------
	function getJob($code=null){
		//$arr = array(1=>"機械", 2=>"ソフト", 3=>"電気/電子");
		$arr = array(1=>"機械", 2=>"ソフト", 3=>"電気/電子", 4=>"建築", 5=>"間接");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}
	function getJobShort($code=null){
		$arr = array(1=>"M", 2=>"S", 3=>"E", 4=>"建", 5=>"間");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}
	// その他あり。
	function getJobOther($code=null){
		$arr = array(1=>"機械", 2=>"ソフト", 3=>"電気/電子", 4=>"建築", 9=>"その他");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}
	// 分野マスタ その他あり。
	function getJobOtherShort($code=null){
		$arr = array(1=>"M", 2=>"S", 3=>"E", 4=>"建", 9=>"その他");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	職種マスタ
	// -------------------------------------------------------------------------
	function getOccupation($job_code=null, $occupation_code=null){
		if($occupation_code != null){
			return $this->getOne("select OCCUPATION_NAME from M_OCCUPATION where OCCUPATION_CODE=?", array($occupation_code));
		}
		// all
		if(empty($job_code)){
			$result = $this->getAllAssoc("select * from M_OCCUPATION where DEL_FLG=0 order by JOB_CODE, SORT_NUMBER");
		} else {
			$result = $this->getAllAssoc("select * from M_OCCUPATION where DEL_FLG=0 and JOB_CODE=? order by SORT_NUMBER", array($job_code));				
		}
		$arr = array();
		foreach($result as $val){
			$arr[$val["OCCUPATION_CODE"]] = $val["OCCUPATION_NAME"];
		}
		return $arr;
	}
	// 分野(JOB)付きで配列を生成
	function getOccupationAll(){
		$result = $this->getAllAssoc("select * from M_OCCUPATION where DEL_FLG=0 order by JOB_CODE, SORT_NUMBER");
		$arr = array();
		foreach($result as $val){
			$arr[$val["JOB_CODE"]][$val["OCCUPATION_CODE"]] = $val["OCCUPATION_NAME"];
		}
		return $arr;
	}
//	//-------------------------------------------------------------------------
	//	扶養数
	// -------------------------------------------------------------------------
	function getSupporterPeople(){
		return array(
			0=>'なし',
			1=>'配偶者のみ',
			2=>'配偶者あり＋扶養１名',
			3=>'配偶者あり＋扶養２名',
			4=>'配偶者あり＋扶養３名',
			5=>'配偶者あり＋扶養４名',
			6=>'配偶者あり＋扶養５名',
			7=>'配偶者なし＋扶養１名',
			8=>'配偶者なし＋扶養２名',
			9=>'配偶者なし＋扶養３名',
			10=>'配偶者なし＋扶養４名',
			11=>'配偶者なし＋扶養５名',
		);
	}
	// -------------------------------------------------------------------------
	//	支払い予定月タイプ
	// -------------------------------------------------------------------------
	function getContractPayMonthType(){
		return array(
			1=>'当月', 
			2=>'翌月', 
			3=>'翌々月',
			4=>'翌々々月');
	}
	// -------------------------------------------------------------------------
	//	税計算区分
	// -------------------------------------------------------------------------
	function getTaxCalcDivision(){
		return array(1=>'行課税', 
			2=>'合計課税');
	}
	// -------------------------------------------------------------------------
	//	賃金
	// -------------------------------------------------------------------------
	function getWage($code=null){
		$arr = array(
				1=>"固定型賃金",
				2=>"業績型賃金",
				3=>"年俸制賃金"
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	誓約書同意
	// -------------------------------------------------------------------------
	function getConsent($code=null){
		$arr = array(
				0=>"未設定",
				1=>"有",
				2=>"無"
		);
		if(!isset($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}


	// -------------------------------------------------------------------------
	//	届け申請種別
	// -------------------------------------------------------------------------
	function getNotice($code=null){
		if($code !== null){
			return $this->getOne("select NOTICE_NAME from M_NOTICE where NOTICE_CODE=?", array($code));
		}
		// all
		$sql = "select NOTICE_CODE, NOTICE_NAME from M_NOTICE where DEL_FLG=0 and NOTICE_CODE<>101 order by SORT";
		$all = $this->getAllAssoc($sql);
		$arr = array();
		foreach($all as $val){
			$arr[$val["NOTICE_CODE"]] = $val["NOTICE_NAME"];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	産業マスタ
	// -------------------------------------------------------------------------
	function getTrade($code=null){
		if($code !== null){
			return $this->getOne("select TRADE_NAME from M_TRADE where TRADE_CODE=?", array($code));
		}
		// all
		$sql = "select * from M_TRADE order by SORT_NUMBER";
		$result = $this->getAllAssoc($sql);
		$arr = array();
		foreach($result as $val){
			$arr[$val["TRADE_CODE"]] = $val["TRADE_NAME"];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	カレンダー区分
	// -------------------------------------------------------------------------
	// 全体のカレンダ区分
	function getCalendarKbn($code=null){
		$arr = array(
			'01'=>'出勤日',
			'02'=>'法定内休日',
			'03'=>'調整出勤日',
			'04'=>'調整休日',	
			'05'=>'交替',
			'06'=>'年休',
		//	'07'=>'調整客先日',
			'09'=>'法定外休日'
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	function getCalendarKbn_s($code=null){
		$arr = array(
			'01'=>'出',
			'02'=>'休内',	// 法定内休日
			'03'=>'調出',
			'04'=>'調休',
			'05'=>'交替',
			'06'=>'年休',
		//	'07'=>'調客',	// 調客
			'09'=>'休外'		// 法定外休日
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// 自社用
	function getCalendarKbnSelf_s(){
		/*出勤、法定内休日、法定外休日、調整出勤、年休*/
		return array(
			'01'=>'出',		// 出勤
			'02'=>'休内',	// 法定内休日
			'09'=>'休外',	// 法定外休日
			'03'=>'調出',
			'06'=>'年休',
		);
	}
	// 顧客用
	function getCalendarKbnCustomer(){
		return array(
			'01'=>'出勤日',		// 出勤
			'02'=>'法定内休日',	// 法定内休日
			'09'=>'法定外休日'	// 法定外休日
		);
	}
	function getCalendarKbnCustomer_s(){
		return array(
			'01'=>'出',		// 出勤
			'02'=>'休内',	// 法定内休日
			'09'=>'休外'		// 法定外休日
		);
	}
	// 個別用
	function getCalendarKbnEmployee(){
		return array(
			'01'=>'出勤',
			'02'=>'休内',
			'03'=>'調出',
			'04'=>'調休',
			'06'=>'年休',
		//	'07'=>'調客',
			'09'=>'休外'	
		);
	}

	// -------------------------------------------------------------------------
	//	カレンダー区分クラス
	// -------------------------------------------------------------------------
	function getCalendarKbnClass(){
		return array(
			'01'=>'syutsu',		// 出勤
			'02'=>'nai',		// 法定内休日
			'03'=>'chosyutsu',	// 調出	調整出勤日
			'04'=>'chokyu',		// 調休
			'05'=>'kotai',		// 交替
			'06'=>'nenkyu',		// 年休	年次有給休暇一斉取得日
		//	'07'=>'gai',		// 調客??? 
			'09'=>'gai'			// 法定外休日??
		);
	}
	// -------------------------------------------------------------------------
	//	カレンダー区分クラス（エクセル出力用）
	// -------------------------------------------------------------------------
	function getCalendarKbnColor(){
		return array(
			'01'=>'FCFFCC',		// 出勤
			'02'=>'FF595C',		// 法定内休日	ピンク
			'03'=>'CCFFCC',		// 調出	調整出勤日		黄緑
			'04'=>'CC0000',		// 調休	?
			'05'=>'CC0000',		// 交替	?
			'06'=>'FF9966',		// 年休	年次有給休暇一斉取得日	オレンジ
		//	'07'=>'CC0000',		// 調客???
			'09'=>'FFCCFF'		// 法定外休日?? ピンク
		);
	}

	// -------------------------------------------------------------------------
	//	カレンダー区分クラス(勤怠表示用)
	// -------------------------------------------------------------------------
	function getCalendarKbnActivity($code=null){
		$arr = array(
			'00'=>'',       // カレンダー未作成
			'01'=>'出',		// 出勤
			'02'=>'休',		// 法定内休日
			'03'=>'調出',	// 調出	調整出勤日
			'04'=>'調休',	// 調休
			'05'=>'交替',	// 交替
			'06'=>'年休',	// 年休	年次有給休暇一斉取得日
			'07'=>'調客',	// 調客???  TODO
			'08'=>'振出',	// 振替出勤
			'09'=>'外休',	// 法定外休日?? TODO
			'10'=>'振休',	// 振替休日
			'11'=>'有休',	// 有給休暇
			'12'=>'前休',	// 午前半休
			'13'=>'後休',	// 午後半休
			'14'=>'特休',	// 特別休暇
			'15'=>'代出',	// 代替出勤
			'16'=>'代休'		// 代替休日
		);
		// TODO 後で消す（古いDBデータでエラーになるのを防ぐための暫定処理）
		$arr += $this->getCalendarAbbreviation();
		
		if(empty($code)){
			return $arr;
		} else {
			if(!isset($arr[$code])){
				return $code;
			}
			return $arr[$code];
		}
	}

	function getCalendarKbnCodeActivity($code=null){
		$arr = array(
			''=>'00',       // カレンダー未作成
			'出'=>'01',		// 出勤
			'休'=>'02',		// 法定内休日
			'調出'=>'03',	// 調出	調整出勤日
			'調休'=>'04',	// 調休
			'交替'=>'05',	// 交替
			'年休'=>'06',	// 年休	年次有給休暇一斉取得日
			'調客'=>'07',	// 調客???  TODO
			'振出'=>'08',	// 振替出勤	
			'外休'=>'09',     // 法定外休日?? TODO
			'振休'=>'10',	// 振替休日
			'有休'=>'11',	// 有給休暇
			'前休'=>'12',	// 午前半休
			'後休'=>'13',	// 午後半休
			'特休'=>'14',   // 特別休暇
            '代出'=>'15',	// 代替出勤
			'代休'=>'16',   // 代替休日
		);
		if(empty($code)){
			return $arr;
		} else {
			if(!isset($arr[$code])){
				return $code;
			}
			return $arr[$code];
		}
	}

	// -------------------------------------------------------------------------
	//	カレンダー区分クラス(勤怠色付け用)
	// -------------------------------------------------------------------------
	function getCalendarKbnActivityColor($code=null){
		$arr = array(
			'00'=>'',		// カレンダー未作成
			'01'=>'syutsu',		// 出勤
			'02'=>'holiday',		// 法定内休日
			'03'=>'chosyutsu',	// 調出	調整出勤日
			'04'=>'chokyu',	// 調休
			'05'=>'kotai',	// 交替
			'06'=>'nenkyu',	// 年休	年次有給休暇一斉取得日
			'07'=>'chokyaku',	// 調客
			'08'=>'furisyutsu',	// 振替出勤
			'09'=>'holiday',	// 法定外休日
			'10'=>'furikyu',		// 振替休日
			'11'=>'yukyu'	,	// 有給休暇
			'12'=>'gozenhan'	,	// 午前半休
			'13'=>'gogohan'	,	// 午後半休
			'14'=>'tokkyu',		// 特別休暇
            '15'=>'furisyutsu'	,	// 代替出勤
			'16'=>'furikyu'		// 代替休日
		);
		if(empty($code)){
			return $arr;
		} else {
			if(!isset($arr[$code])){
				return $code;
			}
			return $arr[$code];
		}
	}

	// -------------------------------------------------------------------------
	// カレンダー区分から略称を取得
	// -------------------------------------------------------------------------
	function getCalendarAbbreviation($code=null){
		//logsave("get_detail getCalendarAbbreviation Code:",$code);
		$arr = array(
			'1'=>'出',		// 通常出勤日
			'2'=>'調出',		// 調整出勤日
			'3'=>'調休',	// 調整休日
			'5'=>'調客',	// 調整客先
			'6'=>'振出',	// 振替出勤
			'A'=>'交代',	// 交替勤務
			'Y'=>'年休',	// 一斉有休
			'Z'=>'休'	// 通常休日
		);
		if(empty($code)){
			return $arr;
		} else {
			if(!isset($arr[$code])){
				//logsave("get_detail getCalendarAbbreviation","Noting");
				return $code;
			}
			//logsave("get_detail getCalendarAbbreviation Ans:",$arr[$code]);
			return $arr[$code];
		}
	}
	
	// -------------------------------------------------------------------------
	//	銀行口座タイプ
	// -------------------------------------------------------------------------
	function getAccountType(){
		return array(1=>'普通', 2=>'当座');
	}

	// -------------------------------------------------------------------------
	//	退職理由
	// -------------------------------------------------------------------------
	function getResignationReason($code=null){
		$arr = array(
				1=>"自己都合",
				2=>"会社都合"
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	性別マスタ
	// -------------------------------------------------------------------------
	function getSex($code=null){
		$arr = array(1=>"男", 2=>"女");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	採用ステータスマスタ
	// -------------------------------------------------------------------------
	function getRecruitStatus($code = null){
		if($code != null){
			return $this->getOne("select APPLY_MEDIA_NAME from M_RECRUIT_STATUS where RECRUIT_STATSU_CODE=?", array($code));
		}
		// all
		$arr = array();
		$result = $this->getAllAssoc("select * from M_RECRUIT_STATUS order by SORT_NUMBER");
		foreach($result as $val){
			$arr[$val["RECRUIT_STATUS_CODE"]] = $val["RECRUIT_STATUS_NAME"];
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	//	雇用ステータスマスタ
	// -------------------------------------------------------------------------
	function getEmployeeStatus($code = null){
		if($code != null){
			return $this->getOne("select EMPLOYEE_STATUS_NAME from M_EMPLOYEE_STATUS where EMPLOYEE_STATUS_CODE=?", array($code));
		}
		// all
		$arr = array();		
		$result = $this->getAllAssoc("select * from M_EMPLOYEE_STATUS order by SORT_NUMBER");
		foreach($result as $val){
			$arr[$val["EMPLOYEE_STATUS_CODE"]] = $val["EMPLOYEE_STATUS_NAME"];
		}
		return $arr;
	}	

	// -------------------------------------------------------------------------
	//	応募媒体マスタ
	// -------------------------------------------------------------------------
	function getApplyMedia($code = null){
		if($code != null){
			return $this->getOne("select APPLY_MEDIA_NAME from M_APPLY_MEDIA where APPLY_MEDIA_CODE=?", array($code));
		}
		// all
		$arr = array();
		$result = $this->getAllAssoc("select * from M_APPLY_MEDIA order by SORT_NUMBER");
		foreach($result as $val){
			$arr[$val["APPLY_MEDIA_CODE"]] = $val["APPLY_MEDIA_NAME"];
		}		
		return $arr;
	}		
	// -------------------------------------------------------------------------
	//	エリアマスタ
	// -------------------------------------------------------------------------
	function getArea($code = null){
		if($code != null){
			return $this->getOne("select AREA_NAME from M_AREA where AREA_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select AREA_CODE, AREA_NAME from M_AREA where DEL_FLG=0 order by SORT_NUMBER");
		$arr = array();
		foreach($result as $val){
			$arr[$val["AREA_CODE"]] = $val["AREA_NAME"];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	エリアマスタ（ショート版）
	// -------------------------------------------------------------------------
	function getArea_s($code = null){
		if($code != null){
			return $this->getOne("select AREA_NAME from M_AREA_S where AREA_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select AREA_CODE, AREA_NAME from M_AREA_S where DEL_FLG=0 order by SORT_NUMBER");
		$arr = array();
		foreach($result as $val){
			$arr[$val["AREA_CODE"]] = $val["AREA_NAME"];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	都道府県マスタ
	// -------------------------------------------------------------------------
	function getPref($code = null){
		if($code != null){
			return $this->getOne("select PREF_NAME from M_PREF where PREF_CODE=?", array($code));
		}
		// all
		$result = $this->getAllAssoc("select PREF_CODE , PREF_NAME from M_PREF where DEL_FLG=0 order by PREF_CODE");	
		$arr = array();
		foreach($result as $val){
			$arr[$val["PREF_CODE"]] = $val["PREF_NAME"];
		}
		return $arr;
	}
	/**
	 * エリア別都道府県マスタ
	 */
	function getAreaPref($area_code = null){
		if($area_code != null){
			return $this->getAllAssoc("select AREA_CODE, PREF_CODE, PREF_NAME from M_PREF where AREA_CODE=?", array($area_code));
		}
		// all
		$result = $this->getAllAssoc("select AREA_CODE, PREF_CODE, PREF_NAME from M_PREF where DEL_FLG=0 order by AREA_CODE, PREF_CODE");	
		$arr = array();
		foreach($result as $val){
			$arr[$val["AREA_CODE"]][$val["PREF_CODE"]] = $val["PREF_NAME"];
		}
		return $arr;
	}
	/**
	 * 都道府県エリアマスタ
	 */
	function getPrefArea(){
		$result  = $this->getAllAssoc("select AREA_CODE, PREF_CODE from M_PREF");
		$arr = [];
		foreach($result as $val){
			$arr[$val['PREF_CODE']] = $val['AREA_CODE'];
		}
		return $arr;
	}
	// -------------------------------------------------------------------------
	//	テクノロジスト格付
	// -------------------------------------------------------------------------
	function getEvalRank($code=null){
		$arr = array(
				'S4' => 'S4',
				'S3' => 'S3',
				'S2' => 'S2',
				'S1' => 'S1',
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}

	// -------------------------------------------------------------------------
	//	入社タイプ
	// -------------------------------------------------------------------------
	function getEntryType($code=null){
		$arr = array(
				1=>"新卒",
				2=>"中途",
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	運転免許
	// -------------------------------------------------------------------------
	function getCarLicense($code=null){
		$arr = array(
				1=>"あり(MT/AT)",
				2=>"あり(AT)",
				3=>"なし"
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	希望就業タイプ
	// -------------------------------------------------------------------------
	function getConfiguration($code=null){
		$arr = array(
			1=>"一般派遣",
			2=>"紹介予定派遣",
			3=>"職業紹介",
			4=>"個人外注"
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	希望就業地域
	// -------------------------------------------------------------------------
	function getLocationType($code=null){
		$arr = array(
			1=>"全国",
			2=>"全国(地域限定あり)",
			3=>"近隣県",
			4=>"同一県内",
			5=>"現住所近隣");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	希望
	// -------------------------------------------------------------------------
	function getOutline($code=null){
		$arr = array(
			1=>"業務内容を優先する",
			2=>"勤務地を優先する",
			3=>"特に問わない");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	希望就業機関
	// -------------------------------------------------------------------------
	function getWorkPeriod($code=null){
		$arr = array(
			1=>"3ヶ月程度",
			2=>"半年程度",
			3=>"１年程度",
			4=>"１年以上"
		);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	希望就業開始
	// -------------------------------------------------------------------------
	function getWorkStart($code=null){
		$arr = array(1=>"即日");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}

	// -------------------------------------------------------------------------
	//	レポート　種別
	// -------------------------------------------------------------------------
	function rpt_getReportType($code=null){
		$arr = array(
			1=>"一般報告",
			2=>"面談報告",
			3=>"研修報告");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	レポート　タイプ
	// -------------------------------------------------------------------------
	function rpt_getContentsType($code=null){
		$arr = array(
			1=>"採用",
			2=>"営業",
			3=>"学校",
			4=>"企業",
			5=>"その他");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	レポート　方法
	// -------------------------------------------------------------------------
	function rpt_getRptMethod($code=null){
		$arr = array(
			4=>"訪問",
			5=>"来訪",
			8=>"Web会議",
			2=>"電話",
			1=>"Email",
			//3=>"Fax",
			//6=>"送付",
			7=>"その他");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	レポート　ステータス
	// -------------------------------------------------------------------------
	function rpt_getRptStatus($code=null){
		$arr = array(
			1=>"作成",
			2=>"提出",
			3=>"差し戻し",
			4=>"承認");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	レポート　ステータス
	// -------------------------------------------------------------------------
	function scl_getCategory($code=null){
		$arr = array(
			1=>"公立",
			2=>"私立",
			3=>"国立");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	お問い合わせタイプ
	// -------------------------------------------------------------------------
	function getContactType(){
		return array(
			1=>"J-jobについて",
			2=>"システム不具合",
			3=>"その他お問い合わせ"
		);
	}
	// -------------------------------------------------------------------------
	//	ログタイプ
	// -------------------------------------------------------------------------
	function getLogType($code=null){
		$arr = array(
				"1"=>"ログイン",
				"2"=>"ログアウト",
				"3"=>"閲覧",
				"4"=>"登録",
				"5"=>"編集",
				"6"=>"削除",
				"7"=>"エクスポート",
				"8"=>"インポート",
				"9"=>"バッチ",
			);
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}	

	// ログ表示用
	function getTableName(){
		return array(
				"CONTACT_TABLE"=>"お問い合わせ", 
				"EMPLOYEE_EXTENDS"=>"従業員拡張", 
				"EMPLOYEE_MASTER"=>"従業員マスタ", 
				"RPT_CONTENTS_TABLE"=>"労懇記録", 
				"SCHOOL_MASTER"=>"大学マスタ", 
				"SCHOOL_REL_TABLE"=>"大学関係者", 
				"SCHOOL_DOC_TABLE"=>"大学折衝記録", 
				"ENTERPRISE_MASTER"=>"顧客マスタ", 
				"ENTERPRISE_BRANCH_TABLE"=>"顧客事業所", 
				"ENTERPRISE_REL_TABLE"=>"顧客関係者", 
				"ENTERPRISE_DOC_TABLE"=>"顧客折衝記録", 
				"ORDER_TABLE"=>"受注リスト",

				"TCD_ACADEMIC_CAREER_INFO_TABLE"=>"学歴", 
				"TCD_BUSINESS_CAREER_INFO_TABLE"=>"職歴",
				"TCD_G_LICENSE_INFO_TABLE"=>"資格（技術）",
				"TCD_LICENSE_INFO_TABLE"=>"資格（一般）",
				"TCD_APPEAL_POINT_TABLE"=>"アピールポイント",
				"TCD_BUSINESS_EXPERIENCE_TABLE"=>"実務経験", 
				"TCD_TRAINING_CARREA_TABLE"=>"研修",
				"TCD_PERSONAL_KNOWHOW_TABLE"=>"ノウハウ",
		);
	}

	//-------------------------------------
	// 受注表
	//-------------------------------------
	// RANK
	function getOrderRank(){
		return array(
				"S"=>"S",
				"A"=>"A",
				"B"=>"B",
				"C"=>"C",
			);
	}
	// 設定工程名を返す
	function getOrderBusinessClassName($code){
		return $this->getOne("select BUSINESS_CLASS_NAME from M_BUSINESS_CLASS where BUSINESS_CLASS_CODE=?", array($code));
	}
	// 設計工程マスタの配列を返す
	function getOrderBusinessClass($job_code){
		$sql = "select JOB_CODE, BUSINESS_CLASS_CODE, BUSINESS_CLASS_NAME from M_BUSINESS_CLASS where DEL_FLG=0 and JOB_CODE=? order by SORT_NUMBER";
		$result = $this->getAllAssoc($sql, array($job_code));
		$arr = [];
		foreach($result as $val){
			$arr[$val["BUSINESS_CLASS_CODE"]] = $val["BUSINESS_CLASS_NAME"];
		}
		return $arr;
	}
	// 設定工程マスタの配列を返す（分野、設定工程の連想配列）
	function getOrderBusinessClassAll(){
		$result = $this->getAllAssoc("select JOB_CODE, BUSINESS_CLASS_CODE, BUSINESS_CLASS_NAME from M_BUSINESS_CLASS where DEL_FLG=0 order by SORT_NUMBER");
		$arr = [];
		foreach($result as $val){
			$arr[$val["JOB_CODE"]][$val["BUSINESS_CLASS_CODE"]] = $val["BUSINESS_CLASS_NAME"];
		}
		return $arr;
	}
	// 契約形態
	function getOrderContractType($code=null){
		$arr = array(
			1=>"派遣",
			2=>"請負",
			3=>"委託");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// 交渉金額
	function getOrderPriceType($code=null){
		$arr = array(
			1=>"1,000円未満",
			1000=>"1,000円～1,500円未満",
			1500=>"1,500円～2,000円未満",
			2000=>"2,000円～2,500円未満",
			2500=>"2,500円～3,000円未満",
			3000=>"3,000円～3,500円未満",
			3500=>"3,500円～4,000円未満",
			4000=>"4,000円～4,500円未満",
			4500=>"4,500円～5,000円未満",
			5000=>"5,000円～5,500円未満",
			5500=>"5,500円～6,000円未満",
			6000=>"6,000円以上");
		if(strcmp($code, "")==0){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// 受注要因
	function getOrderOrderType($code=null){
		$arr = array(
			1=>"増員",
			2=>"入れ替え",
			3=>"欠員補充");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// 結果
	function getOrderResultType($code=null){
		$arr = array(
			1=>"当社決定",
			2=>"他社決定",
			3=>"消滅",
			4=>"当社NG");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}

	function getMailType($code=null){
		$arr = array(1=>"受注リスト");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}

	/**
	 * 市区町村の候補を返す
	 * @param string $name
	 */
	function getCitySuggest($val, $pref){
		$name = str_replace(array("　"," "), "", $val);
		$kana = mb_convert_kana($name, "h", "UTF-8");	// ひらがなを半角カナへ

		$sql = "select adr2 from m_zip ".
				"where adr1=? and (adr2 like ? or adr2_kana like ?) ".
				"group by adr2 ".
				"order by zip";
		$prm = array($pref, $name."%", $kana."%");
		return $this->getAllassoc($sql, $prm);
	}
	/**
	 * 町名の候補を返す
	 * @param string $name
	 */
	function getTownSuggest($val, $pref, $city){
		$name = str_replace(array("　"," "), "", $val);
		$kana = mb_convert_kana($name, "h", "UTF-8");	// ひらがなを半角カナへ

		$sql = "select adr3 from m_zip ".
				"where adr1=? and adr2=? and (adr3 like ? or adr3_kana like ?) and adr3<>'以下に掲載がない場合'".
				"group by adr3 ".
				"order by zip";
		$prm = array($pref, $city, $name."%", $kana."%");
		return $this->getAllassoc($sql, $prm);
	}

	function getPriorityLevel($code=null){
		$arr = array(
			"A"=>"A:高",
			"B"=>"B:中",
			"C"=>"C:低");
		if(empty($code)){
			return $arr;
		} else {
			return $arr[$code];
		}
	}
	// -------------------------------------------------------------------------
	//	TCDのテーブル情報を返す
	// -------------------------------------------------------------------------
	function getTcdTableArr(){
		return array(
			"TCD_ACADEMIC_CAREER_INFO_TABLE" => "学歴", 
			"TCD_BUSINESS_CAREER_INFO_TABLE" => "職歴", 
			"TCD_LICENSE_INFO_TABLE" => "資格（技術）", 
			"TCD_G_LICENSE_INFO_TABLE" => "資格（一般）", 
			"TCD_APPEAL_POINT_TABLE" => "アピールポイント", 
			"TCD_BUSINESS_EXPERIENCE_TABLE" => "業務経歴", 
			"TCD_KNOWLEDGE_EXPERIENCE_TABLE" => "業務経歴（知識）", 
			"TCD_TOOL_EXPERIENCE_TABLE" => "業務経歴（ツール）", 
			"TCD_CLASS_EXPERIENCE_TABLE" => "業務経歴（設計工程）", 
			"TCD_TRAINING_CARREA_TABLE" => "研修", 
			"TCD_TRAINING_KNOWLEDGE_TABLE" => "研修（知識）", 
			"TCD_TRAINING_TOOL_TABLE" => "研修（ツール）",
			"TCD_PERSONAL_KNOWHOW_TABLE" => "ノウハウ", 
		);
	}




	//-------------------------------------------------------------------------------------------------
	//
	// 集計用
	// 
	//-------------------------------------------------------------------------------------------------
	/**
	 * 集計用の営業所マスタ
	 * 営業所マスタテーブルにソートがないので関数化。変更があったらここを直接修正する必要があります。
	 */
	function getOfficeSum($company_code=null){
		$arr['001']['001'] = '札幌';
		$arr['001']['009'] = '水戸';
		$arr['001']['005'] = '東京第一';
		$arr['001']['141'] = '東京第二';
		$arr['001']['012'] = '浜松';
		$arr['001']['010'] = '名古屋';
		$arr['001']['014'] = '大阪';
		$arr['001']['017'] = '福岡';
		if(!empty($company_code)){
			return $arr[$company_code];
		} 
		return $arr;
	}
	/**
	 * 受注集計表用の営業担当リスト
	 * 従業員マスタテーブルに営業担当を絞り込む条件がないので関数化。変更があったらここを直接修正する必要があります。
	 */
	function getOrderEigyoTanto(){
		$arr['0200574'] = '横山　浩治';
		$arr['2007102'] = '木谷　勝';
		$arr['3300900'] = '髙橋　宣光';
		$arr['2016011'] = '北虎　大樹';
		$arr['3300890'] = '松本　成基';
		$arr['3300903'] = '篠田　政司';
		$arr['2015048'] = '中野　貴弘';
		$arr['2007087'] = '橋本　英明';
		$arr['2015015'] = '菊地　佑太';
		$arr['2016024'] = '岩川　信彦';
		$arr['3300832'] = '江原　紀行';
		$arr['3300899'] = '染谷　政和';
		$arr['3300886'] = '朝倉　あす香';
		$arr['2017003'] = '小林　明日香';
		$arr['2017004'] = '政岡　沙希';
		return $arr;
	}
	/**
	 * 分野マスタ・集計用
	 */
	function getJobSum($code=null){
		$arr = array(1=>"機械", 3=>"電気/電子", 2=>"ソフト", 9=>"その他");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}
	/**
	 * 分野マスタ(略)・集計用
	 */
	function getJobSumShort($code=null){
		$arr = array(1=>"M", 3=>"E", 2=>"S", 9=>"その他");
		if($code != null){
			return $arr[$code];
		}
		return $arr;
	}



















	// -------------------------------------------------------------------------
	// 契約区分
	// -------------------------------------------------------------------------
	function getContractDivision($code=null){
		$arr = array(
			0=>"月額固定",
			1=>"時間単価");
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 勤務形態
	// -------------------------------------------------------------------------
	function getDutyForm($code=null){
		$arr = array(
			//0=>'通常勤務',				// 旧管理台帳
			//2=>'交替制勤務',				// 旧管理台帳
			3=>'通常勤務（開始時刻固定）',
			4=>'通常勤務（精算時間固定）',
			1=>'フレックス勤務',
		);
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 個別契約書状態
	// -------------------------------------------------------------------------
	function getContractStatus($code=null){
		$arr = array(
			0=>"未作成",
			1=>"自社押印待ち",
			2=>"客先押印待ち",
			3=>'作成済',
			4=>'未完了');
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 申請ステータス
	// -------------------------------------------------------------------------
	function getApprovalDivision($code=null, $denialFlg=true){
		$arr = array(
			1=>"承認待ち",
			0=>"承認");
		if($denialFlg){
			$arr += array(2=>"否認");
		}
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 届け単位区分
	// -------------------------------------------------------------------------
	function getApprovalUnitDivision($code=null){
		$arr = array(
			1=>"時間",
			2=>"日",
			3=>"半日");
		if(empty($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 端数区分
	// -------------------------------------------------------------------------
	function getFractionDivision($code=null){
		$arr = array(
			0=>"四捨五入",
			1=>"切捨て",
			2=>"切上げ");
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}

	// -------------------------------------------------------------------------
	// 端数丸め位置
	// -------------------------------------------------------------------------
	function getFractionPosition($code=null){
		$arr = array(
			1=>" 小数第１位",
			2=>" 小数第２位",
			3=>" 小数第３位");
		if(empty($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// 時間丸め区分
	// -------------------------------------------------------------------------
	function getTimeRoudingKbn($code=null){
		$arr = array(
			0=>"丸めなし",
			1=>"１５分",
			2=>"３０分");
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}

	// -------------------------------------------------------------------------
	// 工数区分
	// -------------------------------------------------------------------------
	function getManHours($code=null){
		$arr = array(
					0=>array('WAGES', '基準内工数'),
					1=>array('LACK', '不足工数'),
					2=>array('EXCESS', '超過工数'),
					3=>array('INSIDE', '所定内工数'),
					4=>array('OUTSIDE', '時間外工数'),
					5=>array('IN_LEGAL', '法定内休日工数'),
					6=>array('OUT_LEGAL', '法定外休日工数'),
					7=>array('LATE_NIGHT', '深夜工数'),
					8=>array('OVER_LIMIT_45', '限度時間外工数(45-60h)'),
					9=>array('OVER_LIMIT_60', '限度時間外工数(60h-)'),
				);
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}

	// -------------------------------------------------------------------------
	// 経費区分
	// -------------------------------------------------------------------------
	function getExpense($code=null){
		$arr = array(
					0=>"出張旅費",
					1=>"その他"
				);
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}

	// -------------------------------------------------------------------------
	// 税区分
	// -------------------------------------------------------------------------
	function getTaxMethod($code=null){
		$arr = array(
					0=>"税込",
					1=>"税別"
				);
		if(!isset($code)){
			return $arr;
		}
		return $arr[$code];
	}
	// -------------------------------------------------------------------------
	// ファイルタイプ
	// -------------------------------------------------------------------------
	function getFileType(){
		return array(1=>'お知らせ', 2=>'eラーニング');
	}
	// -------------------------------------------------------------------------
	// 月報回答
	// -------------------------------------------------------------------------
	function getReportAnswer1(){
		return array(1=>'非常に満足', 2=>'満足', 3=>'やや満足', 4=>'やや不満', 5=>'不満', 6=>'非常に不満');
	}
	function getReportAnswer2(){
		return array(1=>'非常に多い', 2=>'多い', 3=>'やや多い', 4=>'やや少ない', 5=>'少ない', 6=>'非常に少ない');
	}
	function getReportAnswer3(){
		return array(1=>'非常に高い', 2=>'高い', 3=>'やや高い', 4=>'やや低い', 5=>'低い', 6=>'非常に低い');
	}
	function getReportAnswer4(){
		return array(1=>'長期継続したい', 2=>'継続したい', 3=>'ひとまず継続', 4=>'あまり継続したくない', 5=>'終了したい', 6=>'すぐに終了したい');
	}
}

