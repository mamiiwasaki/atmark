"use strict";

// ライブラリ用のオブジェクトの作成
if (window.test === undefined) {window.test = {}}
if (window.test.reversi === undefined) {window.test.reversi = {}}

(function() {
	var _t = test.reversi;	// ショートカットの作成

	// 変数の初期化

	// デバッグ COM スキップ
	_t.comSkp = function(t) {
		t.brd[t.XYToI(0, 0)] = 0;
		t.brd[t.XYToI(1, 0)] = 1;

		t.brd[t.XYToI(3, 3)] = 1;
		t.brd[t.XYToI(4, 3)] = 1;
		t.brd[t.XYToI(5, 3)] = 1;
		
		t.brd[t.XYToI(3, 4)] = 1;
		t.brd[t.XYToI(4, 4)] = 0;
		t.brd[t.XYToI(5, 4)] = 1;
		
		t.brd[t.XYToI(3, 5)] = 1;
		t.brd[t.XYToI(4, 5)] = 1;
		t.brd[t.XYToI(5, 5)] = 1;
	};

	// デバッグ 終了 敗北
	_t.endLose = function(t) {
		t.scnBrd(function(i) {t.brd[i] = 1});
		t.brd[t.XYToI(0, 7)] = t.BLNK;
		t.brd[t.XYToI(0, 0)] = 0;
	};

	// デバッグ 終了 勝利
	_t.endWin = function(t) {
		t.scnBrd(function(i) {t.brd[i] = 0});
		t.brd[t.XYToI(0, 7)] = t.BLNK;
		t.brd[t.XYToI(0, 6)] = 1;
	};

	// デバッグ 終了 引き分け
	_t.endDraw = function(t) {
		t.scnBrd(function(i) {t.brd[i] = i % 2});
		t.brd[t.XYToI(6, 0)] = 1;
		t.brd[t.XYToI(6, 1)] = 1;
		t.brd[t.XYToI(0, 7)] = t.BLNK;
		t.brd[t.XYToI(0, 6)] = 1;
	};

	// デバッグ 周囲判定
	_t.dbgArnd = function(t) {
		t.scnBrd(function(i) {t.brd[i] = t.BLNK});

		t.brd[t.XYToI(0, 1)] = 0;
		t.brd[t.XYToI(1, 2)] = 0;
		t.brd[t.XYToI(0, 3)] = 0;
		t.brd[t.XYToI(0, 4)] = 1;
		t.brd[t.XYToI(0, 5)] = 0;

		t.brd[t.XYToI(2, 2)] = 1;

		t.brd[t.XYToI(3, 2)] = t.brd[t.XYToI(4, 3)] = 0;
		t.brd[t.XYToI(3, 3)] = t.brd[t.XYToI(4, 2)] = 1;
	};

	// デバッグ1
	_t.dbg1 = function(t) {
		reversi.reversi.brd = 
		[-1, -1,  0, -1, -1,  0, -1, -1,
		 -1, -1,  0,  0,  0,  0, -1, -1,
		  0,  0,  0,  0,  0,  0,  1, -1,
		  0,  0,  0,  0,  0,  1,  0,  0,
		  1,  1,  0,  1,  1,  0,  0, -1,
		  0,  1,  0,  1,  0,  0,  0, -1,
		 -1, -1,  0,  0,  0, -1, -1, -1,
		 -1, -1,  0, -1,  0, -1, -1, -1];
	};

	// デバッグ2
	_t.dbg2 = function(t) {
		reversi.reversi.brd = 
		[-1,  0,  1, -1, -1, -1, -1, -1,
		 -1, -1, -1, -1, -1, -1, -1, -1,
		 -1, -1, -1, -1, -1, -1, -1, -1,
		 -1, -1, -1,  1,  0, -1, -1, -1,
		 -1, -1, -1,  0,  1, -1, -1, -1,
		 -1, -1, -1, -1, -1, -1, -1, -1,
		 -1, -1, -1, -1, -1, -1, -1, -1,
		 -1, -1, -1, -1, -1, -1, -1, -1];
	};

})();
