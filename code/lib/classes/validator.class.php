<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version ushizawa
 *	2014-07-01 : rebuild
 *
 *	validator class
 *
 * ーーーーーー 使い方 例 ーーーーーー
 * $validator->validate("adviser_id", array("checkEmpty"), "IDを入力してください");		// 必須項目チェック
 * $validator->validate("adviser_id", array("checkEisu"), "IDを英数字入力してください");	// 英数字チェック
 * $validator->validate("account", array("checkAccount", 4, 10), "アカウントは４桁〜１０桁の英数字で入力してください");
 * $validator->validate("hour", array("checkRange", 0, 24), "テスト時間は０〜２４で入力してください");
 * $validator->validate("your_height", array("minLength", 0, 140), "身長は140cm以上で入力してください");
 * $validator->validate("test", array("checkSeiki", "/^[0-9-]{10,15}$/"), "テストを確認してください");
 *
 * checkEmpty		必須
 * checkAccount		ID、パスワード 何桁〜何桁
 * checkComparer	2つのデータの比較
 * checkConfirm		入力確認用データとのチェック(パスワードなど)
 * checkSeiki		正規表現指定
 * checkNum			数値
 * checkDate		日付
 * checkTime		時間
 * maxLength			最大文字数
 * minLength			最小文字数
 * checkRange		範囲指定
 * checkKeta			桁
 * checkTel			電話番号
 * checkTel2		電話番号
 * checkZip			郵便番号
 * checkURL			URL
 * checkEmail		Email
 * checkKana			カナ
 * checkHiragana		ひらがな
 * checkKanji		漢字
 * checkEisu		英数
 * checkRate		率
 * checkFileSize	ファイルサイズ
 *----------------------------------------------------------------------------*/

/**
 * フォームから入力されたデータのチェックを行う.
 */
class validator
{
	public $data;
	public $errors = array();

	//-------------------------------------------------------------------------
	//	エラーメッセージのリストを作成する
	//-------------------------------------------------------------------------
	public function makeErrorList(){
		// エラーメッセージ
		if (!empty($this->errors)) {
			foreach ($this->errors as $key => $message) {
				if (!empty($message)) {
					$tmp[$key] = "<span style='cursor:pointer;color:#994545;' onClick=\"document.getElementById('{$key}').focus();\">".$message."</span>\n";
				}
			}

			$style = 'background-color:#cfa6a6;border-top:1px dashed #994545;'.
						'border-bottom:1px dashed #994545;color:red;text-align:left;padding:8px;';

			return "<div style='{$style}'>".implode('<br>', $tmp).'</div><br />';

			return $str;
		} else {
			return '';
		}
	}

