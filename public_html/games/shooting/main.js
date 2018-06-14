// global 
var sc, info;
var run = true;
var fps = 1000/30;
var mouse = new Point();
var ctx;	// canvas2d 
var fire = false;
var counter = 0;

// const
var CHARA_COLOR = 'rgba(0, 0, 255, 0.75)';
var CHARA_SHOT_COLOR = 'rgba(0, 255, 0, 0.75)';
var CHARA_SHOT_MAX_COUNT = 10;
var ENEMY_COLOR = 'rgba(255, 0, 0, 0.75)';
var ENEMY_MAX_COUNT = 10;

window.onload = function(){
	// スクリーンの初期化
	sc = document.getElementById('screen');
	sc.width=256;
	sc.height=256;

	// イベント登録
	sc.addEventListener('mousemove', mouseMove, true);
	sc.addEventListener('mousedown', mouseDown, true);
	window.addEventListener('keydown', keyDown, true);

	// その他のエレメント関連
	info = document.getElementById('info');
	// 2dコンテキスト
	ctx = sc.getContext('2d');

	// 自機初期化
	var charaShot = new Array(CHARA_SHOT_MAX_COUNT);
	for(i = 0; i < CHARA_SHOT_MAX_COUNT; i++){
		charaShot[i] = new CharacterShot();
	}
	
	if(fire){
		for(var i=0; i<CHARA_SHOT_MAX_COUNT; i++){
			if(!charShot[i].alive){
				charaShot[i].set(chara.position, 3, 5);
			}
		}
	}

	// レンダリング処理を呼び出す
	(function(){
		// 
		info.innerHTML = mouse.x+':'+mouse.y;
		// screenクリア
		ctx.clearRect(0, 0, sc.width, sc.height);
		// パスの設定を開始
		ctx.beginPath();
		//chara.position.x = mouse.x;
		//chara.position.y = mouse.y;

		// 自機を描くパスを設定
		//ctx.arc(mouse.x, mouse.y, 10, 0, Math.PI*2, false);
	//	ctx.arc(chara.position.x, chara.position.y, chara.size, 0, Math.PI*2, false);
		// 自機の色を設定
		//ctx.fillStyle = 'rgba(0, 0, 255, 0.75)';
		ctx.fillStyle = CHARA_COLOR;
		ctx.fill();





		// 再起呼び出し
		if(run){
			setTimeout(arguments.callee);
		}
	})();
};
function mouseMove(e){
	mouse.x = e.clientX - sc.offsetLeft;
	mouse.y = e.clientY - sc.offsetTop;
}
function mouseDown(e){
	fire = true;
}
function keyDown(e){
	var kc = e.keyCode;
	if(kc===27){
		run = false;
	}
}
