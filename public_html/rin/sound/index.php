<?php
	require_once("../inc/util.inc");
	
	$width = getTableWidth();

	
	function getSoundIcon($src, $img){
		global $width;
		return "<td onClick=\"playAudio('./src/".$src."');return false;\" style=\"cursor:pointer;padding-top:30px;\">".
			 "<img src=\"./images/".$img."\" width=".$width."></td>\n";
	}
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

	var audio =  new Audio ; // Audio オブジェクト
	var flg = false;
	var	current_audio = null ;	

	// 音を鳴らす
	function playAudio(src){

		if(src != current_audio)
			flg=false;
	
		current_audio = src ;

		try {			
			if(flg){	
				audio.pause();
				flg=false;
				
			} else {
				audio.pause();/////
				audio.src = src;       // FireFoxはmp3は再生できないのでwavファイル等を使用
				
				audio.load();
			//	audio.currentTime = 3; // seeking and seeked will be fired
				audio.play();
				flg=true;////
			}	
		} catch(e) {
		  alert(e);
		}
	}
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
				<table style="width:<?=$table_width?>px;" class="navi"><tr>
					<td onClick="location.href='../sound/index.php';" style="cursor:pointer;">sound</td>
					<td onClick="location.href='../music/index.php';" style="cursor:pointer;">music</td>
					<td onClick="location.href='../picture/index.php';" style="cursor:pointer;">picture</td>
				</tr></table>
				
				
				<form method="post" id="frm" name="frm">

				<table style="width:<?=$table_width?>px;">
				<?php if($ua_type!="sp"){?>
				
					<tr align="center">						
						<?php echo getSoundIcon("train/crossing1.mp3", "train/crossing01.jpg");?>
						<?php echo getSoundIcon("train/sl.mp3", "train/sl01.gif");?>
						<?php echo getSoundIcon("train/train-pass1.mp3", "train/shinkansen.gif");?>
						<?php echo getSoundIcon("train/train-pass2.mp3", "train/ch1E.gif");?>				
					</tr>					
					<tr align="center">	
						<?php echo getSoundIcon("train/bullet-train-door1.mp3", "train/ch1.gif");?>
						<?php echo getSoundIcon("train/train-door1.mp3", "train/hk2.gif");?>
						<?php echo getSoundIcon("train/train-bell1.mp3", "train/kobeE.gif");?>
						<?php echo getSoundIcon("train/monorail-bell.mp3", "train/ism1.gif");?>
					</tr>					
					<tr align="center">
						<?php echo getSoundIcon("norimono/patrol-car1.mp3", "norimono/patrolcar.gif");?>
						<?php echo getSoundIcon("norimono/ambulance-siren1.mp3", "norimono/ambulance.gif");?>
						<?php echo getSoundIcon("norimono/ambulance-siren2.mp3", "norimono/ambulance.gif");?>
						<?php echo getSoundIcon("norimono/firetruck.mp3", "norimono/firetruck.gif");?>
					</tr>
					<tr align="center">
						<?php echo getSoundIcon("animal/lion2.mp3", "animal/lion01.gif");?>
						<?php echo getSoundIcon("animal/elephant1.mp3", "animal/elephant01.gif");?>
						<?php echo getSoundIcon("animal/dog2.mp3", "animal/dog01.gif");?>
						<?php echo getSoundIcon("animal/cat-cry1.mp3", "animal/cat01.gif");?>

					</tr>
					<tr align="center">
						<?php echo getSoundIcon("animal/sheep-cry1.mp3", "animal/sheep01.gif");?>
						<?php echo getSoundIcon("animal/goat-cry1.mp3", "animal/goat01.gif");?>
						<?php echo getSoundIcon("animal/cow.mp3", "animal/cow01.gif");?>
						<?php echo getSoundIcon("animal/horse.mp3", "animal/horse01.gif");?>
						
						</tr>					
					<tr align="center">
						<?php echo getSoundIcon("animal/crows.mp3", "animal/crow01.gif");?>
						<?php echo getSoundIcon("animal/horornis-diphone-twitter1.mp3", "animal/horornis-diphone-twitter01.gif");?>
						<?php echo getSoundIcon("animal/chicken-cry1.mp3", "animal/chicken01.gif");?>
					
					</tr>	
				
				
				
					
									
					<?php } else {?>
					
					<tr align="center">						
						<?php echo getSoundIcon("train/crossing1.mp3", "train/crossing01.jpg");?>
						<?php echo getSoundIcon("train/sl.mp3", "train/sl01.gif");?>
						<?php echo getSoundIcon("train/train-pass1.mp3", "train/shinkansen.gif");?>
					</tr>					
					<tr align="center">
						<?php echo getSoundIcon("train/train-pass2.mp3", "train/ch1E.gif");?>						
						<?php echo getSoundIcon("train/bullet-train-door1.mp3", "train/ch1.gif");?>
						<?php echo getSoundIcon("train/train-door1.mp3", "train/hk2.gif");?>
					</tr>					
					<tr align="center">
						<?php echo getSoundIcon("train/train-bell1.mp3", "train/kobeE.gif");?>						
						<?php echo getSoundIcon("train/monorail-bell.mp3", "train/ism1.gif");?>
						<?php echo getSoundIcon("norimono/patrol-car1.mp3", "norimono/patrolcar.gif");?>
					</tr>					
					<tr align="center">
						<?php echo getSoundIcon("norimono/ambulance-siren1.mp3", "norimono/ambulance.gif");?>
						<?php echo getSoundIcon("norimono/ambulance-siren2.mp3", "norimono/ambulance.gif");?>
						<?php echo getSoundIcon("norimono/firetruck.mp3", "norimono/firetruck.gif");?>
					</tr>
					<tr align="center">
						<?php echo getSoundIcon("animal/lion2.mp3", "animal/lion01.gif");?>
						<?php echo getSoundIcon("animal/elephant1.mp3", "animal/elephant01.gif");?>
						<?php echo getSoundIcon("animal/dog2.mp3", "animal/dog01.gif");?>
					</tr>
					<tr align="center">
						<?php echo getSoundIcon("animal/cat-cry1.mp3", "animal/cat01.gif");?>					
						<?php echo getSoundIcon("animal/sheep-cry1.mp3", "animal/sheep01.gif");?>
						<?php echo getSoundIcon("animal/goat-cry1.mp3", "animal/goat01.gif");?>
							</tr>					
					<tr align="center">
						<?php echo getSoundIcon("animal/cow.mp3", "animal/cow01.gif");?>
						<?php echo getSoundIcon("animal/horse.mp3", "animal/horse01.gif");?>					
						<?php echo getSoundIcon("animal/crows.mp3", "animal/crow01.gif");?>
					</tr>					
					<tr align="center">
						<?php echo getSoundIcon("animal/horornis-diphone-twitter1.mp3", "animal/horornis-diphone-twitter01.gif");?>
						<?php echo getSoundIcon("animal/chicken-cry1.mp3", "animal/chicken01.gif");?>
					
					</tr>	
				
					
					<?php }?>
				</table>
					
					
				<!--			
				<div class="btn-box">
					<input type="submit" name="" value="TOP" onClick="location.href='../index.php'">
				</div>
				-->				
					
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
