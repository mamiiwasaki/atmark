<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	一覧表示計の関数群
 *----------------------------------------------------------------------------*/
//////////////////////////////////////////////////////////////////////
//
// 一覧の検索値
// 検索値はデータコンテナに保管する
// 
//////////////////////////////////////////////////////////////////////
/*
 * 検索のデフォルト値をセットする
 */
function setSearchInitials($search_session_name, $search_initials, $search_items=array()){
	global $data;

	// TODO 初期値をセットするのは、リセット時、もしくはPOSTもAttributeもなとき。
	if(isset($_GET['reset']) || (empty($_POST) && !$data->hasAttribute($search_session_name))){
		// 初期値の設定がない場合、検索アイテム全てを空に。
		if(empty($search_initials)) {
			if(!empty($search_items)){
				foreach($search_items as $name){
					$search_data[$name] = '';
				}
				$data->setAttribute($search_session_name, $search_data);
				return $search_data;
			} else {
				return '';
			}
		} 

		// 初期値の設定がある場合
		foreach($search_initials as $name=>$val){
			if(is_array($val)){
				foreach($val as $v){
					$search_data[$name][$v] = $v;
				}
			} else {
				$search_data[$name] = $val;
			}
		}
		$data->setAttribute($search_session_name, $search_data);
		return $search_data;
	}
}
/*
 * 検索用の値をセットする
 * POSTされた値があったらセット。
 * なければセッションから取得。
 */
function setSearchPosts($search_session_name, $search_items){
	global $data;
	$search_data = array();
	if($data->hasAttribute($search_session_name)){
		$search_data = $data->getAttribute($search_session_name);
	}
	if(isset($_GET['nopostdata'])){
		logsave("logsave", "nopostdata");
		return $search_data;
	}
	foreach($search_items as $name){
		if(!isset($_POST[$name])) continue;
		if(is_array($_POST[$name])){
			foreach($_POST[$name] as $k=>$v){
				$search_data[$name][$k] = $v;
			}
		} else if(strcmp($_POST[$name], '')==0){
		//if(empty($_POST[$name])){
			$search_data[$name] = '';
		} else {
			$search_data[$name] = htmlspecialchars($_POST[$name]);
		}
	}
	$data->setAttribute($search_session_name, $search_data);
	return $search_data;
}

/*
 * ページングの作成
 * $total_rec	総レコード数
 * $limit 		表示したい件数
 */
