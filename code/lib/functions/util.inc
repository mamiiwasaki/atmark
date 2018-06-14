<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	utilities
 *----------------------------------------------------------------------------*/
/*
 * 日本語用メール送信
 */
function mb_sendmail($from, $to, $title, $body){
	// 文字指定
	mb_language("uni");
	mb_internal_encoding("UTF-8");

	$from = "From: ".$from."\r\n";
	$pfrom   = "-f ".$from;

	// メールの送信
	$send_mail = mb_send_mail($to, $title, $body, $from, $pfrom);

	// メールの送信に問題があったらログ
	if (!$send_mail) {
		logsave("mail", "mail failed");
	}
}
/*
 * 週の配列を返す
 */
function getWeek(){
	return array(0=>"日", 1=>"月", 2=>"火", 3=>"水", 4=>"木", 5=>"金", 6=>"土" );
}
function getWeekStr($date){
	$weeks = get_week();
	if(is_numeric(($date) && strlen($date)==8)){
		$date = sprintf("", substr($date,0,4),substr($date,4,2),substr($date,-2));
	}
	$w = date("w", strtotime($date));
	return $weeks[$w];
}
/*
 * htmlspecialchars
 */
function h($str){
	return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
}
function h_escape($val, $escape_quite=false){
	$val = nl2br(htmlspecialchars($val));
	if($escape_quite){
		return htmlspecialchars(str_replace(array("'", "\""), "", $val));
	}
	return $val;
}
/*
 * パスワードのハッシュ
 */
function getPassHash($password){
	return hash('sha256', $password . PASSWORD_SALT);
}
/*
 * 数字のみのパスワード発行
 */
function getPasswordNum($digits = 4) {
	$pw = '';
	for ($i = 0; $i < $digits; $i++) {
		$pw .= mt_rand(1, 9);
	}
	return $pw;
}

/*
 * 数値の配列を返す
 */
function getNumArr($start, $end, $steps=1){
	for($i=$start; $i<=$end; $i=$i+$steps){
		$arr[$i] = $i;
	}
	return $arr;
}
/*
 * デバイスを返す
 */
function getDevice(){
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
		return "sp";
	} elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
		return "tab";

	} elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
		return "g";
	
	} else {
		// その他
		return "else";
	}
}
/*
 * 税込金額を返す
 * 税計算タイプ(1:四捨五入/2:切り上げ/3:切り捨て)
 */
function getPriceIntax($price, $tax_calc_type=1){
	$price_intax = 0;
	$price_calc = $price * (1+(TAX_RATE/100));
	switch($tax_calc_type){
		case 1; $price_intax = round($price_calc); break;	// 四捨五入
		case 2; $price_intax = ceil($price_calc); break;	// 切り上げ
		case 3; $price_intax = floor($price_calc); break;	// 切り捨て
	}
	return $price_intax;
}
/*
 * 税金額を返す
 * $tax_inc_flg　税別税込フラグ(0:税別 / 1:税込)
 * $tax_calc_type　税計算タイプ(1:四捨五入/2:切り上げ/3:切り捨て)
 */
function getPriceTax($price, $tax_inc_flg=0, $tax_calc_type=1){
	$tax = 0;
	if($tax_inc_flg === '0'){
		// 税別
		$tax_tmp = $price * (TAX_RATE/100);
	} else {
		// 税別
		$tax_tmp = $price - ($price / (1+(TAX_RATE/100)));
	}
	// 端数処理
	switch($tax_calc_type){
		case 1; $tax = round($tax_tmp); break;	// 四捨五入
		case 2; $tax = ceil($tax_tmp); break;	// 切り上げ
		case 3; $tax = floor($tax_tmp); break;	// 切り捨て
	}
	return $tax;
}
function convBNR($str){
	$str = nl2br(str_replace('</BNR>', '<br>', $str));
	return $str;
}
/*
 * 生年月日から年齢を返す
 */
function getAge($birthday) {
	if(empty($birthday)) return "";

	$birthday = str_replace(array("-","/"),"",$birthday);
	$now = date("Ymd");

	// 未来に生まれている
	if ($now < $birthday) return false;

	$age = floor(($now - $birthday) / 10000);

	return $age;
}
/*
 * 経過年数を返す
 */
