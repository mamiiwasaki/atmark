<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild
 *	
 *	configuration file
 *----------------------------------------------------------------------------*/
function getMenu(){
	return '
		<li><a href="othello.html">オセロ</a></li>
		<li><a href="math.html">さんすう</a></li>
		<li><a href="math_bird.html">バード</a></li>
		<li><a href="tashiten.html">たしてん</a></li>
		<li><a href="panel_game.html">まちがいさがし</a></li>
		<li><a href="mogura.html">もぐらたたきゲーム</a></li>
		<li><a href="memory.html">しんけいすいじゃく</a></li>
		<li><a href="puzzle.html">パズル</a></li>
		<li><a href="neko">ねこねこgogo</a></li>
		<li><a href="pokemon.html">ポケモン</a></li>
		<li><a href="tetris/">テトリス</a></li>
		<li><a href="slot.html">スロット</a></li>
		<li><a href="intaractive.html/">ボール</a></li>
		<li><a href="tresurehunter.html/">宝箱</a></li>
		<li><a href="flashcards.html">単語帳</a></li>
		<li><a href="seconds5.html">5秒当て</a></li>
		<li><a href="warikan.html">割り勘</a></li>
		<li><a href="slideshow.html">スライドショー</a></li>
		';
}
function getHightJs(){
	return '
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shCore.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushCss.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushPhp.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushXml.js"></script>
		<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter/styles/shCore.css">
		<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter/styles/shThemeDefault.css">
		<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter/styles/shBrushCSharp.css">
		<script type="text/javascript">
			SyntaxHighlighter.all();
		</script>
		';
}