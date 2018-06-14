////////////////////////////////////////////////////////////////////////////////
//
// 汎用系
//
////////////////////////////////////////////////////////////////////////////////
// パラメータを除去したURLを返す
function getUrl(){
	return location.protocol + "//" + location.host + location.pathname ;//+ location.search;
}
function getUrlParam(){
	return location.protocol + "//" + location.host + location.pathname + location.search;
}
/*
 * 実行中イメージ表示
 */
function showLoadingImg(){
	$('#container').append('<img src="/images/gif-load.gif" id="img_loading">');
}
/*
 * 実行中イメージ除去
 */
function hideLoadingImg(){
	$("#img_loading").remove();
}
/*
 * JSONデータをパースする
 */
function parseJson(j_data){
	if(j_data === ''){
		console.log('parseJson', '結果が空でした');
		return;
	}
	// 内容をチェック。エラーがあったらアラート表示して処理を終了する
	checkAjaxData(j_data);
	// パースして返す
	return JSON.parse(j_data);
}
/*
 * 内容をチェック。エラーがあったらアラート表示して処理を終了する
 */
function checkAjaxData(j_data){
	if( j_data.match(/PDO_ERROR/) || j_data.match(/Notice/) || j_data.match(/Warning/) || j_data.match(/Fatal error/)|| j_data.match(/Parse error/)) { 
		// エラーがあったらalert
		console.log('checkAjaxData', j_data);
		return false;
	}
	return true;
}
/*
 * 汎用的なサジェスト用
 * action_name(従業員リスト取得:get_suggest_employee, 顧客リスト取得:get_suggest_customer)
 * 汎用的なactionは、index/actions/action.phpへ。
 */
function suggest(action_name, no_name, name_name){
	$('[name='+name_name+']').autocomplete({
		source: '/action.html?action='+action_name,
		delay: 0,
		minLength: 0,
		change: function(){
			$('[name='+no_name+']').val('');	// 初期化
			// 候補から選択されたら番号代入
			if ($(this).val().indexOf(":") != -1) {
				var tmp = $(this).val().split(":");
				$(this).val(tmp[1]);
				$('[name='+no_name+']').val(tmp[0]);
			}
		}
	}).focus(function () {
		$(this).autocomplete("search", "");
	});
}



////////////////////////////////////////////////////////////////////////////////
//
// 一覧系
//
////////////////////////////////////////////////////////////////////////////////
/**
 * 一覧をセットする
 * formのid はsearch_formにする
 * テーブルのidは「list_table」
 */
function getList(send_post_data=1, page=1, sub = 0, page2 = 0){
	var url = getUrl() + '?action=get_list';
	if(!isNaN(page)){
		if(!sub){
			url = url + '&page='+page+'&page_sub='+page2;
		}else{
			url = url + '&page='+page2+'&page_sub='+page;
		}
	}
	/*
	 * 検索フォーム更新じゃなくて、リンクで来た時はPOSTデータ送らない
	 */
	if(send_post_data!==1){
		url = url+'&nopostdata';
	}
	//console.log('url', url);

	// 一覧セット
	$.post(url, $('#search_form').serialize(), function(j_data){
		//console.log('getList j_data', j_data);
		//alert(j_data);
		// ヘッダー行取得
		// 内容をチェック。エラーがあったらアラート表示して処理を終了する
		checkAjaxData(j_data);
		// JSONデータパース
		//var parse_data = JSON.parse(j_data);
		var parse_data = parseJson(j_data);
		//console.log('parse_data', parse_data);
		// ヘッダー行取得
		var header = $('#list_table tr').eq(0).html();
		// ページャーセット
		$('#pagination').html(parse_data['pager']);
		// 一覧セット
		$('#list_table').html('<tr>'+header+'</tr>'+parse_data['list']);

		// ヘッダー行取得
		var header_sub = $('#list_table_sub tr').eq(0).html();
		// ページャーセット(追加分のみIDを指定）
		$('#pagination_sub').html(parse_data['pager_sub']);
		// 一覧セット
		$('#list_table_sub').html('<tr>'+header_sub+'</tr>'+parse_data['list_sub']);
	});
}

/**
 * 検索フォームをクリアする
 * formのidは「search_form」
 */