function getKeikaYear($ymd){
	return (int)( (time() - strtotime($ymd) ) / (60*60*24*365) );
}
/*
 * データ量の単位を付与する
 */
function getUnitSize($data)
{
	if ($data < 1000) {
		$return = $data . "KB";
	} elseif ($data < 1000000) {
		$return = $data/1000 . "MB";
	} else {
		$return = $data/1000000 . "GB";
	}

	return $return;
}
/*
 * 西暦和暦変換

function convYear($year, $month, $day, $era = ''){
	if (empty($era)) {
		// 西暦から和暦
		$seireki = $year;

		if ($seireki > 1989
			|| $seireki == 1989 && $month > 1
			|| $seireki == 1989 && $month == 1 && $day >= 8) {
		//if ($seireki > 1989
		//	|| $seireki == 1989 && $month == 1 && $day >= 8) {
				// 平成 1989/1/8～
				$era = '平成';
				$wareki = $seireki - 1988;
		} elseif ($seireki > 1926
				|| $seireki == 1926 && $month > 12
				|| $seireki == 1926 && $month == 12 && $day >= 25) {
					// 昭和 1926/12/25～
					$era = '昭和';
					$wareki = $seireki - 1925;
		} elseif ($seireki > 1912
				|| $seireki == 1912 && $month > 7
				|| $seireki == 1912 && $month == 7 && $day >= 30) {
					// 大正 1912/7/30～
					$era = '大正';
					$wareki = $seireki - 1911;
		} else {
			// 明治 1868/1/25～
			$era = '明治';
			$wareki = $seireki - 1867;
		}
	} else {
		// 和暦から西暦
		$wareki = $year;

		if ($era == '平成') {
			$seireki = $wareki + 1988;
		} elseif ($era == '昭和') {
			$seireki = $wareki + 1925;
		} elseif ($era == '大正') {
			$seireki = $wareki + 1911;
		} else {
			$seireki = $wareki + 1867;
		}
	}
	return array($seireki, $wareki, $era);
} */
/*
 * ファイルをアップロードする
 */
function uploadFile($name, $width='', $tmp_dir_flg = false){
logsave("test uploadfile", $name);
	if(!isset($_FILES[$name]) || empty($_FILES[$name]["name"])) return "";
	// ファイル名
	$r = mt_rand(0, 99);	// 乱数
	$r2 = sprintf("%02d",$r);

	$file_name = $name."_".date("YmdHi").$r2;
	$file_name .= strrchr($_FILES[$name]["name"], "."); // 拡張子
	$upload_dir = (!$tmp_dir_flg) ? UPLOAD_DIR : UPLOAD_DIR .'tmp/';

	////////////////////////////////////////////////////
	// ファイルの移動　ディレクトリに格納する
	////////////////////////////////////////////////////
	if(file_exists($upload_dir.$file_name)){
		return ;
	}
	if( ! move_uploaded_file($_FILES[$name]['tmp_name'], $upload_dir.$file_name )) {
		$err_msg = 'ファイルのアップロードに失敗しました。';
		if(!empty($_FILES[$name]['error'])){
			switch($_FILES[$name]['error']){
				case '1' : $err_msg .= 'サイズを超えています。<br>　'; break;
				case '2' : $err_msg .= 'サイズを超えています。<br>　'; break;
				case '3' : $err_msg .= '一部のみしかアップロードされませんでした。　<br>'; break;
				case '4' : $err_msg .= 'アップロードできませんでした。<br>　'; break;
				case '6' : $err_msg .= 'サーバー一時保管フォルダがありません。<br>　'; break;
				case '7' : $err_msg .= '書き込みに失敗しました。<br>　'; break;
			}
		}
		logsave("test uploadfile", $_FILES[$name]['error']."-".$err_msg);
		// エラーがあったらエラーメッセージを返して終了
		return ("{$err_msg}");
	}
	// 既存の添付ファイルの上書きだったら、既存ファイルを削除する
	if(!empty($_POST[$name.'_name'])){
		if(file_exists($upload_dir . $_POST[$name.'_name'])) unlink(UPLOAD_DIR . $_POST[$name.'_name']);
	}

	////////////////////////////////////////////////////
	// 幅、高さの指定があったらリサイズ!!
	////////////////////////////////////////////////////
	if(!empty($width)) {
logsave("test uploadfile", $width);
		resizeImage($file_name, $width, $upload_dir);
	} // if convert
	//} // !file_exists

	return $file_name;
}
/*
 * 画像サイズ変換
 */
