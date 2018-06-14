"use strinct";

if(window.game===undefined){window.game={}}
if(window.game.core===undefined){window.game.core={}}

(function(){
	var _t = game.core;

	_t.getWinW = function(){return window.innerWidth};
	_t.getWinH = function(){return window.innerHeight};
	
	_t.getFitSize = function(w, h){
		var winW = _t.getWinW();
		var winH = _t.getWinH();
		
		var resW, resH;
		if(w/h >= winW/winH){
			resW = winW;
			resH = (h*winW/w)|0;
		} else {
			resW = (w*winH/h)|0;
			resH = winH;
		}
		return {w:resW, h:resH};
	}
})();


