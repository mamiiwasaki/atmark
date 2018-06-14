<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version ushizawa
 *	2014-07-01 : rebuild
 *
 *	validator class
 *----------------------------------------------------------------------------*/
define("CHARSET", "EUC-JP");

/**
 * フォームから入力されたデータのチェックを行う
 */
class validator {
	var $data;
	var $errors = array();

	//-------------------------------------------------------------------------
	//	エラーメッセージのリストを作成する
	//-------------------------------------------------------------------------
	function makeErrorList() {
		// エラーメッセージ
		if(!empty($this->errors)) {
			foreach($this->errors As $key=>$message) {
				if(!empty($message)) $tmp[$key] = "<span style='cursor:pointer;' onClick=\"$('{$key}').focus();\">".$message."</span>\n";
			}
			return "<div id=\"error_list\">".implode("<br>", $tmp)."</div><br />";
	
		} else {
			return "";
		}
	}
	
	/**
	 * すべてPOSTデータをエスケープする
	 * @return boolean
	 */
	function escape(){
		if(empty($this->data)) return true;
		
		$pattern = "/<script.*?>|<\/script>|javascript:/i";
		$convert = '#script tag escaped#';
		
		foreach($this->data as $key => $val){
			
			if(is_array($val)) continue;
			
			if (preg_match_all($pattern, $val, $matches)) {
				$this->data[$key] = preg_replace($pattern, $convert, $val);
			}
		}
	}
	/**
	 * フォーム入力チェック
	 * @param string $fieldName
	 * @param string $rules
	 * @param string $message
	 * @return boolean
	 */
	function validate($fieldName, $rules, $message) {
		
		// 2つ以上チェック項目がある場合
		if(!empty($this->errors[$fieldName])) return true;

		// rule, val
		$rule = $rules[0];
		$val = (isset($this->data[$fieldName])) ? $this->data[$fieldName] : "";
		if(!is_array($val)) $val = trim($val);
		
		// 入力チェック以外で空できたら、スルー		
		if($rule!="checkEmpty" && empty($val)) return true;
		
		// 半角チェックだったら、全角＝＞半角にする処理
		if($rule=="checkNum"){
			$this->data[$fieldName] = mb_convert_kana($this->data[$fieldName], "n", CHARSET);
			$val = $this->data[$fieldName];
		}

		
		switch( $rule ) {
			// 入力必須
			case "checkEmpty":
			//	if(empty($val)) $this->errors[$fieldName] = $message;	
				if(is_array($val)){
					if(empty($val)) $this->errors[$fieldName] = $message;
				} else {
					if(empty($val) && strcmp($val,0)!==0) $this->errors[$fieldName] = $message; // emptyだと0 が未入力と判定されてしまうので
				}
				break;
	
			// ID、パスワード 何桁〜何桁
			case "checkAccount":
				if(!$this->checkAccount($val, $rules[1], $rules[2])){
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// 2つのデータの比較
			case "checkCompare":
				if(!$this->checkCompare($val, $rules[1], $rules[2])){
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// パスワードなどの入力確認用データとのチェック
			case "checkConfirm":
				if(!$this->checkCompare($val, $this->data[$fieldName."_confirm"]))	{
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// ユニークチェック
			case "checkUnique":
				if(!empty($rules[3])) {
					$res = $this->checkUnique($val, $rules[1], $rules[2], $rules[3]);
				} else {
					$res = $this->checkUnique($val, $rules[1], $rules[2]);
				}
				if(!$res){
					$this->errors[$fieldName] = $message;
				}
				break;
	
	
			// 正規表現指定
			case "checkSeiki":
				if(!$this->checkSeiki($val, $rules[1]))	{
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// デフォルト その他
			default:
				$rule = $rules[0];
				if(isset($rules[2])) {			// パラメータ2つ
					if(!$this->$rule($val, $rules[1], $rules[2])){
						$this->errors[$fieldName] = $message;
					}
				} else if(isset($rules[1])) {	// パラメータ１つ
					if(!$this->$rule($val, $rules[1])){
						$this->errors[$fieldName] = $message;
					}
				} else {						// パラメータなし
					if(!$this->$rule($val)) {
						$this->errors[$fieldName] = $message;
					}
				}
				break;
		}
		return true;
	}
	
	
	
	
	
	#----------------------------------------------------------------
	# 半角数字
	#----------------------------------------------------------------
	function checkNum($val) {
		//$val = mb_convert_kana($val, "n", CHARSET);
		return (is_numeric($val)) ? true : false;
	}	
	#----------------------------------------------------------------
	# ユニーク データの重複チェック
	#----------------------------------------------------------------
	function checkUnique($val, $tablename, $colname, $id=null) {
		$common_dao = new common_dao();

		// idのカラム名を取得
		$con = $common_dao->query("SHOW COLUMNS FROM $tablename");
		for($i=0; $i<$con->numrows(); $i++){
			$tmp = $con->fetch();
			if($tmp["Key"] == "PRI"){
				$idname = $tmp["Field"];
				break;
			}
		}

		$where_str = $colname."='{$val}' AND del_flg=0";
		if(!empty($id)) $where_str .= " AND $idname<>".$id;
		$cnt = $common_dao->getOne("Count(*)", $tablename, $where_str);
		
		return ($cnt==0) ? true : false;	
	}
	#----------------------------------------------------------------
	# 日付チェック
	#----------------------------------------------------------------
	function checkDate($val) {
		if($val=="0000-00-00") return true;	
		if(!preg_match("/^\d{4}(-|\/)\d{1,2}(-|\/)\d{1,2}$/", $val)) return false;
	
		$sep = substr($val, 4,1);
		$tmp = explode($sep, $val);
		return checkdate($tmp[1], $tmp[2], $tmp[0]);	
	}	
	#----------------------------------------------------------------
	# アカウント
	#----------------------------------------------------------------
	function checkAccount($val, $keta1, $keta2) {
		return preg_match("/^[0-9A-Za-z\-\/\_]{{$keta1},{$keta2}}$/", $val) ? true : false;
	}	
	#----------------------------------------------------------------
	# 最大桁、最小桁
	#----------------------------------------------------------------
	function maxLength($val, $max) {
		return (mb_strlen($val, CHARSET) <= $max);
	}
	function minLength($val, $min) {
		return (mb_strlen($val, CHARSET) >= $min);
	}	
	#----------------------------------------------------------------
	# 比較
	#----------------------------------------------------------------
	function checkCompare($val1, $val2, $togo="=") {		
		if(empty($val2)) return true;
		switch($togo) {
			case "=":
			case "equal": 
				return trim($val1) == trim($val2); 
				break;
			case ">": return trim($val1) > trim($val2);	break;	
			case "<": return trim($val1) < trim($val2); break;
			case ">=": return trim($val1) >= trim($val2); break;	
			case "<=": return trim($val1) <= trim($val2); break;
		}
	}
	#----------------------------------------------------------------
	# 桁
	#----------------------------------------------------------------
	function checkKeta($val, $keta) {
		return (mb_strlen($val)==$keta) ? true : false;
	}
	#----------------------------------------------------------------
	# 任意の式
	#----------------------------------------------------------------
	function checkSeiki($val, $seiki) {
		return preg_match($seiki, $val) ? true : false;
	}
	#----------------------------------------------------------------
	# Tel
	#----------------------------------------------------------------
	function checkTel($val) {
		if(empty($val)) return true;
		return preg_match('/\d{2,5}-\d{2,4}-\d{3,4}$/', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# tel2　ハイフンなし
	#----------------------------------------------------------------
	function checkTel2($val) {
		if(empty($val)) return true;
		return preg_match('/\d{10,12}$/', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# time
	#----------------------------------------------------------------
	function checkTime($val) {
		if(empty($val)) return true;
		return ereg('^([0-1][0-9]|[2][0-3]):[0-5][0-9]$', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# PostNo
	#----------------------------------------------------------------
	function checkPostNo($val) {
		if(empty($val)) return true;
		return preg_match("/^\d{3}\-\d{4}$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# url
	#----------------------------------------------------------------
	function checkURL($val) {
		if(empty($val)) return true;
		return preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# mail
	#----------------------------------------------------------------
	function checkEmail($val) {
		if(empty($val)) return true;
		return preg_match("/^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# 全角カナ
	#----------------------------------------------------------------
	function checkKana($val) {
		//$val = mb_convert_encoding($val, "UTF-8", "EUC-JP");
		//mb_regex_encoding("UTF-8");
		// 半角も通す。
		$val = $this->han_kaku_to_jen_kaku($val);	
		//return (preg_match("/^(\xe3(\x82[\xa1-\xbf]|\x83[\x80-\xb6]|\x83\xbc))*$/",$val))? true: false;	
		return (preg_match("/^(\xa5[\xa1-\xf6]|\xa1[\xb3\xb4\xbc])+$/",$val))? true: false;	// euc
		//return (preg_match("/^[ァ-ヶー]+$/u", $val))? true: false; // utf08
	}	
	#----------------------------------------------------------------
	# ひらがな
	#----------------------------------------------------------------
	function checkHiragana($val) {
		mb_regex_encoding("UTF-8");  
		return (preg_match("/^[ぁ-ん]+$/u", $val))? true: false;	
	}
	#----------------------------------------------------------------
	# 半角英数字
	#----------------------------------------------------------------
	function checkEisu($val) {
		$val = mb_convert_kana($val, "n", "EUC-JP");
		return preg_match("/^[0-9A-Za-z \t\/._-]+$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# ファイルサイズチェック
	#----------------------------------------------------------------
	function checkFileSize($val, $size)	{
	
	//////////////////////////////////////	if(empty($this->data[$this->name]['file_'.$field]['name'])) return true;
		if(empty($val)) return true;
	
	}

	#----------------------------------------------------------------
	# 年号と日付の整合性をチェック
	#----------------------------------------------------------------
	function checkNengo($data, $nengo, $yy, $mm, $dd) {
	
		$nengo 	= $this->data[$this->alias][$nengo];
		$yy 		= $this->data[$this->alias][$yy];
		$mm 		= $this->data[$this->alias][$mm];
		$dd 		= $this->data[$this->alias][$dd];
	
		if($nengo=="" || $yy=="" || $mm=="" || $dd=="") return true;
	
		switch($nengo){
			case "明治": $yy+=1867; break;
			case "大正": $yy+=1911; break;
			case "昭和": $yy+=1925; break;
			case "平成": $yy+=1988; break;
		}
	
		// 日付の整合性
		if(!checkdate($mm, $dd, $yy)) return false;
	
		if($nengo=="平成" && "{$yy}/{$mm}/{$dd}">="1989/1/8" && $yy<=date("Y")){
			$flg = true;
		} else if($nengo=="昭和" && "{$yy}/{$mm}/{$dd}">="1925/12/25" && "{$yy}/{$mm}/{$dd}"<="1989/1/7"){
			$flg = true;
		} else if($nengo=="大正" && "{$yy}/{$mm}/{$dd}">="1912/7/30" && "{$yy}/{$mm}/{$dd}"<="1926/12/24"){
			$flg = true;
		} else if($nengo=="明治" && "{$yy}/{$mm}/{$dd}">="1867/7/29"){
			$flg = true;
		} else {
			$flg = false;
		}
		return $flg;
	
	}
	
	#----------------------------------------------------------------
	# 半角カナ→全角カナ
	#----------------------------------------------------------------
	function han_kaku_to_jen_kaku($val) {
			$replace_of = array('ｳﾞ','ｶﾞ','ｷﾞ','ｸﾞ','ｹﾞ','ｺﾞ','ｻﾞ','ｼﾞ','ｽﾞ','ｾﾞ','ｿﾞ','ﾀﾞ','ﾁﾞ','ﾂﾞ','ﾃﾞ','ﾄﾞ','ﾊﾞ','ﾋﾞ','ﾌﾞ','ﾍﾞ','ﾎﾞ','ﾊﾟ','ﾋﾟ','ﾌﾟ','ﾍﾟ','ﾎﾟ');
			$replace_by = array('ヴ','ガ','ギ','グ','ゲ','ゴ','ザ','ジ','ズ','ゼ','ゾ','ダ','ヂ','ヅ','デ','ド','バ','ビ','ブ','ベ','ボ','パ','ピ','プ','ペ','ポ');
			$_result = str_replace($replace_of, $replace_by, $val);
			
			$replace_of = array('ｱ','ｲ','ｳ','ｴ','ｵ','ｶ','ｷ','ｸ','ｹ','ｺ','ｻ','ｼ','ｽ','ｾ','ｿ','ﾀ','ﾁ','ﾂ','ﾃ','ﾄ','ﾅ','ﾆ','ﾇ','ﾈ','ﾉ','ﾊ','ﾋ','ﾌ','ﾍ','ﾎ',
			    'ﾏ','ﾐ','ﾑ','ﾒ','ﾓ','ﾔ','ﾕ','ﾖ','ﾗ','ﾘ','ﾙ','ﾚ','ﾛ','ﾜ','ｦ','ﾝ','ｧ','ｨ','ｩ','ｪ','ｫ','ヵ','ヶ','ｬ','ｭ','ｮ','ｯ','､','｡','ｰ',
			    '｢','｣','ﾞ','ﾟ'	);
			$replace_by = array('ア','イ','ウ','エ','オ','カ','キ','ク','ケ','コ','サ','シ','ス','セ','ソ','タ','チ','ツ','テ','ト','ナ','ニ','ヌ','ネ','ノ','ハ','ヒ','フ','ヘ','ホ',
			    'マ','ミ','ム','メ','モ','ヤ','ユ','ヨ','ラ','リ','ル','レ','ロ','ワ','ヲ','ン','ァ','ィ','ゥ','ェ','ォ','ヶ','ヶ','ャ','ュ','ョ','ッ','、','。','ー',
			    '「','」','”',''	); 
			$_result = str_replace($replace_of, $replace_by, $_result);
			return $_result;
	}
	
	/*
	
	// 画像が登録・変更されたかをチェック
	function checkUpload($fieldname) {
		$model = $this->name;
		$file = $this->data[$model][$fieldname];
	
		if(!empty($this->data[$model]['id'])) {
			$ex = $this->field($fieldname, "id=".$this->data[$model]['id']);
	echo $ex."---";
	echo $file."<br>";
	
			if($ex!=$file) {
				return true;
			}
		}
		return false;
	}
*/
	/**
	 * 日付が３つに分かれた入力フォーム　
	 * 年・月・日すべて選択されているかどうかをチェックする
	 * @param string $year_name
	 * @param string $month_name
	 * @param string $day_name
	 * @param string $msg
	 * @return boolean
	 */
	function checkDate3($year_name, $month_name, $day_name, $msg) {
		if(empty($this->data[$year_name]) && empty($this->data[$month_name]) && empty($this->data[$day_name])) {
			return true;
		}
		
		if(((!empty($this->data[$year_name]) && (empty($this->data[$month_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$month_name]) && (empty($this->data[$year_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$day_name]) && (empty($this->data[$year_name]) || empty($this->data[$month_name])))
		) || !checkdate($this->data[$month_name], $this->data[$day_name], $this->data[$year_name])	
		) {
			$this->errors[$year_name] = $msg;
			$this->errors[$month_name] = "";
			$this->errors[$day_name] = "";
		}
	}
		
	/**
	 * TELが３つに分かれた入力フォーム　
	 * tel01・tel02・tel03すべて入力されているかどうかをチェックする
	 * @param int $name1
	 * @param int $name2
	 * @param int $name3
	 * @param string $msg
	 * @return boolean
	 */
	function checkTel3($name1, $name2, $name3, $hiss_flg=false, $name="電話番号") {
		
		if(!$hiss_flg && empty($this->data[$name1]) && empty($this->data[$name2]) && empty($this->data[$name3])) {
			// 入力必須項目でなく、すべて空だったらスルー
			return true;
		}

		$msg = $name."を正しく入力してください";
		
		// 一応　全角→半角の処理
		if(!empty($this->data[$name1])) $this->data[$name1] = mb_convert_kana($this->data[$name1], "n", CHARSET);
		if(!empty($this->data[$name2])) $this->data[$name2] = mb_convert_kana($this->data[$name2], "n", CHARSET);
		if(!empty($this->data[$name3])) $this->data[$name3] = mb_convert_kana($this->data[$name3], "n", CHARSET);

		$flg = true;
		if($hiss_flg && (empty($this->data[$name1]) || empty($this->data[$name2]) || empty($this->data[$name3]))) $flg = false;
			
		if(!empty($this->data[$name1]) && (empty($this->data[$name2]) || empty($this->data[$name3]))) $flg = false;
		if(!empty($this->data[$name2]) && (empty($this->data[$name1]) || empty($this->data[$name3]))) $flg = false;
		if(!empty($this->data[$name3]) && (empty($this->data[$name2]) || empty($this->data[$name1]))) $flg = false;
		if(!empty($this->data[$name1]) && !$this->checkNum($this->data[$name1])) $flg = false;
		if(!empty($this->data[$name2]) && !$this->checkNum($this->data[$name2])) $flg = false;
		if(!empty($this->data[$name3]) && !$this->checkNum($this->data[$name3])) $flg = false;
		
		if(!$flg){
			$this->errors[$name1] = $msg;
			$this->errors[$name2] = "";
			$this->errors[$name3] = "";
		}
	}
}
?>
