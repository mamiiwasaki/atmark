(function() {
'use strict';
var loadFiles = [];
if (window.matchMedia('(max-width: 767px)').matches) {
	loadFiles = [
	'/js/syntaxhighlighter/scripts/shCore.js',
	'/js/syntaxhighlighter/scripts/shBrushJScript.js',
	'/js/syntaxhighlighter/scripts/shBrushPhp.js',
	'/js/syntaxhighlighter/scripts/shBrushCss.js',
	'/js/syntaxhighlighter/scripts/shBrushSql.js',
	'/js/syntaxhighlighter/scripts/shBrushXml.js',
	'/js/common.js',
	'/js/jquery-ui.min.js',
	'/js/jquery.min.js',
	'/js/syntaxhighlighter/styles/shCore.css', 
	'/js/syntaxhighlighter/styles/shThemeDefault.css', 
	'/js/syntaxhighlighter/styles/shBrushCSharp.css'
	];
	injectFile();
} else if (window.matchMedia('(min-width: 768px)').matches) {
	loadFiles = [
	'/js/syntaxhighlighter/scripts/shCore.js',
	'/js/syntaxhighlighter/scripts/shBrushJScript.js',
	'/js/syntaxhighlighter/scripts/shBrushPhp.js',
	'/js/syntaxhighlighter/scripts/shBrushCss.js',
	'/js/syntaxhighlighter/scripts/shBrushSql.js',
	'/js/syntaxhighlighter/scripts/shBrushXml.js',
	'/js/common.js',
	'/js/jquery-ui.min.js',
	'/js/jquery.min.js',
	'/js/syntaxhighlighter/styles/shCore.css', 
	'/js/syntaxhighlighter/styles/shThemeDefault.css', 
	'/js/syntaxhighlighter/styles/shBrushCSharp.css'
	];
	injectFile();
}

function injectFile() {
	var tags = document.createDocumentFragment();
	for(var i=0; i<loadFiles.length; i++) {
		if(loadFiles[i].match(/\.css$/)) {
			var link  = document.createElement('link');
			link.rel  = 'stylesheet';
			link.href = loadFiles[i];
			tags.appendChild(link);
		} else if (loadFiles[i].match(/\.js$/)){
			var script = document.createElement('script');
			script.src = loadFiles[i];
			tags.appendChild(script);
		}
		document.getElementsByTagName('head')[0].appendChild(tags);
	}
}
}).call(this);