	/**
	 * すべてのデータをエスケープする.
	 *
	 * @return bool
	 */
	public function escape(){
		if (empty($this->data)) {
			return true;
		}

		$pattern_arr = array('<', '>', '¥', '〜', '~', "'", '"', '\\');
		$convert_arr = array('＜', '＞', '￥', 'ー', 'ー', '’', '”', '￥');

		foreach ($this->data as $key => $val) {
			if (is_array($val)) {
				continue;
			}

			// 絵文字を[emoji]に置換する
			$val = preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '[emoji]', $val);

			//if (preg_match($pattern, $val)) {
				$this->data[$key] = str_replace($pattern_arr, $convert_arr, $val);
			//}
		}
	}
	/**
	 * フォーム入力チェック.
	 *
	 * @param string $fieldName
	 * @param string $rules
	 * @param string $message
	 *
	 * @return bool
	 */
	public function validate($fieldName, $rules, $message){
		// 2つ以上チェック項目がある場合
		if (!empty($this->errors[$fieldName])) {
			return true;
		}

		// rule, val
		$rule = $rules[0];
		$val = (isset($this->data[$fieldName])) ? $this->data[$fieldName] : '';

		// 入力チェック以外で空できたら、スルー
		//if ($rule != 'checkEmpty' && empty($val)) {
		if ($rule != 'checkEmpty' && strcmp($val, '')===0) {
			return true;
		}

		// 半角チェックだったら、全角＝＞半角にする処理
		if ($rule == 'checkNum' || $rule == 'checkDate' || $rule == 'checkTime' ||
			$rule == 'checkTel' || $rule == 'checkTel2' || $rule == 'checkZip' ||
			$rule == 'checkEmail' || $rule == 'checkURL' || $rule == 'checkEisu' || $rule == 'checkRate') {
			$this->data[$fieldName] = mb_convert_kana($this->data[$fieldName], 'a', CHARSET);
			$this->data[$fieldName] = str_replace('−', '-', $this->data[$fieldName]);
			$val = $this->data[$fieldName];
		}

		switch ($rule) {
			// 入力必須
			case 'checkEmpty':
			//	if(empty($val)) $this->errors[$fieldName] = $message;
				if (is_array($val)) {
					if (!isset($val)) {
						$this->errors[$fieldName] = $message;
					}
				} else {

				//	if(!isset($val) || strcmp($val,0)==0 || strcmp($val,"")==0) { // emptyだと0 が未入力と判定されてしまうので
					if (!isset($val) || strcmp($val, '') == 0) { // emptyだと0 が未入力と判定されてしまうので
						//echodebug($fieldName."---".$val);
						$this->errors[$fieldName] = $message;
					}
				}
				break;

			// ID、パスワード 何桁〜何桁
			case 'checkAccount':
				if (!$this->checkAccount($val, $rules[1], $rules[2])) {
					$this->errors[$fieldName] = $message;
				}
				break;

			// 2つのデータの比較
			case 'checkCompare':
				if (!$this->checkCompare($val, $rules[1], $rules[2])) {
					$this->errors[$fieldName] = $message;
				}
				break;
			
			// 日付の比較
			case 'compareDate':
				if (!$this->compareDate($rules[1], $rules[2],$message)) {
					$this->errors[$fieldName] = $message;
				}
				break;
			
			// 時間の比較
			case 'compareTime':
				if (!$this->compareTime($rules[1], $rules[2],$message)) {
					$this->errors[$fieldName] = $message;
				}
				break;
			case 'compareTime2':
				if (!$this->compareTime2($rules[1], $rules[2],$message)) {
					$this->errors[$fieldName] = $message;
				}
				break;

			// パスワードなどの入力確認用データとのチェック
			case 'checkConfirm':
				if (!$this->checkCompare($val, $this->data[$fieldName.'_confirm'])) {
					$this->errors[$fieldName] = $message;
				}
				break;

			// 正規表現指定
			case 'checkSeiki':
				if (!$this->checkSeiki($val, $rules[1])) {
					$this->errors[$fieldName] = $message;
				}
				break;

			// デフォルト その他
			default:
				$rule = $rules[0];
				if (isset($rules[2])) {            // パラメータ2つ
					if (!$this->$rule($val, $rules[1], $rules[2])) {
						$this->errors[$fieldName] = $message;
					}
				} elseif (isset($rules[1])) {    // パラメータ１つ
					if (!$this->$rule($val, $rules[1])) {
						$this->errors[$fieldName] = $message;
					}
				} else {                        // パラメータなし
					if (!$this->$rule($val)) {
						$this->errors[$fieldName] = $message;
					}
				}
				break;
		}

		return true;
	}

	//----------------------------------------------------------------
	// 半角数字
	//----------------------------------------------------------------
	public function checkNum($val){
		//$val = mb_convert_kana($val, "n", CHARSET);
		//return preg_match("/^\d+$/", $val);
		return is_numeric($val);
	}
	//----------------------------------------------------------------
	// 日付チェック
	//----------------------------------------------------------------
	public function checkDate($val){
		if ($val == '0000-00-00') {
			return true;
		}
		if (!preg_match("/^\d{4}(-|\/)\d{1,2}(-|\/)\d{1,2}$/", $val)) {
			return false;
		}

		$sep = substr($val, 4, 1);
		$tmp = explode($sep, $val);

		return checkdate($tmp[1], $tmp[2], $tmp[0]);
	}
	//----------------------------------------------------------------
	// アカウント
	//----------------------------------------------------------------
	public function checkAccount($val, $keta1, $keta2){
		return preg_match("/^[0-9A-Za-z\-\/\_]{{$keta1},{$keta2}}$/", $val);
	}
	//----------------------------------------------------------------
	// 最大桁、最小桁
	//----------------------------------------------------------------
	public function maxLength($val, $max){
		return mb_strlen($val, CHARSET) <= $max;
	}
	public function minLength($val, $min)
	{
		return mb_strlen($val, CHARSET) >= $min;
	}
	//----------------------------------------------------------------
	// 範囲指定
	//----------------------------------------------------------------
	public function checkRange($val, $min, $max){
		return $val >= $min && $val <= $max;
	}
	//----------------------------------------------------------------
	// 比較
	//----------------------------------------------------------------
	public function checkCompare($val1, $val2, $togo = '='){
		if (empty($val2)) {
			return true;
		}
		switch ($togo) {
			case '=':
			case 'equal':
				return trim($val1) == trim($val2);
				break;
			case '>': return trim($val1) > trim($val2); break;
			case '<': return trim($val1) < trim($val2); break;
			case '>=': return trim($val1) >= trim($val2); break;
			case '<=': return trim($val1) <= trim($val2); break;
		}
	}
	//----------------------------------------------------------------
	// 桁
	//----------------------------------------------------------------
	public function checkKeta($val, $keta){
		return (mb_strlen($val) == $keta);
	}
	//----------------------------------------------------------------
	// 任意の式
	//----------------------------------------------------------------
	public function checkSeiki($val, $seiki){
		return preg_match($seiki, $val);
	}
	//----------------------------------------------------------------
	// Tel
	//----------------------------------------------------------------
	public function checkTel($val){
		if (strcmp($val, '')===0) {
			return true;
		}
		return preg_match('/^\d{2,5}-\d{2,4}-\d{3,4}$/', $val) ;
	}
	//----------------------------------------------------------------
	// tel2　ハイフンなし
	//----------------------------------------------------------------
	public function checkTel2($val){
		if (strcmp($val, '')===0) {
			return true;
		}
		return preg_match('/^\d{10,12}$/', $val);
	}
	//----------------------------------------------------------------
	// time
	//----------------------------------------------------------------
	public function checkTime($val){
		if (strcmp($val, '')===0) {
			return true;
		}
		return preg_match('/^([0-1][0-9]|[2][0-3]):[0-5][0-9]$/', $val);
	}
	//----------------------------------------------------------------
	// Zip
	//----------------------------------------------------------------
	public function checkZip($val){
		if (strcmp($val, '')===0) {
			return true;
		}

		return preg_match("/^\d{3}\-\d{4}$/", $val) ;
	}
	//----------------------------------------------------------------
	// url
	//----------------------------------------------------------------
	public function checkURL($val){
		if (strcmp($val, '')===0) {
			return true;
		}
		if(substr($val)>100) return false;	// すごく長かったらエラーにする
		return preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", $val);
	}
	//----------------------------------------------------------------
	// mail
	//----------------------------------------------------------------
	public function checkEmail($val){
		if (strcmp($val, '')===0) {
			return true;
		}
		if(strlen($val)>100) return false;	// すごく長かったらエラーにする
		//return preg_match("/^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $val) ? true : false;
		// 「?」って使える？20150617
		return preg_match("/^[_a-z0-9-.?]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $val) ;
	}
	//----------------------------------------------------------------
	// 全角カナ
	//----------------------------------------------------------------
	public function checkKana($val){
		//$val = mb_convert_encoding($val, "UTF-8", "EUC-JP");
		//mb_regex_encoding("UTF-8");
		// 半角も通す。
		$val = $this->han_kaku_to_jen_kaku($val);
		//return (preg_match("/^(\xe3(\x82[\xa1-\xbf]|\x83[\x80-\xb6]|\x83\xbc))*$/",$val))? true: false;
		//return (preg_match("/^(\xa5[\xa1-\xf6]|\xa1[\xb3\xb4\xbc])+$/",$val))? true: false;	// euc
		return preg_match('/^[ァ-ヶー]+$/u', $val) ; // utf08
	}
	//----------------------------------------------------------------
	// ひらがな
	//----------------------------------------------------------------
	public function checkHiragana($val){
		//mb_regex_encoding("UTF-8");
		//mb_internal_encoding("UTF8");
		//$val = mb_convert_encoding($val, "UTF-8", "EUC-JP");
		//return (preg_match("/^[ぁ-ん]+$/u", $val))? true: false;
		return preg_match('/^[ぁ-ゞー　 ]+$/u', $val);
		// EUC-JPでの記述
		//return (preg_match("/^(\xa4[\xa1-\xf3]|\xa1[\xb5\xb6\xab])+$/", $val)) ? true : false;
	}
	//----------------------------------------------------------------
	// よみがな
	//----------------------------------------------------------------
	public function checkYomigana($val){
		//mb_regex_encoding("UTF-8");
		//mb_internal_encoding("UTF8");
		//$val = mb_convert_encoding($val, "UTF-8", "EUC-JP");
		//return (preg_match("/^[ぁ-ん]+$/u", $val))? true: false;
		return preg_match('/^[ぁ-ゞー　・（）]+$/u', $val);
		// EUC-JPでの記述
		//return (preg_match("/^(\xa4[\xa1-\xf3]|\xa1[\xb5\xb6\xab])+$/", $val)) ? true : false;
	}
	//----------------------------------------------------------------
	// 漢字(名前などに使用・漢字・ひらがな・カタカナ・英字)
	// 
	// 以下、漢字の内訳
	// 3005 々（漢字の踊り字）
	// 3007 〇（漢数字のゼロ）
	// 303B 〻（漢字の踊り字）
	// 3400..9FFF CJK統合漢字拡張A（＋易経の六十四卦）＋CJK統合漢字
	// F900..FAFF CJK互換漢字
	// 20000..2FFFF CJK統合漢字拡張B〜D＋CJK互換漢字追加（＋念のため）
	//----------------------------------------------------------------
	public function checkKanji($val){
		// 半角=>全角
		$val = mb_convert_kana($val, 'RAKSV', CHARSET);
		$val = str_replace('−', '－', $val);
		return preg_match('/^[　Ａ-Ｚａ-ｚぁ-んァ-ヶー々〇〻\x{3400}-\x{9FFF}\x{F900}-\x{FAFF}\x{20000}-\x{2FFFF}]+$/u', $val);
	}
	//----------------------------------------------------------------
	// 漢字(住所などに使用・漢字・ひらがな・カタカナ・英数字)
	// 
	// 以下、漢字の内訳
	// 3005 々（漢字の踊り字）
	// 3007 〇（漢数字のゼロ）
	// 303B 〻（漢字の踊り字）
	// 3400..9FFF CJK統合漢字拡張A（＋易経の六十四卦）＋CJK統合漢字
	// F900..FAFF CJK互換漢字
	// 20000..2FFFF CJK統合漢字拡張B〜D＋CJK互換漢字追加（＋念のため）
	//----------------------------------------------------------------
	public function checkKanji2($val){
		// 半角=>全角
		$val = mb_convert_kana($val, 'RANKSV', CHARSET);
		$val = str_replace('−', '－', $val);
		logsave("debug", $val);
		return preg_match('/^[　０-９Ａ-Ｚａ-ｚぁ-んァ-ヶー々〇〻－\x{3400}-\x{9FFF}\x{F900}-\x{FAFF}\x{20000}-\x{2FFFF}]+$/u', $val);
	}
	//----------------------------------------------------------------
	// 半角英数字
	//----------------------------------------------------------------
	public function checkEisu($val){
		$val = mb_convert_kana($val, 'n', CHARSET);

		return preg_match("/^[0-9A-Za-z \t\/._-]+$/", $val);
	}
	//----------------------------------------------------------------
	// 率
	//----------------------------------------------------------------
	public function checkRate($val){
		if(!preg_match("/^([1-9]\d*|0)(\.\d+)?$/", $val)){
			return false;
		}
		if($val>100){
			return false;
		}
		return true;
	}
	//----------------------------------------------------------------
	// 値が1~31の数値かどうかをチェックする
	//----------------------------------------------------------------
	function checkMonthDay($val){
		if(!preg_match("/^[0-9]{1,2}$/", $val)){
			return false;
		}

		if($val<1 || $val>31){
			return false;
		}
		return true;
	}
	//----------------------------------------------------------------
	// ファイルサイズチェック
	//----------------------------------------------------------------
	public function checkFileSize($val, $size){

	//////////////////////////////////////	if(empty($this->data[$this->name]['file_'.$field]['name'])) return true;
		if (strcmp($val, '')===0) {
			return true;
		}
	}

	//----------------------------------------------------------------
	// 年号と日付の整合性をチェック
	//----------------------------------------------------------------
	public function checkNengo($data, $nengo, $yy, $mm, $dd){
		$nengo = $this->data[$this->alias][$nengo];
		$yy = $this->data[$this->alias][$yy];
		$mm = $this->data[$this->alias][$mm];
		$dd = $this->data[$this->alias][$dd];

		if ($nengo == '' || $yy == '' || $mm == '' || $dd == '') {
			return true;
		}

		switch ($nengo) {
			case '明治': $yy += 1867; break;
			case '大正': $yy += 1911; break;
			case '昭和': $yy += 1925; break;
			case '平成': $yy += 1988; break;
		}

		// 日付の整合性
		if (!checkdate($mm, $dd, $yy)) {
			return false;
		}

		if ($nengo == '平成' && "{$yy}/{$mm}/{$dd}" >= '1989/1/8' && $yy <= date('Y')) {
			$flg = true;
		} elseif ($nengo == '昭和' && "{$yy}/{$mm}/{$dd}" >= '1925/12/25' && "{$yy}/{$mm}/{$dd}" <= '1989/1/7') {
			$flg = true;
		} elseif ($nengo == '大正' && "{$yy}/{$mm}/{$dd}" >= '1912/7/30' && "{$yy}/{$mm}/{$dd}" <= '1926/12/24') {
			$flg = true;
		} elseif ($nengo == '明治' && "{$yy}/{$mm}/{$dd}" >= '1867/7/29') {
			$flg = true;
		} else {
			$flg = false;
		}

		return $flg;
	}

	//----------------------------------------------------------------
	// 半角カナ→全角カナ
	//----------------------------------------------------------------
	public function han_kaku_to_jen_kaku($val){
		$replace_of = array('ｳﾞ', 'ｶﾞ', 'ｷﾞ', 'ｸﾞ', 'ｹﾞ', 'ｺﾞ', 'ｻﾞ', 'ｼﾞ', 'ｽﾞ', 'ｾﾞ', 'ｿﾞ', 'ﾀﾞ', 'ﾁﾞ', 'ﾂﾞ', 'ﾃﾞ', 'ﾄﾞ', 'ﾊﾞ', 'ﾋﾞ', 'ﾌﾞ', 'ﾍﾞ', 'ﾎﾞ', 'ﾊﾟ', 'ﾋﾟ', 'ﾌﾟ', 'ﾍﾟ', 'ﾎﾟ');
		$replace_by = array('ヴ', 'ガ', 'ギ', 'グ', 'ゲ', 'ゴ', 'ザ', 'ジ', 'ズ', 'ゼ', 'ゾ', 'ダ', 'ヂ', 'ヅ', 'デ', 'ド', 'バ', 'ビ', 'ブ', 'ベ', 'ボ', 'パ', 'ピ', 'プ', 'ペ', 'ポ');
		$_result = str_replace($replace_of, $replace_by, $val);

		$replace_of = array('ｱ', 'ｲ', 'ｳ', 'ｴ', 'ｵ', 'ｶ', 'ｷ', 'ｸ', 'ｹ', 'ｺ', 'ｻ', 'ｼ', 'ｽ', 'ｾ', 'ｿ', 'ﾀ', 'ﾁ', 'ﾂ', 'ﾃ', 'ﾄ', 'ﾅ', 'ﾆ', 'ﾇ', 'ﾈ', 'ﾉ', 'ﾊ', 'ﾋ', 'ﾌ', 'ﾍ', 'ﾎ',
				'ﾏ', 'ﾐ', 'ﾑ', 'ﾒ', 'ﾓ', 'ﾔ', 'ﾕ', 'ﾖ', 'ﾗ', 'ﾘ', 'ﾙ', 'ﾚ', 'ﾛ', 'ﾜ', 'ｦ', 'ﾝ', 'ｧ', 'ｨ', 'ｩ', 'ｪ', 'ｫ', 'ヵ', 'ヶ', 'ｬ', 'ｭ', 'ｮ', 'ｯ', '､', '｡', 'ｰ',
				'｢', '｣', 'ﾞ', 'ﾟ', );
		$replace_by = array('ア', 'イ', 'ウ', 'エ', 'オ', 'カ', 'キ', 'ク', 'ケ', 'コ', 'サ', 'シ', 'ス', 'セ', 'ソ', 'タ', 'チ', 'ツ', 'テ', 'ト', 'ナ', 'ニ', 'ヌ', 'ネ', 'ノ', 'ハ', 'ヒ', 'フ', 'ヘ', 'ホ',
				'マ', 'ミ', 'ム', 'メ', 'モ', 'ヤ', 'ユ', 'ヨ', 'ラ', 'リ', 'ル', 'レ', 'ロ', 'ワ', 'ヲ', 'ン', 'ァ', 'ィ', 'ゥ', 'ェ', 'ォ', 'ヶ', 'ヶ', 'ャ', 'ュ', 'ョ', 'ッ', '、', '。', 'ー',
				'「', '」', '”', '', );
		$_result = str_replace($replace_of, $replace_by, $_result);

		return $_result;
	}
	/**
	 * 日付が３つに分かれた入力フォーム
	 * 年・月・日すべて選択されているかどうかをチェックする.
	 *
	 * @param string $year_name
	 * @param string $month_name
	 * @param string $day_name
	 * @param string $msg
	 *
	 * @return bool
	 */
	public function checkDate3($year_name, $month_name, $day_name, $msg){
		if (empty($this->data[$year_name]) && empty($this->data[$month_name]) && empty($this->data[$day_name])) {
			return true;
		}

		if (((!empty($this->data[$year_name]) && (empty($this->data[$month_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$month_name]) && (empty($this->data[$year_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$day_name]) && (empty($this->data[$year_name]) || empty($this->data[$month_name])))
		) || !checkdate($this->data[$month_name], $this->data[$day_name], $this->data[$year_name])
		) {
			$this->errors[$year_name] = $msg;
			$this->errors[$month_name] = '';
			$this->errors[$day_name] = '';
		}
	}

	/**
	 * TELが３つに分かれた入力フォーム
	 * tel01・tel02・tel03すべて入力されているかどうかをチェックする.
	 *
	 * @param int    $name1
	 * @param int    $name2
	 * @param int    $name3
	 * @param string $msg
	 *
	 * @return bool
	 */
	public function checkTel3($name1, $name2, $name3, $hiss_flg = false, $name = '電話番号'){
		if (!$hiss_flg && empty($this->data[$name1]) && empty($this->data[$name2]) && empty($this->data[$name3])) {
			// 入力必須項目でなく、すべて空だったらスルー
			return true;
		}

		$msg = $name.'を正しく入力してください';

		// 一応　全角→半角の処理
		if (!empty($this->data[$name1])) {
			$this->data[$name1] = mb_convert_kana($this->data[$name1], 'n', CHARSET);
		}
		if (!empty($this->data[$name2])) {
			$this->data[$name2] = mb_convert_kana($this->data[$name2], 'n', CHARSET);
		}
		if (!empty($this->data[$name3])) {
			$this->data[$name3] = mb_convert_kana($this->data[$name3], 'n', CHARSET);
		}

		$flg = true;
		if ($hiss_flg && (empty($this->data[$name1]) || empty($this->data[$name2]) || empty($this->data[$name3]))) {
			$flg = false;
		}

		if (!empty($this->data[$name1]) && (empty($this->data[$name2]) || empty($this->data[$name3]))) {
			$flg = false;
		}
		if (!empty($this->data[$name2]) && (empty($this->data[$name1]) || empty($this->data[$name3]))) {
			$flg = false;
		}
		if (!empty($this->data[$name3]) && (empty($this->data[$name2]) || empty($this->data[$name1]))) {
			$flg = false;
		}
		if (!empty($this->data[$name1]) && !$this->checkNum($this->data[$name1])) {
			$flg = false;
		}
		if (!empty($this->data[$name2]) && !$this->checkNum($this->data[$name2])) {
			$flg = false;
		}
		if (!empty($this->data[$name3]) && !$this->checkNum($this->data[$name3])) {
			$flg = false;
		}

		if (!$flg) {
			$this->errors[$name1] = $msg;
			$this->errors[$name2] = '';
			$this->errors[$name3] = '';
		}
	}

	/**
	 * 全角に変換.
	 */
	public function conv_zen($name){
		$this->data[$name] = mb_convert_kana($this->data[$name], 'AK', CHARSET);
	}
	
	/*
	 * 日付の比較
	 */
	function compareDate($start_Date, $end_Date, $message = ''){
		if(empty($message)) {
			$message = '開始日付は終了日付よりも過去を入力してください';
		}
		$start = strtotime($this->data[$start_Date]);
		$end = strtotime($this->data[$end_Date]);
		
		if(empty($this->errors[$start_Date]) && empty($this->errors[$end_Date]) && $start>$end){
			// 開始日付より終了日付が早ければNG.
			$this->errors[$start_Date] = '';
			$this->errors[$end_Date] = $message;
			return false;
		}
		return true;
	}
	/*
	 * 時間の比較
	 */
	function compareTime($start_time, $end_time, $message = ''){
		if(empty($message)) {
			$message = '開始時刻は終了時刻よりも早い時間を入力してください';
		}
		$start = strtotime($this->data[$start_time].':00');
		$end = strtotime($this->data[$end_time].':00');
		
		if($start>=strtotime('05:00:00') && $end<=strtotime('05:00:00')){
			// ５時以降からの日またぎは許可.
			return true;
		}
		
		if(empty($this->errors[$start_time]) && empty($this->errors[$end_time]) && $start>=$end){
			// 開始時間より終了時間が早ければNG.
			$this->errors[$start_time] = '';
			$this->errors[$end_time] = $message;
			return false;
		}
		return true;
	}
	/*
	 * 時間の比較(５時またぎの確認)
	 */
	function compareTime2($start_time, $end_time, $message = ''){
		if(empty($message)) {
			$message = '午前５時をまたいでの申請はできません';
		}
		$start = strtotime($this->data[$start_time].':00');
		$end = strtotime($this->data[$end_time].':00');
		
		if($start<strtotime('05:00:00') && $end>strtotime('05:00:00')){
			// ５時またぎはだめ.
			return false;
		}
		return true;
	}

}

/*
 * 絵文字を除去する
 */
function delEmojiZZ($str, $encode='UTF-8') {
	$msc = mb_substitute_character();
	mb_substitute_character('none');
	$str = mb_convert_encoding($str, $encode, $encode);
	mb_substitute_character($msc);
	return $str;
}
/*
 * スマホの絵文字を除去
 */
function delEmoji($str){
	return preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '[emoji]', $str);
}