function resizeImage($file_name, $new_width, $dir){
	list($width,$height,$type) = getimagesize($dir.$file_name);
	$new_height = round($height*$new_width/$width);
	$emp_img = imagecreatetruecolor($new_width,$new_height);
	switch($type){
		case IMAGETYPE_JPEG:
			$new_image = imagecreatefromjpeg($dir.$file_name);
			break;
		case IMAGETYPE_GIF:
			$new_image = imagecreatefromgif($dir.$file_name);
			break;
		case IMAGETYPE_PNG:
			imagealphablending($emp_img, false);
			imagesavealpha($emp_img, true);
			$new_image = imagecreatefrompng($dir.$file_name);
			break;
	}
	imagecopyresampled($emp_img,$new_image,0,0,0,0,$new_width,$new_height,$width,$height);
	//$date = date("YmdHis");
	switch($type){
		case IMAGETYPE_JPEG:
			imagejpeg($emp_img, $dir."/".$file_name);
			break;
		case IMAGETYPE_GIF:
			$bgcolor = imagecolorallocatealpha($new_image,0,0,0,127);
			imagefill($emp_img, 0, 0, $bgcolor);
			imagecolortransparent($emp_img,$bgcolor);
			imagegif($emp_img, $dir."/".$file_name);
			break;
		case IMAGETYPE_PNG:
			imagepng($emp_img, $dir."/".$file_name);
			break;
	}
	imagedestroy($emp_img);
	imagedestroy($new_image);
}

/*
 * チェック画像を返す
 */
function getCheckImg($val="", $checked=1){
	return (!empty($val) && $val==$checked) ? "<img src='/images/check.png'>" : "";
}
/*
 * 郵便番号を変換する(1234567 -> 123-4567)
 */
function getZipstr($val){
	if(empty($val)) return;
	$val = str_replace("-", "", $val);
	return mb_substr($val, 0, 3, "UTF-8")."-".mb_substr($val, 4, 4, "UTF-8");
}
/*
 * ２つの日付から期間を返す（○年○ヶ月）
 */
function getKikan($from_ymd, $to_ymd){
	$date1 = strtotime($to_ymd."01");
	$date2 = strtotime($from_ymd."01");
	$month1 = date("Y", $date1) * 12 + date("m", $date1);
	$month2 = date("Y", $date2) * 12 + date("m", $date2);

	$diff = $month1 - $month2 + 1;

	if($diff==12){
		$diff_str = "1年";
	} else if($diff<12){
		$diff_str = $diff."ヶ月";
	} else {
		$diff_str = floor($diff/12)."年".($diff%12)."ヶ月";
	}
	return $diff_str;
}

/*
 * CSVダウンロード
 */
function doExport($list_dao, $tablename, $sql=null, $header_arr=null, $convert_flg=true){
	$list_dao->limit = "";
	$csv_data = $list_dao->getCsvList($sql);
	makeCSV($tablename, $csv_data, $header_arr, $convert_flg);
}
/*
 * CSVを生成する
 */
