function loadScript(src, callback) {
	var done = false;

	var sources = [
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

	// head
	var head = document.getElementsByTagName('head')[0];
	// scripts
	for (var i=0; i<sources.length; i++) {
		if(sources[i].match(/\.js$/)) {
			var script = document.createElement('script');
			script.setAttribute("type", "text/javascript");
			script.setAttribute("src", sources[i]);
			head.appendChild(script);

		} else if (sources[i].match(/\.css$/)){
			var link = document.createElement('link');
			with(link){
				href = sources[i];
				type = 'text/css';
				rel = 'stylesheet';
			}
		}
	}

	script.onload = script.onreadystatechange = function() {
		if ( !done && (!this.readyState ||
				this.readyState === "loaded" || this.readyState === "complete") ) {
			done = true;
			callback();
			// Handle memory leak in IE
			script.onload = script.onreadystatechange = null;
			if ( head && script.parentNode ) {
				head.removeChild( script );
			}
		}
	};
}


/*
 * 
loadScript("/js/import.js", function() {
	console.log('script loaded');
	SyntaxHighlighter.all();
});
 */