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
		<li><a href="knowledge/css.html">knowledge</a></li>
		<li><a href="tools/colorchart.html">tools</a></li>
		<li><a href="drawtyping.html">drawtyping</a></li>
		<li><a href="kuku.html">九九表</a></li>
		<li><a href="stopwatch.html">ストップウォッチ</a></li>
		<li><a href="mousestalker.html">マウスストーカー</a></li>
		<li><a href="mousestalker2.html">マウスストーカー2</a></li>
		<li><a href="lifegame.html">life game</a></li>
		<li><a href="colorchart.html">Color Chart</a></li>
		<li><a href="source_escape.html">HTMLエンコード</a></li>
		<li><a href="othello.html">オセロ</a></li>
		<li><a href="mogura.html">もぐら</a></li>
		';
}
function getSubMenu(){
    return '<ul id="submenu">
                <li><a href="css.html">css</a></li>
                <li><a href="css_sample.html">css_sample</a></li>
                <li><a href="canvas.html">canvas</a></li>
                <li><a href="linux.html">linux</a></li>
                <li><a href="php.html">php</a></li>
                <li><a href="javascript.html">js</a></li>
            </ul>
            <div style="clear: both;"></div>';
}
function getSubMenu2(){
    return '<ul id="submenu">
                <li><a href="colorchart.html">css</a></li>
                <li><a href="source_escape.html">css_sample</a></li>
                <li><a href="pw_generator.html">canvas</a></li>

            </ul>
            <div style="clear: both;"></div>';
}
function getSideBar(){
	return '
		<div id="sidebar">
			<section class="-2u">
				<header>
					<h2>menu</h2>
				</header>
				<div>
					<section class="6u">
						<ul class="default">'.getMenu().'</ul>
					</section>
				</div>
			</section>
		</div>
		'
	;/*
		<section class="6u">
			<ul class="default">
				<li><a href="#">Donec facilisis tempor</a></li>
				<li><a href="#">Nulla convallis cursus</a></li>
				<li><a href="#">Integer congue euismod</a></li>
				<li><a href="#">Venenatis vulputate</a></li>
				<li><a href="#">Morbi ligula volutpat</a></li>
			</ul>
		</section>
	*/
}
function getHightJs(){
	return '
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shCore.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushCss.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushPhp.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushJScript.js"></script>
		<script type="text/javascript" src="/js/syntaxhighlighter/scripts/shBrushXml.js"></script>
		<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter/styles/shCore.css">
		<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter/styles/shThemeDefault.css">
		<script type="text/javascript">
			SyntaxHighlighter.all();
		</script>
		';
	//<pre class="brush: js"></pre>
}