function makeCSV($filename, $data, $header_arr=null, $convert_flg=true) {
	/////////////////////////////////////////////
	// header
	/////////////////////////////////////////////
	// ヘッダーの指定がない場合は、データ配列のキーから取得する
	if(empty($header_arr)) {
		foreach ($data as $val) {
			foreach($val as $key2 => $val2) {
				if(is_numeric($key2)) continue;

				$header_arr[] = $key2;
			}
			break;
		}
	}
	$csv[] = implode(",", $header_arr)."\r\n";

	/////////////////////////////////////////////
	// data
	/////////////////////////////////////////////
	foreach ($data as $val) {
		$tmp = array();
		// 内容をフォーマット
		foreach($val as $key2 => $val2) {
			$val2 = preg_replace("/\r\n|\r|\n/", chr(13), $val2);	// 半角のダブルクォートを全角のダブルクォートに置換
			//$val2 = preg_replace("/\"/", "\"\"", $val2);			//半角のダブルクォートを全角のダブルクォートに置換
			$val2 = preg_replace("/\"/", "”", $val2);				//半角のダブルクォートを全角のダブルクォートに置換
			//	$val[$key2] = ereg_replace(",","，", $val2);			//半角カンマを全角カンマに置換
			$val2 = "\"". $val2 . "\"";								//内容を半角ダブルクォートで括る
			$tmp[] = $val2;
		}
		// カンマ区切りの一行へ
		$line = implode(",", $tmp);


		if($convert_flg){
			$line .= "\r\n" ;
		} else {
			$line .= "\n" ;
		}
		$csv[] = $line;
	}

	if(empty($csv)) return false;

	// ログを書く
	require_once DAO_DIR . 'log_dao.php';
	$log_dao = new log_dao();
	$log_dao->export();

	// ダウンロードするファイル名
	$down_file = $filename."_".date('YmdHi') . '.csv';

	////////////////////////////////////////////////////////////////////
	// CSVの出力
	header ("Content-Type: application/force-download");
	header ("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($down_file));
	header ("Content-Description: File Transfer");

	foreach ($csv as $mval) {
		if($convert_flg){
			echo mb_convert_encoding($mval, "SJIS-win", CHARSET);
		} else {
			echo $mval;
		}
		flush();
		ob_flush();
		usleep(10000);
		//exit();
	}
	exit();
}

/*
 * インポートしたCSVファイルのエラーチェック後、ファイルを読み込む
 */
function getImportCsv(){
	// ファイルタイプをチェック
	// $_FILES['in_file']['type'] !== 'text/csv' は、windows(application/vnd.ms-excel)で失敗する！
	$file_type = explode(".",$_FILES['in_file']["name"]);
	if($file_type[1] !== "csv"){
		return "インポートできるファイルはCSVファイルのみです。";
	}

	// ファイルアップロードエラーチェック 
	switch ($_FILES['in_file']['error']) {
		case UPLOAD_ERR_OK:
			// エラー無し
			break;
		case UPLOAD_ERR_NO_FILE:
			// ファイル未選択
			return "ファイルを選択してください";
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			// 許可サイズを超過
			return 'ファイルサイズが大きすぎます';
		default:
			return 'Unknown error';
	}

	//$detect_order = 'ASCII,JIS,UTF-8,CP51932,SJIS-win';
	setlocale(LC_ALL, "ja_JP.UTF-8");

	// アップロードしたファイルを読み込み
	$buffer = file_get_contents($_FILES['in_file']['tmp_name']);

	// UTF-16LE BOM付きじゃなかったらエラー
	if (!preg_match('/^[\x0x\xff][\x0x\xfe]/', $buffer)) {
		return 'ファイルの保存形式が違います。';
	}

	//////////////////////////
	// インポート処理
	//////////////////////////
	// BOMがついてたら削除
	if (ord($buffer{0}) == 0xef && ord($buffer{1}) == 0xbb && ord($buffer{2}) == 0xbf) {
		$buffer = substr($buffer, 3);
	}
	// 文字コード変換
	$buffer = mb_convert_encoding($buffer, "UTF-8", "UTF-16LE");
	$buffer = str_replace("\r","",$buffer);
	// 改行コードで分割
	return explode("\n", $buffer);
}
/*
 * 年月の配列を返す
 * @param int $start	(Ymd形式もしくはYm形式）
 * @param int $end		(Ymd形式もしくはYm形式）
 * @return 年月の配列
 */
function getYYArr($start="", $end="", $sort="desc"){
	if(empty($end)){
		$end = date('Y');
	}
	$arr = array();
	if($sort === 'asc'){
		// 昇順
		$year = $start;
		while($year<=$end){
			$arr[$year] = $year;
			$year++;
		}
	} else {
		// 降順
		$year = $end;
		while($year>=$start){
			$arr[$year] = $year;
			$year--;
		}
	}
	return $arr;
}
/*
 * 年月の配列を返す
 * @param int $start	(Ymd形式もしくはYm形式）
 * @param int $end	(Ymd形式もしくはYm形式）
 * @return 年月の配列
 */
function getYYMMArr($start="", $end="", $sort="desc"){
	if(empty($start)){
		$start = date("Ym01");
	} else if(strlen($start)===6){
		$start .= '01';
	}
	if(empty($end)){
		$end = date("Ym01");
	} else if(strlen($end)===6){
		$end .= '01';
	}

	$arr = array();
	if($sort === 'asc'){
		// 昇順
		$t = strtotime($start);
		for($i=0; $i<100; $i++){
			$t2 = mktime(0,0,0,date("m", $t) + $i, 1, date("Y", $t));
			$d1 = date("Ym", $t2);
			$d2 = date("Y年m月", $t2);
			$arr[$d1] = $d2;

			if(strtotime($end) <= $t2) break;
		}
	} else {
		// 降順
		$t = strtotime($end);
		for($i=0; $i<100; $i++){
			$t2 = mktime(0,0,0,date("m", $t) - $i, 1, date("Y", $t));
			$d1 = date("Ym", $t2);
			$d2 = date("Y年m月", $t2);
			$arr[$d1] = $d2;

			if(strtotime($start) >= $t2) break;
		}
	}
	return $arr;
}
/* 昨年度からからの年月の配列を返す
 * $future_flg=true 今年度末まで
 */
function getYYMMArr2Years($future_flg=false){
	$oldest_year = (date('m') < 4) ? date('Y')-2 : date('Y')-1;
	$oldest_yymm = sprintf("%04d04", $oldest_year);
	if(START_YYMM>$oldest_yymm){
		// システム利用開始以前だったら、開始年月に合わせる
		$oldest_yymm = START_YYMM;
	}
	if($future_flg){
		$new_year = (date('m') < 3) ? date('Y') : date('Y')+1;
		$new_yymm = sprintf("%04d03", $new_year);
	} else {
		$new_yymm = date('Ym');
	}
	return getYYMMArr($oldest_yymm, $new_yymm);
}
/*
 * 時の配列を返す
 */
function getHourArr(){
	$hours = array();
	for($i=0; $i<=24; $i++){
		$hours[$i] = sprintf("%02s", $i);
	}
	return $hours;
}
/*
 * 分の配列を返す
 */
function getTimeArr(){

	$times = array();
	for($i=0; $i<=60; $i=$i+10){
		$times[$i] = sprintf("%02s", $i);
	}
	return $times;
}
/*
 * 20170920 -----> 2017/09/20
 * 201709 -----> 2017/09
 * 0901 -----> 09/01
 */
function getYMD($str, $ja=false){
	if(empty($str)) return '';

	if(is_numeric($str) && strlen($str)===6){
		if($ja){
			// yyyy年mm月
			return substr($str, 0, 4).'年'.substr($str, -2).'月';
		} else {
			// yyyy/mm
			return substr($str, 0, 4).'/'.substr($str, -2);
		}
	}elseif(is_numeric($str) && strlen($str)===8){
		if($ja){
			// yyyy年mm月
			return substr($str, 0, 4).'年'.substr($str, 4, 2).'月'.substr($str, 6, 2).'日';
		} else {
			// yyyy/mm/dd
			return substr($str, 0, 4).'/'.substr($str, 4, 2).'/'.substr($str, 6, 2);
		}
	}else if(is_numeric($str) && strlen($str)===4){
		if($ja){
			// mm月dd日
			return substr($str, 0, 2).'月'.substr($str, -2).'日';
		} else {
			// mm/dd
			return substr($str, 0, 2).'/'.substr($str, -2);
		}
	}else{
		if($ja){
			return date('Y年m月d日', strtotime($str));
		} else {
			return str_replace('-', '/', $str);
		}
	}
}
/*
 * 先月のYm(201709)
 */
function getLastYm(){
	return date('Ym', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));
}
/*
 * 0900 ---->09:00
 */