function createPaging($total_rec, $limit = 10, $sub = 0, $page2 = 0){
	$pager = "";

	if(!$sub){
		$current_page = (!empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1; // 現在のページ
	}else{
		$current_page = (!empty($_REQUEST['page_sub'])) ? $_REQUEST['page_sub'] : 1; // 現在のページ
	}

	$total_page = ceil($total_rec / $limit); //総ページ数
	$show_nav = 5;   //表示するナビゲーションの数

	//全てのページ数が表示するページ数より小さい場合、総ページを表示する数にする
	if ($total_page < $show_nav) {
		$show_nav = $total_page;
	}

	//トータルページ数が2以下か、現在のページが総ページより大きい場合表示しない
	if ($total_page <= 1 || $total_page < $current_page) {
		'<li>' . $total_rec . '件</li>';
	}

	//総ページの半分
	$show_navh = floor($show_nav / 2);
	//現在のページをナビゲーションの中心にする
	$loop_start = $current_page - $show_navh;
	$loop_end = $current_page + $show_navh;
	//現在のページが両端だったら端にくるようにする
	if ($loop_start <= 0) {
		$loop_start = 1;
		$loop_end = $show_nav;
	}
	if ($loop_end > $total_page) {
		$loop_start = $total_page - $show_nav + 1;
		$loop_end = $total_page;
	}

	//2ページ移行だったら「一番前へ」を表示
	if ($current_page > 2) {
		$pager .= '<li onclick="getList(1, 1,'.$sub.','.$page2.');"><i><<</i></li>'."\n";
	}
	//最初のページ以外だったら「前へ」を表示
	if ($current_page > 1) {
		$pager .= '<li onclick="getList(1, '.($current_page - 1).','.$sub.','.$page2.');"><i><</i></li>'."\n";
	}
	// no
	for ($i = $loop_start; $i <= $loop_end; $i++) {
		if ($i > 0 && $total_page >= $i) {
			if ($i == $current_page) {
				// 現在のページ
				$pager .= '<li><strong>' . $i . '</strong></li>'."\n";
			} else {
				$pager .= '<li onclick="getList(1, '.$i.','.$sub.','.$page2.');"><i>'.$i.'</i></li>'."\n";
			}
		}
	}
	//最後のページ以外だったら「次へ」を表示
	if ($current_page < $total_page) {
		$pager .=' <li onclick="getList(1, ' . ($current_page + 1) . ','.$sub.','.$page2.');"><i>></i></li>'. "\n";
	}
	//最後から２ページ前だったら「一番最後へ」を表示
	if ($current_page < $total_page - 1) {
		$pager .= ' <li onclick="getList(1, ' . $total_page . ','.$sub.','.$page2.');"><i>>></i></li>'. "\n";
	}

	if($total_rec==0){
		return "";
	} else {
		return sprintf('<li class="count">%s 件</li> %s', number_format($total_rec), $pager);
	}
}

/*
 * ページングの作成 matealize.js用
 * materialize.js用のスタイル
 * $total_rec	総レコード数
 * $limit 		表示したい件数
 */
function createPagingMaterialize($total_rec, $limit = 10){
	$pager = "";

	$current_page = (!empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1; // 現在のページ

	$total_page = ceil($total_rec / $limit); //総ページ数
	$show_nav = 5;   //表示するナビゲーションの数

	//全てのページ数が表示するページ数より小さい場合、総ページを表示する数にする
	if ($total_page < $show_nav) {
		$show_nav = $total_page;
	}

	//トータルページ数が2以下か、現在のページが総ページより大きい場合表示しない
	if ($total_page <= 1 || $total_page < $current_page) {
		'<li>' . $total_rec . '件</li>';
	}

	//総ページの半分
	$show_navh = floor($show_nav / 2);
	//現在のページをナビゲーションの中心にする
	$loop_start = $current_page - $show_navh;
	$loop_end = $current_page + $show_navh;
	//現在のページが両端だったら端にくるようにする
	if ($loop_start <= 0) {
		$loop_start = 1;
		$loop_end = $show_nav;
	}
	if ($loop_end > $total_page) {
		$loop_start = $total_page - $show_nav + 1;
		$loop_end = $total_page;
	}

	//2ページ移行だったら「一番前へ」を表示
	if ($current_page > 2) {
		$pager .= '<li class="prev"><a href="#!" onclick="getListAdmin(1, 1);"><i class="material-icons">first_page</i></a></li>';
	} else {
		// inactive
		$pager .= '<li class="prev inactive"><i class="material-icons">first_page</i></li>';
	}
	//最初のページ以外だったら「前へ」を表示
	if ($current_page > 1) {
		$pager .= '<li class="prev"><a href="#!" onclick="getListAdmin(1, 1);"><i class="material-icons">chevron_left</i></a></li>';
	} else {
		// inactive
		$pager .= '<li class="prev inactive"><i class="material-icons">chevron_left</i></li>';
	}
	// no
	for ($i = $loop_start; $i <= $loop_end; $i++) {
		if ($i > 0 && $total_page >= $i) {
			if ($i == $current_page) {
				// 現在のページ
				$pager .= '<li class="active"><a href="#!">' . $i . '</a></li>';
			} else {
				$pager .= '<li class="waves-effect" onclick="getListAdmin(1, '.$i.');"><a href="#!">'.$i.'</a></li>';
			}
		}
	}
	//最後のページ以外だったら「次へ」を表示
	if ($current_page < $total_page) {
		$pager .= '<li class="next" onclick="getListAdmin(1, ' . ($current_page + 1) . ');"><a href="#!"><i class="material-icons">chevron_right</i></a></li>';
	} else {
		// inactive
		$pager .= '<li class="next inactive"><i class="material-icons">chevron_right</i></li>';
	}
	//最後から２ページ前だったら「一番最後へ」を表示
	if ($current_page < $total_page - 1) {
		$pager .= '<li class="next" onclick="getListAdmin(1, ' . $total_page . ');"><a href="#!"><i class="material-icons">last_page</i></a></li>';
	} else {
		// inactive
		$pager .= '<li class="next inactive"><i class="material-icons">last_page</i></li>';
	}

	if($total_rec==0){
		return "";
	} else {
		return sprintf('<li class="count">%s 件</li> %s', number_format($total_rec), $pager);
	}
}
