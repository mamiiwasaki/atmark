////////////////////////////////////////////////////////////////////////////////
//
// フォーム系js
//
////////////////////////////////////////////////////////////////////////////////
function getUrl(){
	return  location.protocol + "//" + location.host + location.pathname ; //+ location.search;
}

/*--------------------------------------------------
 * 入力内容をチェックする
 * action=check
 --------------------------------------------------*/
function checkForm(){
	//var fd = $('form').serialize();
	var fd = new FormData($('form').get(0));

	///////////////////////////////////
	// check
	$.ajax({
		url: getUrl() + '?action=check',
		type: 'POST',
		data: fd,
		cache       : false,
		contentType : false,
		processData : false,
		dataType    : "html"
	}).done(function(j_data){
		/////////////////////////////////
		// エラーメッセージを初期化
		$('.error').each(function(index , elem){
			$(elem).remove();
		});
		$('input').css("background-color", "");
//alert(j_data);
		/////////////////////////////////
		//
		// jsonデータをパースする
		// エラーがあったら、メッセージ表示して終了。
		// 
		/////////////////////////////////
		if(j_data !== '') {
			// エラーメッセージ表示
			// 一番初めのエラーのあるタグへスクロール
			displayError(j_data);
			return false;
		}

		/////////////////////////////////
		//
		// OKだった
		// 
		/////////////////////////////////
		// ボタン（戻る、登録する）
		var btn_str = '<input type="button" id="btn_back" class="btn back" value="戻る">'
				+ '<input type="button" id="btn_save" class="btn submit" value="登録する">';
		$('.btn_box').html(btn_str);
		$('.confirm_box').css('display', 'block');
		$('#to_list').css('display', 'none');

		/////////////////////////////////
		// データを確認モードにする
		/////////////////////////////////
		// 通常タグ
		$('input[type="date"], input[type="tel"], input[type="text"], textarea').each(function(index ,elem){
			var elem_value = $(elem).val().replace(/\r?\n/g, '<br>');
			$(elem).after(elem_value);
			$(elem).remove();
		});

		// ファイルファップロード　ファイル選択ボタン批評j
		$('input[type="file"]').each(function(index, elem){
			$(elem).parent().remove();
		});

		// セレクトボックス
		$('select').each(function(index, elem){
			if(elem.name!==''){	// TODO name属性が設定されていないとエラーで止まる
				var elem_text = $('[name='+elem.name+'] option:selected').text();
				$(elem).after(elem_text);
				$(elem).remove();
			}
		});
		// ラジオボタン、チェックボックス
		// アップロードファイル削除チェックボックスは除く
		$('input[type="radio"], input[type="checkbox"]').not("[id$=_file_del]").each(function(index , elem){
			if($(elem).is( ':checked')){
				var elem_text = $('input[name="'+elem.name+'"]:checked').parent().text();
				$(elem).parent().after(elem_text);
			}
			$(elem).parent('label').remove();
			$(elem).remove();
		});
		// アップロードファイル削除チェックボックス
		$("[id$=_file_del]").each(function(index, elem){
			if($(elem).is( ':checked')){
				$(elem).parent().after(' <span class="attention">削除</span>');
			}
			$(elem).parent('label').remove();
			$(elem).remove();
		});

		/////////////////////////////////
		// 戻るボタン、サブミットボタン以外は非表示にする TODO 戻るボタン、登録ボタン以外。この二つで大丈夫？
		$(":button:not('#btn_back, #btn_save')").css('display', 'none');

		/////////////////////////////////
		// 注釈は消す
		$('.caution').remove();
		$('.notice').remove();
	});
	return false;
}
/*--------------------------------------------------
 * DB登録する
 * actino=save
 --------------------------------------------------*/