function clearForm(){
	//$('#search_form').find('input[type="date"], :text, textarea, select').val("").end().find(":checked").prop("checked", false);
	//$('#search_form').find("textarea, :text, select").val("").end().find(":checked").prop("checked", false);
	$('input[type="date"], :text, textarea, select').val("").end().find(":checked").prop("checked", false);
	
}

/**
 * １行削除
 */
function deleteRecord(elem, reload_list_flg=1){
	confirmBox.Confirm(
		'削除してよろしいですか？',
		// はい
		function(){
			$.get(getUrl() + '?action=del&id='+elem.data('id'), function(){
				elem.parent().parent().remove();
				// 一覧再取得
				if(reload_list_flg === 1){
					getList();
				}
			});
		},
		// いいえ
		function(){}
	);
}
/**
 * 詳細ポップアップ
 */
function showDetail(id,sub = 0,option='normal'){
	//console.log(getUrl() + '?action=get_detail&id='+id);
	if(!sub){
		$.get(getUrl() + '?action=get_detail&id='+id, function(detail){
			showPopup(detail,0,option);
		});
	}else{
		$.get(getUrl() + '?action=get_detail_sub&id='+id, function(detail){
			showPopup(detail,0,option);
		});
	};
}



////////////////////////////////////////////////////////////////////////////////
//
// ポップアップ系
//
////////////////////////////////////////////////////////////////////////////////
/**
 * メッセージを表示して、ふわっと消す
 */
function showNotice(msg){
	$('#container').append($('<div id="msg" class="notice_message">' + msg + '</div>'));
	$('#msg').fadeIn(100).delay(1000).fadeOut(400, function(){$('#msg').remove();});
}

/*
 * ポップアップウィンドウを表示する
 */
function showPopup(contents,sub = 0,option='normal'){
	// ポップアップウィンドウ
	var class_option = '';
	switch(option){
		case 'normal':
			class_option = '';
			break;
		case 'small':
			class_option = ' popup_small';
			break;
	}
	
	if(!sub){
		var popup= '<div id="screen_black" class="screen_black" onclick="closePopup();"></div>'
				+  '<div id="popupbox" class="popupbox'+class_option+'">'
				+     '<div id="popupbox_inner" class="popupbox_inner">'
				+        contents
				+     '</div>'
				+   '<div id="popupbox_foot" class="popupbox_foot" onclick="closePopup();">閉じる</div>'
				+ '</div>';
		// append
		$('#container').append(popup);
	}else{
		// ポップアップウィンドウ
		var popup= '<div id="screen_black_child" class="screen_black" onclick="closePopupChild();"></div>'
				+  '<div id="popupbox_child" class="popupbox'+class_option+'">'
				+     '<div id="popupbox_inner_child"  class="popupbox_inner">'
				+        contents
				+     '</div>'
				+   '<div id="popupbox_foot_child" class="popupbox_foot" onclick="closePopupChild();">閉じる</div>'
				+ '</div>';
		// append
		$('#containerchild').append(popup);
	}
}
/*
 * ポップアップを閉じる
 */
function closePopup(){
	$("#screen_black").remove();
	$("#popupbox").remove();
}
function closePopupChild(){
	$("#screen_black_child").remove();
	$("#popupbox_child").remove();
}
/*
 * ダイアログを表示
 */
function showDialog(msgText){
	var dialog = '<div id="screen_black" class="screen_black"></div>'
			+'<div id="dialog">'
			+'	<div id="dialog_inner">'+msgText+'</div>'
			+'	<div id="dialog_button">'
			+'		<button class="btn" onclick="closeDialog();">OK</button>'
			+'	</div>'
			+'<div id="dialog_foot" onclick="closeDialog();"><i class="fa fa-2x fa-times-circle" aria-hidden="true"></i></div>'
			+'</div>';
	$("#container").append(dialog);
}
/*
 * confirmを表示
 */
var confirmBox = (function(){
	return {
		// ******************************************
		// 関数名：確認(confirm)
		// ******************************************
		Confirm: function(msgText, yesFunc, noFunc){
			var dialog = '<div id="screen_black" class="screen_black"></div>'
				+'<div id="dialog">'
				+'	<div id="dialog_inner">'+msgText+'</div>'
				+'	<div id="dialog_button">'
				+'		<a id="btn_yes" class="btn">はい</a>'
				+'		<a id="btn_no" class="btn">いいえ</a>'
				+'	</div>'
				+'</div>';
			$("#container").append(dialog);

			// 「はい」ボタン
			$('#btn_yes').on('click', function(){
				closeDialog();
				yesFunc();
			});
			// 「いいえ」ボタン
			$('#btn_no').on('click', function(){
				closeDialog();
				noFunc();
			});
		}
	}
})();
/*
 * ダイアログを閉じる
 */