function getHMS($str){			// 正しくは、getHM!!! あとで消す。
	if(empty($str)) return '';
	return substr($str, 0, 2).':'.substr($str, -2);
}
function getHM($str){
	if(empty($str)) return '';
	return substr($str, 0, 2).':'.substr($str, -2);
}
// 時刻（４桁表記）の差分を時間単位（h）で返す
function timeDiff($from, $to){
	$diff = (floatval(substr($to,0,2))*60+floatval(substr($to,2,4)))
		   -(floatval(substr($from,0,2))*60+floatval(substr($from,2,4)));
	if($diff < 0){
		$diff += 1440;
	}

	//return sprintf("%.2f",$diff/60);
	return $diff/60;
}
/*
 * nヶ月後を返す
 * $add_monthがマイナスなら nヶ月前
 */
function getAddMonth($date, $add_month){
	// 年月日に分ける
	$mon = date('n',strtotime($date));
	$day = date('j',strtotime($date));
	$year = date('Y',strtotime($date));
	// add_monthヶ月後の月の1日
	$ym = mktime(0, 0, 0, ($mon + $add_month), 1, $year);
	$arrayYm = getdate($ym);
	// 日を置き換えて日時の正当性チェック
	if(checkdate($arrayYm['mon'], (int)$day, $arrayYm['year'])){
		// そのままの日付で日時取得
		$res = mktime(0, 0, 0, $arrayYm['mon'], $day, $arrayYm['year']);
	}else{
		// 6ヶ月後の末日にして日時取得
		$res = mktime(0, 0, 0, $arrayYm['mon'], date('t', $ym), $arrayYm['year']);
	}
	return (date('Y-m-d',$res));
}