function saveForm(list_url, message){
	$('#btn_save').prop("disabled", true);	// 保存ボタンをdisabledに。

	// save
	$.ajax({
		url: getUrl() + '?action=save',
		type: 'POST',
		data: $('form').serialize()
	}).done(function(j_data){
		//alert(j_data);
		// 内容をチェック。エラーがあったらアラート表示して処理を終了する
		checkAjaxData(j_data);

		// メッセージあったら表示
		if(typeof(message) !== "undefined"){
			showDialog(message);
		}
		// 一覧へ移動
		if(typeof(list_url) !== "undefined" && list_url!==''){
			location.href = list_url;
		}

	}).fail(function(jqXHR, textStatus, errorThrown){
		alert("fail");
	});
	return false;
}

/*--------------------------------------------------
 * 入力内容をチェックしてDB登録をする
 * action=check
 --------------------------------------------------*/
function checkAndSaveForm(list_url, msg=''){
	// check
	$.ajax({
		url: getUrl() + '?action=checksave',
		type: 'POST',
		data: $('form').serialize()
	}).done(function(j_data){
		//console.log('j_data', j_data);
		/////////////////////////////////
		// エラーメッセージを初期化
		$('.error').each(function(index , elem){
			$(elem).remove();
		});
		//$('input').css("background-color", "");
		/////////////////////////////////
		// jsonデータをパースする
		// エラーがあったら、メッセージ表示して終了。
		/////////////////////////////////
		if(j_data !== '') {
			// エラー情報をパースする
			var json_parse = parseJson(j_data);

			// loop
			var i = 0;
			for (var key in json_parse) {
				var elem = $('[name='+key+']');
				var err_msg = ' <span class="error">'+json_parse[key]+'</span>';

				/////////////////////////////////
				// タグの背景をピンクにして、隣にエラーメッセージ表示
				/////////////////////////////////
				// ラジオボックス
				if(elem.prop('type')==='radio'){
					elem.parent().parent().find('.radio_group').after(err_msg);	// エラー表示
				// セレクトボックス
				}else if(elem.prop('type')==='select-one'){
					elem.after(err_msg);						// エラー表示
				// 記号、注釈あり
				}else if(elem.next().prop('class')==='unit'){
					elem.next().after(err_msg);					// エラー表示
				}else{
					elem.after(err_msg);						// エラー表示
				}
				/////////////////////////////////
				// 一番初めのエラーのあるタグへスクロール
				if(i === 0){
					var position = 0;
					if($('[name="'+key+'"]').offset()){
						position = $('[name="'+key+'"]').offset().top;
					}

					console.log('key_position', i+'/'+key+'/'+position);
					$('html, body').animate({
						scrollTop : (position-10)
					}, {
						queue : false
					});
					// $('[name='+key+']').focus(); // フォーカスすると、clearBg($(this))とかぶるのでフォーカスさせない
				}
				i++;
			}

			// エラーメッセージ表示
			$('#err_msg').html('エラーがあります。確認してください');
			return false;
		}

		/////////////////////////////////
		// OKだった
		/////////////////////////////////
		$.ajax({
			url: getUrl() + '?action=save',
			type: 'POST',
			data: $('form').serialize()
		}).done(function(j_data){
			// 内容をチェック。エラーがあったらアラート表示して処理を終了する
			checkAjaxData(j_data);

			// 一覧へ移動
			if(typeof(list_url) !== "undefined" && list_url!==''){
				location.href = list_url;
			} else if(msg!=''){
				showNotice(msg);
			}
		});
	});
	return false;
}

/*--------------------------------------------------
 * 「戻る」ボタン
 * 確認画面から編集画面へ移動
 * mode=back
 --------------------------------------------------*/
function backForm(){
	var url = location.protocol + "//" + location.host + location.pathname ;
	if(location.search !== ''){
		// queryあり
		var search = location.search.replace('&mode=back', '');
		url = url + search + '&mode=back';
	} else {
		url = url + '?mode=back';
	}
	location.href = url;
}
/*--------------------------------------------------
 * 要素をクリックしたらエラーメッセージを除去して背景を元に戻す
 --------------------------------------------------*/
