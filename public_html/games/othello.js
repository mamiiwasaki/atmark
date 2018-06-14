/*--------------------------------------------
 * init
 * field（配列）を生成する。
 * デフォルトの石を４つセット。
 * boradを表示する。
 */
function init(){
	// 打ち手
	player = BLACK;

	// (マス+2)×(マス+2)のフィールドを作って、石の色を保持する。
	// 0: 石がない, BLACK: 黒石, WHITE: 白石、9: 壁
	var v;
	for(var y=0; y<(parseInt(cell_cnt)+2); y++){
		field[y] = [];
		for(var x=0; x<(parseInt(cell_cnt)+2); x++){
			v = 0;
			if(y===0 || y===(parseInt(cell_cnt)+1) || x===0 || x===(parseInt(cell_cnt)+1)){
				v = 9;
			}
			field[y][x] = v;
		}
	}

	// デフォルトの石
	a = cell_cnt / 2;
	b = a+1;
	field[a][a] = BLACK;
	field[b][b] = BLACK;
	field[a][b] = WHITE;
	field[b][a] = WHITE;

	// 石盤表示
	showBoard();
	// score
	document.getElementById('score_black').innerHTML = 2;
	document.getElementById('score_white').innerHTML = 2;
	// 打ち手
	document.getElementById('player').innerHTML = STONE_STR[player];
}
/*--------------------------------------------
 * 盤面を表示する
 */
function showBoard(){
    var board = document.getElementById('board');
    board.innerHTML = '';

    for(var y=1; y<=cell_cnt; y++){
        for(var x=1; x<=cell_cnt; x++){
            var cell = document.createElement('div');
            // event
            cell.addEventListener('click', clickEvent, false);
            // セルのID
            cell.id = 'p_'+y+'_'+x;
            // class
            cell.className = 'cell';
            if(cell_cnt==10){
                cell.className = 'cell10';	// 小さいセル
            }
            // cell_cntピースごとに改行する
            if((x % cell_cnt) ===1){
                cell.style.clear = 'both';
            }
            // 石をおく
            if(field[y][x]!==0){
                cell.style.backgroundImage = STONE[field[y][x]];
            }
            // append
            board.appendChild(cell);
        }
    }
}

/*
 * 指定のマスが、ひっくり返せるマスの座標を配列で返す
 * ひっくり返せるセルがなかったらfalseを返す
 */
function checkChangeable(y, x, player){
   // console.log('player: '+player+": ",y, x, field[y][x]);

    // 選択した箇所に石が置かれている場合はスルー
    if(field[y][x]!==0){
        return false;
    }

    // 相手
    var enemy = (player === BLACK) ? WHITE : BLACK;
    var enemy_cnt = 0; // ひっくり返せる敵の数

    // 変更できるマスの座標
    var changeable_field = [];
    // 周囲8箇所
    var round_y = [-1, -1, 0, 1, 1, 1, 0, -1];
    var round_x = [0, 1, 1, 1, 0, -1, -1, -1];

    // 周囲の敵を数える
    for(var pos=0; pos<8; pos++){
        var tmp_y = parseInt(y) + round_y[pos];
        var tmp_x = parseInt(x) + round_x[pos];

        //-----------------------------------------------------
        // 周囲8箇所のどれかに敵がいた時実行
        if(field[tmp_y][tmp_x]===enemy){
            //-----------------------------------------------------
            // 敵の延長線上に自分がいるか。
            var enemy_pos = ''+tmp_y+tmp_x;	// 敵の座標
            var player_exists = false;
            var tmp_field = [];

            // 端までチェックしていく
            for(var i=0; i<cell_cnt; i++){
                if(i>0){
                    tmp_y+= round_y[pos];
                    tmp_x+= round_x[pos];
                }

                // 端まで行った、空白が見つかったらおわり。
                if(field[tmp_y][tmp_x]===9 || field[tmp_y][tmp_x]===0) {
                    break;
                }
                // 延長線上の自分がみつかったらおわり
                if(field[tmp_y][tmp_x]===player){
                    player_exists = true;
                    break;
                }
                // 座標を取っておく。(p_y_x)
                tmp_field[i] = 'p_'+tmp_y+'_'+tmp_x;
                // ひっくり返せる敵の数
                enemy_cnt++;
            } // for to cell_cnt

            // 敵の延長線上にplayerがいたら（ひっくり返せるマスがあったら）取っておいた座標情報を配列に。
            if(player_exists!==false){
                changeable_field[enemy_pos] = tmp_field;	// 座標情報
            }
        } //enemy
    } // pos8
    // ひっくり返せるセルがなかったらfalse
    if(changeable_field.length===0){
        return false;
    }
    return changeable_field;

}

/*--------------------------------------------
 * スコア
 */
function calcScore(){
	var s = [];
	s[BLACK] = 0;
	s[WHITE] = 0;
	for(var y=1; y<=cell_cnt; y++){
		for(var x=1; x<=cell_cnt; x++){
			s[field[y][x]]++;
		}
	}
	document.getElementById('score_black').innerHTML = s[BLACK];
	document.getElementById('score_white').innerHTML = s[WHITE];
	score[BLACK] = s[BLACK];
	score[WHITE] = s[WHITE];
}
/*--------------------------------------------
 * 盤面の線を初期化する
 */
function initLine(){
	// 線を戻す
	for(var y=1; y<=cell_cnt; y++){
		for(var x=1; x<=cell_cnt; x++){
			document.getElementById('p_'+y+'_'+x).style.border='1px solid #000';
			document.getElementById('p_'+y+'_'+x).innerHTML = '';
		}
	}
}
/*--------------------------------------------
 * 変更可能なマスの枠線を変更
 * 変更箇所数を返す
 */
function markLine(arr){
	var cnt = 0;
	for(var i in arr){
		for(var j in arr[i]){
			var pos_a = arr[i][j].split('_');	// (p_y_x)
			var y = pos_a[1];
			var x = pos_a[2];
			document.getElementById('p_'+y+'_'+x).style.border='1px solid blue';
			cnt++;
		}
	}
	return cnt;
}
/*--------------------------------------------
 * 判定
 */
function hantei(){
	if(score[BLACK]>score[WHITE]){
		alert('くろのかち！！！');
	} else if(score[BLACK]<score[WHITE]){
		alert('しろのかち！！！');
	} else {
		alert('ひきわけ！！！');
	}
}
/*--------------------------------------------
 * 次の手があるかどうかを返す
 */
function checkNextHand(){
	for(var y=1; y<=cell_cnt; y++){
		for(var x=1; x<=cell_cnt; x++){
			// チェック
			changeable_field = checkChangeable(y, x, player);
			if(changeable_field!==false){
				return true;
			}
		}
	}
	return false;
}