// 時間を丸める（$rounding_class 1->15分丸め 2->30分丸め）
function roundDown($val, $rounding_class){
	$ret = $val;
	switch($rounding_class){
		case 1:	//15分丸め
			$ret = floor($val*4) / 4;
			break;
		case 2:	//30分丸め
			$ret = floor($val*2) / 2;
			break;
	}
	return $ret;
}

/*
 * タグをエスケープする
 */
function escape_tag($val){
	$pattern_arr = array('<', '>', '¥', '〜', '~', "'", '"', '\\');
	$convert_arr = array('＜', '＞', '￥', 'ー', 'ー', '’', '”', '￥');
	return str_replace($pattern_arr, $convert_arr, $val);
}

/*
 * 日付ループ用の配列を返す
 */
function getPeriodUtil($sday,$eday){
	$start = new DateTime($sday);
	$end = new DateTime($eday);
	$interval = new DateInterval('P1D');		
	$end->add($interval);
	$period = new DatePeriod($start, $interval, $end);
	return $period;
}

/*
 * 指定期間の日数を返す
 */
function getDayCount($sday,$eday){
	$period = getPeriodUtil($sday, $eday);
	$count = 0;
	foreach($period as $d){
		$count++;
	}
	return $count;
}
/*
 * 年度基準の年を返す
 * 1月〜3月はyear-1
 */
function getNendoYear($year=null, $month=null){
	if(empty($year)) {
		$year = date('Y');
	}
	if(empty($month)) {
		$month = date('n');
	}
	if((int)$month>=1 && (int)$month<=3) {
		return $year-1;
	}
	return $year;
}
/*
 * リアルの年を返す
 * 1月〜3月はyear+1
 */
function getRealYear($year=null, $month=null){
	if(empty($year)) {
		$year = date('Y');
	}
	if(empty($month)) {
		$month = date('n');
	}
	if((int)$month>=1 && (int)$month<=3) {
		return $year+1;
	}
	return $year;
}
/*
// 年度の開始から始まる月の配列を返す
function getMonthOrderZZ($nendo_start_month){
	$month = $nendo_start_month;
	for($i=1; $i<=12; $i++){
		$arr[] = sprintf('%02d', $month);
		$month++;
		if($month==13) $month = 1;
	}
	return $arr;
}*/
function getMonthOrder($year, $nendo_year_type, $nendo_start_month){
	$month = $nendo_start_month;
	$real_year = $year;
	if($nendo_year_type==2){
		$real_year = $year-1;
	}
	$arr = array();
	for($m=1; $m<=12; $m++){
		$arr[$month] = $real_year;
		$month++;
		if($month==13) {
			$month = 1;
			$real_year++;
		}
	}
	return $arr;
}
/*
 * 直近〜n年分の年月を配列で返す
 */
function getLatestYM($n=2){
	// 前年度の4月までを配列に追加
	$arr = array('999999'=>'直近');

	for( $i = 0; $i < ($n*12) ; $i++ ){
		$arr[date("Ym" , strtotime("-$i month"))] = date("Y/m" , strtotime("-$i month"));
	}
	return $arr;
}