function clearBg(elem){
	//console.log(elem.attr('name'));
	if(elem.attr('type')==='radio'){
		// radio
		elem.parent().parent().css("background-color", "");			// 背景
		elem.parent().parent().parent().find('.error').remove();	// エラーメッセージ

	} else if(elem.attr('type')==='file'){
		// file
		elem.parent().css("background-color", "#5186b7");			// 背景
		elem.parent().parent().find('.error').remove();				// エラーメッセージ

	} else {
		// radio, file以外の通常タグ
		elem.css("background-color", "");
		elem.parent().find('.error').remove();
	}
	$('#err_msg').html('');
}
/*--------------------------------------------------
 * ファイルのインポート
 --------------------------------------------------*/
function importFile(tag_name, form_name, url){
	if($('#'+tag_name).val().length === 0) {
		showNotice('ファイルを選択してください');
		return;
	}
	if(!confirm('ファイルをインポート・更新してよろしいですか？')) return;

	// フォームデータを取得
	var fd = new FormData($('#'+form_name).get(0));
	showLoadingImg();
	// POSTでアップロード
	$.ajax({
		url  : url,
		type : "POST",
		data : fd,
		cache       : false,
		contentType : false,
		processData : false,
		dataType    : "html"
	})
	.done(function(error_msg, textStatus, jqXHR){
		if(error_msg !== ''){
			/* エラーあり！ */
			//showNotice(error_msg);
			showDialog(error_msg);
			hideLoadingImg();
		} else {
			/* 処理に成功！ */
			// loadingイメージ除去
			hideLoadingImg();
			showNotice('処理が完了しました');
			// reload
			location.reload();
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown){
		alert("fail");
		hideLoadingImg();
	});
}
/*--------------------------------------------------
 * ページを移動するときに編集中の内容があったら警告を出す
 --------------------------------------------------*/
function observeForm(){
	// ページを移動するときに警告を出す
	$('form').change(function() {
		$(window).on('beforeunload', function() {
			return '投稿が完了していません。このまま移動しますか？';
		});
	});
	// 保存、チェック、戻るボタンだけは監視から外す
	$('#btn_check, #btn_save, #btn_back, #btn_copy, [id^=btn_update_]').click(function() {
		$(window).off('beforeunload');
	});
}
/*--------------------------------------------------
 * アップロード前に画像をプレビューする
 --------------------------------------------------*/
function preview(elem, name, width, height) {
	if (!elem.files.length) return;  // ファイル未選択

	var file = elem.files[0];
	if (!/^image\/(png|jpeg|gif)$/.test(file.type)) return;  // typeプロパティでMIMEタイプを参照

	var img = document.createElement('img');
	var fr = new FileReader();
	fr.onload = function() {
		img.src = fr.result;  // 読み込んだ画像データをsrcにセット
		img.naturalWidth = "100";
		img.naturalHeight = "100";
		document.getElementById('preview_field' + name).appendChild(img);
	};
	fr.readAsDataURL(file);  // 画像読み込み
}
/*--------------------------------------------------
 * エラーを表示する(ユーザ側)
 --------------------------------------------------*/
function parseErrData(j_data){
	// エラーを初期化
	$('input, select, textarea').each(function(index , elem){
		$(elem).next('.error').remove();
	});
	// jsonデータをパースする
	var json_parse = parseJson(j_data);
	// loop
	for (var key in json_parse) {
		//$('[name='+key+']').css('background-color', '#f7cfcf');	// 背景色
		$('[name='+key+']').after(' <span class="error" id="test">'+json_parse[key]+'</span>');	// エラー表示
	}
	
}



function displayError(j_data){
	// エラーメッセージ表示
	$('#btn_check').before('<span class="error">エラーがあります。確認してください<br></span>');

	// エラー情報をパースする
	var json_parse = parseJson(j_data);
console.log(json_parse);
	// loop
	var i = 0;
	for (var key in json_parse) {
		var elem = $('[name="'+key+'"]');
		var err_msg = ' <span class="error">'+json_parse[key]+'</span>';

		/////////////////////////////////
		// タグの背景をピンクにして、隣にエラーメッセージ表示
		/////////////////////////////////
		// ラジオボックス
		if(elem.attr('type')==='radio'){
			elem.parent().parent().find('.radio_group').css('background-color', '#f7cfcf');	// 背景色
			elem.parent().parent().find('.radio_group').after(err_msg);	// エラー表示
		// 記号、注釈あり
		} else if(elem.next().attr('class')==='unit'){
			elem.css('background-color', '#f7cfcf');				// 背景色
			elem.next().after(err_msg);								// エラー表示
		// file
		} else if(elem.attr('type')==='file'){
			//elem.parent().css('background-color', '#f7cfcf');		// 背景色
			elem.parent().parent().find('label').after(err_msg);	// エラー表示		TODOTODO labelどこへ？
		// 通常のタグ
		} else {
			elem.css('background-color', '#f7cfcf');				// 背景色
			elem.after(err_msg);									// エラー表示
		}

		/////////////////////////////////
		// 一番初めのエラーのあるタグへスクロール
		if(i === 0){
			var position = $('[name="'+key+'"]').offset().top;
			//console.log(i, key+'/'+position);
			$('html, body').animate({
				scrollTop : (position-10)
			}, {
				queue : false
			});
			// $('[name='+key+']').focus(); // フォーカスすると、clearBg($(this))とかぶるのでフォーカスさせない
		}
		i++;
	}
}

/*
 * 従業員サジェスト
 */
function employeeSuggest(name, number){
	var url = getUrl() + '?action=get_suggest_employee';
	$('[name=' + name + ']').autocomplete({
		source: url,
		delay: 0,
		minLength: 0,
		change: function(){
			// 候補から選択されたら番号代入
			$('[name=' + number + ']').val('');	// 初期化
			if ($(this).val().indexOf(":") != -1) {
				var tmp = $(this).val().split(":");
				$(this).val(tmp[1]);
				$('[name=' + number + ']').val(tmp[0]);
			}
			var url = getUrl() + '?action=get_contract&number=' + tmp[0];

			// 一覧セット
			$.post(url, function(j_data){
				// 内容をチェック。エラーがあったらアラート表示して処理を終了する
				checkAjaxData(j_data);

				// JSONデータパース
				var parse_data = JSON.parse(j_data);
				// 一覧セット
				$('#contract').html(parse_data);
			});
		}
	}).focus(function () {
		$(this).autocomplete("search", "");
	});
}
/*
 * ファイルサイズのチェック。
 * PHP側で制御できなかったので、サイズオーバーだけJSで処理する。
 */
function checkUploadSize(){
	var upload_ok_flg = true;

	$('input[type="file"]').each(function(index, elem){
		if($(elem)[0].files[0]){
			// 選択されたファイル名
			var file_name = $(elem)[0].files[0].name;
			var lg = $(elem)[0].files.length;
			var items = $(elem)[0].files;
			if (lg > 0) {
				for (var i = 0; i < lg; i++) {
					//console.log(file_name+':'+items[i].size);
					if(items[i].size>10485760){	// 10M
						upload_ok_flg = false;
						// エラーメッセージ表示
						$(elem).parent().parent().find('.error').remove();
						$(elem).parent().parent().find('[name=selected_file]').after('<div class="error">アップロードできるファイルサイズは10MBまでです。</div>');
					}
				}
			}
			// 確認画面へボタン。エラーがある場合はdisabled
			if(!upload_ok_flg){
				$('#btn_check').attr("disabled", true);
				$('#btn_check').css("background-color", "gray");
				$('#btn_check').css("border", "gray");
			} else {
				$('#btn_check').attr("disabled", false);
				$('#btn_check').css("background-color", "#FF9014");
				$('#btn_check').css("border", "#FF9014");
			}
		}
	});
}