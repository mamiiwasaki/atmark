<?php
/*
右側、左側、それぞれスクロール
表示テーブルのtd、wとかどうする。
getのエンコード
書き込みの権限
htaccess
1回も呼ばれてない関数
セッション保持
詳細の、表示/非表示
grep０のものだけ表示
待ち時間に対する表示
スペース区切りで複合検索
「〜以外」の検索
縮小版
大文字小文字関係なく。
edit.phpの許可問題
リフレッシュボタン
scp
*/

echo "#aaaaaaaaaa";exit;
session_start();
error_reporting(E_ALL);
ini_set( 'display_errors', 1 );

//////////////////////////////////
// 設定
//////////////////////////////////
$_SESSION['root_url'] = "http://mami.tools";		// ルート
$_SESSION['root_dir'] = "/home/mami/public_html/";	// ルート
$_SESSION['base_dir'] = "/home/mami/public_html/";	// ベース

// イメージ格納場所
define("IMG_DIR", "./img/");
// ファイルタイプイメージ格納場所
define("FILE_TYPE_IMG_DIR", IMG_DIR."types/");


//////////////////////////////////

$file_type_arr = array(
		"pdf"=>"",
		"xls"=>"xls",
		"doc"=>"doc",
		"txt"=>"txt",
		"gif"=>"gif",
		"jpg"=>"jpeg",
		"jpeg"=>"jpeg",
		"ppt"=>"ppt",
		"mdb"=>"clip",
		"css"=>"css",
		"js"=>"javaScript",
		"php"=>"php",
		"html"=>"html",
		"htm"=>"html",
		"sh"=>"シェルスクリプト",
		"csv"=>"txt");


  $search_type_arr = array("1"=>"完全一致", 
             "2"=>"前方一致", 
             "3"=>"後方一致",
             "4"=>"あいまい");


  $file_type_img_arr = array(
            "pdf"=>"acrobat.png",
            "xls"=>"xls.gif",
            "doc"=>"doc.gif",
            "txt"=>"txt.png",
            "gif"=>"gif.png",
            "jpg"=>"jpeg.png",
            "jpeg"=>"jpeg.png",
            "ppt"=>"ppt.gif",
            "mdb"=>"clip.gif",
            "css"=>"css.png",
            "js"=>"js.png",
            "php"=>"php.png",
            "html"=>"html.png",
            "htm"=>"html.png",
            "csv"=>"txt.png",
            ""=>"file.png"
            );




  $file_type_search_arr = array(
          "1"=>"gif, jpeg, png", 
          "2"=>"php, txt, html", 
          "3"=>"javaScript, css",
          "9"=>"その他",
          "8"=>"すべて");


  $file_type_search_arr2 = array(
          "1"=>"すべて",
          "2"=>"php, txt, html", 
          "3"=>"javaScript, css");

  

////////////////////////////////////////////////////////

// ディレクトリを配列に格納

////////////////////////////////////////////////////////
function getDirs( $parentdir ){

    if(!is_dir($parentdir)){
      die($parentdir."はディレクトリではありません。");
      
    }

    $dir = opendir($parentdir);

    while($file = readdir($dir)){

      // ディレクトリだったら実行。
      if(!is_dir($parentdir."/".$file)) continue;
      if($file=="." || $file=="..") continue;


      // 当ディレクトリのパス
      if(substr($parentdir, strlen($parentdir)-1,1)!="/") $parentdir.="/";
      $path = $parentdir.$file;


      // open/close スイッチ!!
	  $str = '';
      if(!empty($_GET['path']) && ($_GET['path'] == $path) && ($_GET['sts']=="close" || isset($_GET['show']))){
        $sts="open";
      } else if(!empty($_GET['path']) && ($_GET['path'] == $path) && $_GET['sts']=="open"){
        $sts="close";
      } else if($_SESSION['sts'][$path]=="") {
        $sts = "close";
      } else {
        $sts = $_SESSION['sts'][$path];
      }


      // sts(open/close)のセッション
      $_SESSION['sts'][$path] = $sts;

      // ■再帰実行■
      // sts==openだったらその下のディレクトリも配列に入れる！！
	  $child_arr = array();
      if($_SESSION['sts'][$path]=="open"){
          $child_arr = getDirs( $path );

      }


      // 含まれるディレクトリ数
      $dir_cnt = exec("ls -l {$path} | grep ^d | wc -l");

      // 配列に格納！
      $arr[$path]=  array( "file"=>$file, "sts"=>$sts, "child_arr"=>$child_arr, "dir_cnt"=>$dir_cnt);

    }
    return $arr;
}





