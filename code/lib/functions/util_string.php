<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	使用頻度の少ない文字列計の関数
 *----------------------------------------------------------------------------*/
/*
 * カタカナをひらがなに変換して返す
 */
function katakana2hiragana($str){
	$arr["ア"] = "あ";	$arr["イ"] = "い";	$arr["ウ"] = "う";	$arr["エ"] = "え";	$arr["オ"] = "お";
	$arr["カ"] = "か";	$arr["キ"] = "き";	$arr["ク"] = "く";	$arr["ケ"] = "け";	$arr["コ"] = "こ";
	$arr["サ"] = "さ";	$arr["シ"] = "し";	$arr["ス"] = "す";	$arr["セ"] = "せ";	$arr["ソ"] = "そ";
	$arr["タ"] = "た";	$arr["チ"] = "ち";	$arr["ツ"] = "つ";	$arr["テ"] = "て";	$arr["ト"] = "と";
	$arr["ナ"] = "な";	$arr["ニ"] = "に";	$arr["ヌ"] = "ぬ";	$arr["ネ"] = "ね";	$arr["ノ"] = "の";
	$arr["ハ"] = "は";	$arr["ヒ"] = "ひ";	$arr["フ"] = "ふ";	$arr["ヘ"] = "へ";	$arr["ホ"] = "ほ";
	$arr["マ"] = "ま";	$arr["ミ"] = "み";	$arr["ム"] = "む";	$arr["メ"] = "め";	$arr["モ"] = "も";
	$arr["ラ"] = "ら";	$arr["リ"] = "り";	$arr["ル"] = "る";	$arr["レ"] = "れ";	$arr["ロ"] = "ろ";
	$arr["ヤ"] = "や";	$arr["ユ"] = "ゆ";	$arr["ヨ"] = "よ";
	$arr["ワ"] = "わ";	$arr["ヲ"] = "を";	$arr["ン"] = "ん";
	$arr["ガ"] = "が";	$arr["ギ"] = "ぎ";	$arr["グ"] = "ぐ";	$arr["ゲ"] = "げ";	$arr["ゴ"] = "ご";
	$arr["ザ"] = "ざ";	$arr["ジ"] = "じ";	$arr["ズ"] = "ず";	$arr["ゼ"] = "ぜ";	$arr["ゾ"] = "ぞ";
	$arr["ダ"] = "だ";	$arr["ヂ"] = "ぢ";	$arr["ヅ"] = "づ";	$arr["デ"] = "で";	$arr["ド"] = "ど";
	$arr["バ"] = "ば";	$arr["ビ"] = "び";	$arr["ブ"] = "ぶ";	$arr["ベ"] = "べ";	$arr["ボ"] = "ぼ";
	$arr["ャ"] = "ゃ";	$arr["ュ"] = "ゅ";	$arr["ョ"] = "ょ";	$arr["ッ"] = "っ";
	$res = "";
	for($i=0; $i<mb_strlen($str, CHARSET); $i++){
		$s = mb_substr($str, $i, 1, CHARSET);
		$res .= (isset($arr[$s])) ? $arr[$s] : $s;
	}
	return $res;
}
/*
 * 氏名からイニシャルを返す
 */
function getInitial($str, $delimiter=" "){
	if($str=="") return "";

	$ini_arr = array("あ"=>"A","い"=>"I","う"=>"U","え"=>"E","お"=>"O",
			"か"=>"K","き"=>"K","く"=>"K","け"=>"K","こ"=>"K",
			"さ"=>"S","し"=>"S","す"=>"S","せ"=>"S","そ"=>"S",
			"た"=>"T","ち"=>"T","つ"=>"T","て"=>"T","と"=>"T",
			"な"=>"N","に"=>"N","ぬ"=>"N","ね"=>"N","の"=>"N",
			"は"=>"H","ひ"=>"H","ふ"=>"H","へ"=>"H","ほ"=>"H",
			"ま"=>"M","み"=>"M","む"=>"M","め"=>"M","も"=>"M",
			"や"=>"Y","ゆ"=>"Y","よ"=>"Y",
			"ら"=>"R","り"=>"R","る"=>"R","れ"=>"R","ろ"=>"R",
			"わ"=>"W",
			"が"=>"G","ぎ"=>"G","ぐ"=>"G","げ"=>"G","ご"=>"G",
			"ざ"=>"Z","じ"=>"Z","ず"=>"Z","ぜ"=>"Z","ぞ"=>"Z",
			"だ"=>"D","ぢ"=>"D","づ"=>"D","で"=>"D","ど"=>"D",
			"ば"=>"B","び"=>"B","ぶ"=>"B","べ"=>"B","ぼ"=>"B",
		);

	$str = mb_convert_kana($str, "s", "UTF-8");

	if(strpos($str, " ") !=false){
		list($sei, $mei) = explode($delimiter, $str);
		$sei = mb_substr($sei, 0, 1, "UTF-8");
		$mei = mb_substr($mei, 0, 1, "UTF-8");
		
		return $ini_arr[$sei].$ini_arr[$mei];
		
	} else{
		$seimei = mb_substr($str, 0, 1, "UTF-8");
		return $ini_arr[$seimei];
	}
}
/*
 * 住所から、番地以下を除去して返す
 */
function getAddress_s($address){
	if($address=="") return "";

	$address_short = "";
	$address = mb_convert_kana($address, "n", "UTF-8");
	if(!preg_match("/[0-9]/", $address))return $address;

	for($i=3; $i<mb_strlen($address, "UTF-8"); $i++){
		if(preg_match("/[0-9]/", mb_substr($address, $i, 1))){
			$address_short = mb_substr($address, 0, $i, "UTF-8");
			break;
		}
	}
	return $address_short;
}
/**
 * Emailリンク付きに変換
 */
function convEmail($str){
	$pattern = "/^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i";

	$str = str_replace("\n", "<br>", htmlspecialchars($str, ENT_QUOTES, CHARSET));

	if(preg_match($pattern, $str)){
		preg_match_all($pattern, $str, $results);
		foreach ($results[0] as $key=>$val){
			$replace[] = '<a href="mailto:'.$val.'">'.$val.'</a>';
		}
		$str = str_replace($results[0], $replace, $str);
	}
	return $str;
}
/**
 * URLリンク付きに変換
 */
function convUrl($str){
	$pattern = "/(http|https):\/\/[-\w\.]+(:\d+)?(\/[^\s]*)?/";

	$str = str_replace("\n", "<br>", htmlspecialchars($str, ENT_QUOTES, CHARSET));

	if(preg_match($pattern, $str)){
		preg_match_all($pattern, $str, $results);
		foreach ($results[0] as $key=>$val){
			$replace[] = '<a href="'.$val.'" target="_blank">'.$val.'</a>';
		}
		$str = str_replace($results[0], $replace, $str);
	}
	return $str;
}