function closeDialog(){
	$("#screen_black").remove();
	$("#dialog").remove();
}

/*
 * 全角を半角に変換
 */
function zenToHan(strVal){
	// 半角変換
	var halfVal = strVal.replace(/[！-～]/g,
		function( tmpStr ) {
			// 文字コードをシフト
			return String.fromCharCode( tmpStr.charCodeAt(0) - 0xFEE0 );
		}
	);
	return halfVal;
}

////////////////////////////////////////////////////////////////////////////////
/* 2017/02/27
 * 日付チェック
 */
function ValidDate(y, m, d) {
	m=m-0;	// cast
	dt = new Date(y, (m-1), d);
	return(dt.getFullYear()==y && dt.getMonth()==(m-1) && dt.getDate()==d);
}
////////////////////////////////////////////////////////////////////////////////
/* 日付フォーマット　20180101 ---> 2018/01/01
 */
function formatDate(date){
	// 全角を半角に
	date = zenToHan(date);

	// 20170501
	if(date.match(/^\d{8}$/)){
		return date.slice(0,4)+'/'+date.slice(4,6)+'/'+date.slice(-2);
	}
	// 2017/5/1
	if(date.match(/^(\d{4})(\/|-)(\d{1})(\/|-)(\d{1})$/)){
		return date.slice(0,4)+'/0'+date.slice(5,6)+'/0'+date.slice(-1);
	}
	// 2017/5/01
	if(date.match(/^(\d{4})(\/|-)(\d{1})(\/|-)(\d{2})$/)){
		return date.slice(0,4)+'/0'+date.slice(5,6)+'/'+date.slice(-2);
	}
	// 2017/05/1
	if(date.match(/^(\d{4})(\/|-)(\d{2})(\/|-)(\d{1})$/)){
		return date.slice(0,4)+'/'+date.slice(5,7)+'/0'+date.slice(-1);
	}
	return date;
}
/*
* 時刻フォーマット　1200 ---> 12:00
*/
function formatTime(time){
	// 全角を半角に
	time = zenToHan(time);
	if(time.match(/^\d{4}$/)){
		return time.slice(0,2)+':'+time.slice(-2);
	} else if(time.match(/^\d{3}$/)){
		return '0'+time.slice(0,1)+':'+time.slice(-2);
	} else if(time.match(/^\d{1}:\d{2}$/)){
		return '0'+time.slice(0,1)+':'+time.slice(-2);
	} else {
		return time;
	}
}
/*
 * 郵便番号フォーマット　1234567 ---> 123-4567
 */
function formatZip(zip){
	// 全角を半角に
	zip = zenToHan(zip);
	if(zip.match(/^\d{7}$/)){
		return zip.slice(0,3)+'-'+zip.slice(-4);
	}
	return zip;
}
function exportFile(prm, file_name){
	if(!confirm('ファイルを出力してよろしいですか？')) return false;

	// エクセルエクスポート！
	location.href=file_name+'?'+prm;
}
/*
 * ドリルダウン
 */
function set_pulldown(elem, dest, action_name){
	$('[name='+dest+']').val('');	// 初期化
	var code = elem.val();
	$.get('/action.html?action='+action_name+'&code='+code, function(j_data){
		$('[name='+dest+']').html(j_data);
	});
}
//nヶ月前後の年月日を取得する
function getAddMonthDate(year,month,day,add){
	var addMonth = month + add;
	var endDate = getEndOfMonth(year,addMonth);//add分を加えた月の最終日を取得

	//引数で渡された日付がnヶ月後の最終日より大きければ日付を次月最終日に合わせる
	//5/31→6/30のように応当日が無い場合に必要
	if(day > endDate){
		day = endDate;
	}else{
		day = day - 1;
	}

	var addMonthDate = new Date(year,addMonth - 1,day);
	return addMonthDate;
}

//今月の月末日を取得
//※次月の0日目＝今月の末日になる
function getEndOfMonth(year,month){
	var endDate = new Date(year,month,0);
	return endDate.getDate();
}