////////////////////////////////////////////////////////

// ディレクトリをプリント

////////////////////////////////////////////////////////
function printTree($dir_arr){
	if(is_array($dir_arr)){
		// ソート
		ksort($dir_arr);
		// ツリー作成
		foreach($dir_arr As $key=>$val){
			// close/openステータス
			$sts = $val['sts'];
			// tree
			if($val['dir_cnt']>0){
				if($sts=="open") {
					$js_tree = "<img src=\"".IMG_DIR."jsTree/expanded1.png\" border='0'></a>";
				} else {
					$js_tree = "<img src=\"".IMG_DIR."jsTree/closed1.png\" border='0'></a>";
				}
			} else {
				$js_tree = "<img src=\"".IMG_DIR."types/spacer.gif\" border='0' width='15'></a>";
			}

			// ディレクトリアイコン
			$dir_ico = ($sts=="open") ? "directory_opened.png" : "directory.png";

			// インデント
			$indent_base = substr_count($_SESSION['base_dir'], "/");
			$indent = substr_count($key, "/") - substr_count($_SESSION['base_dir'], "/");
			if($indent>0) {
				$space = str_repeat(" 　 ", ($indent));
			} else {
				$space = "";
			}
			// ディレクトリ名
			$str = $space;
			$str.= "<a href='{$self}?path={$key}&sts={$sts}'>\n";
			$str.= $js_tree;
			$str.= "<img src=".IMG_DIR."types/{$dir_ico}><a href='{$self}?path={$key}&show'>{$val['file']}</a>";
			$str.= "<br>\n";
			// ディレクトリ名印字！！
			echo $str;
			 // ■再帰実行■
			// 子供のディレクトリ
			if($_SESSION['sts'][$key]=="open"){
				printTree($val['child_arr']);
			}
		}
	}
}



////////////////////////////////////////////////////////
// ファイルのアイコンイメージ
////////////////////////////////////////////////////////
function getFileTypeImg($file, $img_path) {
	global $file_type_img_arr;
	// ディレクトリ
	if(is_dir($file)) {
		$img="<img src=\"{$img_path}directory.png\"  border=\"0\">";

	} else {
		if(substr($img_path, strlen($img_path)-1, 1)!="/") $img_path.="/";
		$pos = strrpos($file, ".");
		$ext = substr($file, $pos+1, strlen($file)-$pos);
		$img = ($file_type_img_arr[$ext]=="") ? "file.png": $file_type_img_arr[$ext];
		$imgpath = "<img src=\"{$img_path}{$img}\" border=\"0\">";
	}
	return  $imgpath;
}

////////////////////////////////////////////////////////
// ファイル種別
////////////////////////////////////////////////////////
function getFileType($file) {
	global $file_type_arr;

	  // ディレクトリ
	  if(is_dir($file)) {
		 $file_type= "ファイルフォルダ";
	  } else {
		if(substr($img_path, strlen($img_path)-1, 1)!="/") $img_path.="/";
			$pos = strrpos($file, ".");
		$ext = substr($file, $pos+1, strlen($file)-$pos);
		$file_type = ($file_type_arr[$ext]=="") ? "file": $file_type_arr[$ext];
	  }
	  return  $file_type;
}




