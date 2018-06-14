"use strict";

// ライブラリ用のオブジェクトの作成
if (window.test === undefined) {window.test = {}}
if (window.test.sound === undefined) {window.test.sound = {}}

(function() {
	var _t = test.sound;	// ショートカットの作成

	// 変数の初期化

	// 開始/停止
	_t.plyStp = function() {
		var c = reversi.canvas.c;
		var cnt = 0;
		$(c.cnvs).click(function(e) {
			if (cnt == 0) resouce.sound.play("se0-0");
			else          resouce.sound.stop("se0-0");
			cnt = 1 - cnt;
		});
	};

	// ループ開始/停止
	_t.plyLoopStp = function() {
		var c = reversi.canvas.c;
		var cnt = 0;
		$(c.cnvs).click(function(e) {
			if (cnt == 0) resouce.sound.playLoop("se0-0");
			else          resouce.sound.stop("se0-0");
			cnt = 1 - cnt;
		});
	};

	// 開始/一時停止
	_t.plyPause = function() {
		var c = reversi.canvas.c;
		var cnt = 0;
		$(c.cnvs).click(function(e) {
			if (cnt == 0) resouce.sound.play("bgm0");
			else          resouce.sound.pause("bgm0");
			cnt = 1 - cnt;
		});
	};

	// SE開始
	_t.plySe = function() {
		var c = reversi.canvas.c;
		$(c.cnvs).click(function(e) {
			resouce.sound.playSE("se0");
		});
	};

	// BGM開始/切り替え
	_t.plyBGM = function() {
		var c = reversi.canvas.c;
		var cnt = 0;
		$(c.cnvs).click(function(e) {
			if (cnt == 0) resouce.sound.playBGM("bgm0");
			else          resouce.sound.playBGM("bgm1");
			cnt = 1 - cnt;
		});
	};

})();
