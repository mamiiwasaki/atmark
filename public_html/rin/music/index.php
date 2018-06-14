<?php
	require_once("../inc/util.inc");
	
	$width = getTableWidth();

$file = "./list.txt";
$arr = file($file);

/*


むすんでひらいて
まるまるもりもり
いっぽんでもにんじん
シャボン玉
きらきらぼし
アイアイ
いとまきまき
はらぺこあおむし

*/


?>



<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="EUC-JP">
	<link rel="shortcut icon" href="images/favicon.ico" >
	<title>Rintaro</title>
	<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Muli'>
	<link rel="stylesheet" href="../css/common.css">
	<script type="text/javascript" src="../js/jquery.js"></script>		
	<script type="text/javascript">

	</script>
</head>
<body>


<div id="container">
	<header>
		<div>
			<h1>凜のサイト</h1>
		</div>
	</header>
	<div id="wrapper">
		<div id="login">			
			<section>					
				<table style="width:<?=$table_width?>px;"><tr align="center" style="height:30px;">
					<td onClick="location.href='../sound/index.php';" style="cursor:pointer;">sound</td>
					<td onClick="location.href='../music/index.php';" style="cursor:pointer;">music</td>
					<td onClick="location.href='../picture/index.php';" style="cursor:pointer;">picture</td>
				</tr></table>
				
				
				
				
				<form method="post" id="frm" name="frm">

				
				
				

<table width="100%" cellpadding="0" cellspacing="0">

<?php 


foreach($arr as $key => $val){
	//if(empty(trim($val))) continue;
	//if(trim($val)=="") continue;
	$tmp = explode("\t", $val);
	if(empty($tmp[1])) continue;
	//pr($tmp);
	if($key==0 || ($key%3)==0) echo "<tr>";
	
	echo "<td>".$val["title"]."<br>";
	echo "<iframe width=\"300\" height=\"250\" src=\"./src/{$tmp[0]}_{$tmp[2]}\" frameborder=\"0\" allowfullscreen></iframe>\n";
	echo "<br>".$tmp[1];
	echo "</td>\n";
	
	if(($key%3)==2) echo "</tr>";
}
?>	
</table>

					
					
				</form>
			</section>
		</div>
    </div>
	<div id="footer-space">space</div>
</div>
<footer>
	<small>&copy; 2014 Mami Iwasaki</small>
</footer>

</body>
</html>