///////////////////////////////////////////////
// パラメータ
///////////////////////////////////////////////
$self = $_SERVER['PHP_SELF'];

// 現在開いているディレクトリ
$path = filter_input(1, 'path');
if(!empty($path) && substr($path, strlen($path)-1, 1)!="/") {
	$path.="/";
}
if(isset($_GET['bottom_cd'])) {
	$_SESSION['bottom_cd'] = $_GET['bottom_cd'];
}

// 現在のディレクトリ
$this_dir = ($path=="") ? $_SESSION['base_dir'] : $path;

// URL
$url = "{$self}?path={$path}";


$w = filter_input(1, 'w');






///////////////////////////////////////////////
//
// 表示タイプ
//    1 縮小版
//    2 アイコン
//    3 一覧
//    4 詳細
//
///////////////////////////////////////////////
if(isset($_GET['show_type']))  $_SESSION['show_type'] = $_GET['show_type'];
if($_SESSION['show_type']=="") $_SESSION['show_type']=4;


///////////////////////////////////////////////
//
// ディレクトリ情報を配列に格納!!
//
///////////////////////////////////////////////
$_SESSION['dir'] = array();
$_SESSION['dir'] = getDirs($_SESSION['base_dir']); 



// 上へ, grep_cnt
// ベースパス以上にはリンクしない。
$tmp_path = substr($path, 0, strlen($path)-1);
$upper_dir = substr($path, 0, strrpos($tmp_path, "/"));

if($path!="" && $path!=$_SESSION['base_dir']) {
	$upper_url   = "<a href=\"".$self."?path=".$upper_dir."\">上へ</a>";
	$grep_cnt_url = "<a href=\"{$url}&bottom_cd=3\">GREP回数</a>";
} else {
	$upper_url   = "<font color='gray'>上へ</font>";
	$grep_cnt_url = "<font color='gray'>GREP回数</font>";
}


$bottom_src = '';
if(!empty(bottom_cd)){
	switch($_SESSION['bottom_cd']) {
		case 1:
			$bottom_src = "find.php";
			break;
		case 2:
			$bottom_src = "grep.php";
			break;
		case 3:
			$bottom_src = "grep_cnt.php";
			break;
		default:
			$bottom_src = "";
			break;
	}
}
?>






<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
  <link href="./main.css" rel=stylesheet type="text/css">
  <script language="JavaScript">
    function getWidth(){
      w = (event.x*0.8);
      location.href="<?=$url?>&show_type=2&w="+w;
   
    }
    
    function openWindow(url, path){
      window.open(url + '?path=' + path,'','width=800 height=600 scrollbars=yes resizable=yes');
    }

  </script>
  <title>explorer</title>

</head>
<body>
<div class="top_block"><?=$this_dir?></div>
<div class="menu_block">
	<table width="700">
	  <tr align="center">
		<td><a href="#" onClick="history.back();">戻る</a></td>
		<td><?=$upper_url?></td>
		<td width="80" bgcolor="#89A0BA"><a href="<?=$url?>&bottom_cd=1">ファイル検索</a></td>
		<td width="80" bgcolor="#C0C0C0"><a href="<?=$url?>&bottom_cd=2">文字検索</a></td>
		<td width="100" bgcolor="#B1B49C"><?=$grep_cnt_url?></td>
		<td width="80">　</td>
		<td width="50"><a href="<?=$url?>&show_type=4">詳細</a></td>
		<td width="80"><a href="<?=$url?>&show_type=1">縮小版</a></td>
		<td width="50"><a href="#" onClick="getWidth();">アイコン</a></td>
		<td width="50"><a href="<?=$url?>&show_type=3">一覧</a></td>
	  </tr>
	</table>
</div>
<div class="tree_block"><?=printTree($_SESSION['dir']); ?></div>
<div class="contents_block"><?php include("showFiles.php"); ?></div>
<div class="search_block"><?php if($bottom_src!="") include($bottom_src); ?></div>
</body>
</html>
