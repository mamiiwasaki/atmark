<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version ushizawa
 *	2014-06-25 : rebuild
 *
 *	formヘルパー class
 *----------------------------------------------------------------------------*/
class form {
	var $data = array();
	var $errors = array();
	var $mode = "edit";

//	function form($tablename=null) {
	//	$this->tablename = $tablename;
	//	$this->getSearchData($tablename);
//	}
	

	/**
	 * フォーム要素を生成する
	 * @param string $tag_type
	 * @param string $name
	 * @param array $options
	 * @param array $list
	 * @param array $conf("select_space", "image_width", "image_height") など
	 * @return string
	 */
	function getFormTag($tag_type, $name="", $options=array(), $list=array(), $conf=array()) {
		$tag = "";
		$confirm_flg = ($this->mode=="confirm") ? true : false;

		// name_str
		$name_str = "name=\"".$name."\"";
		// id_str
		//$id_str = "id=\"".$name."\"";
		$id_str = "id=\"".str_replace("[]","",$name)."\"";	// select multiple対応
		// value_str
		if(!isset($this->data[$name])){
			$value_str = "";
		} else {
			if(!is_array($this->data[$name])){
				$value_str = "value=\"".htmlspecialchars($this->data[$name])."\"";
			} else {
				$value_str = "value=\"".$this->data[$name]."\"";
			}
		}


		///////////////////////////////////
		// オプション設定
		///////////////////////////////////
		$option_str = "";
		// エラーがあったら、エレメントの背景に色をつける
		if(isset($this->errors[$name])) {
			$options[] = "style=\"background-color:#FFE7DF;\"";
		}

		// 確認画面では読み取り専用
		if($this->mode == "confirm") {
			$options[] = "READONLY";
		}
		// options string
		if(!empty($this->data[$name])){
			// value 上書き
			if(!empty($options)){
				foreach($options as $key=>$val){
					if(substr($val,0,6)=="value=") unset($options[$key]);
				}
			}
			// $options[] = "value=\"".$this->data[$name]."\"";
		}
		$options_str = "";
		if(!empty($options)) {
			$options_str = implode($options, " ");		
		}
		
		//// add 20140801 $optionsに「vaue=」があって、初回の表示の場合		
		if(empty($_POST) && !empty($options) && empty($this->data[$name]) && $tag_type!="select" && $tag_type!="radio" && $tag_type!="checkbox"){
			foreach($options as $val){
				if(substr($val,0,6)=="value=") $value_str = $val;
			}
		}
			
		// type別
		switch($tag_type) {
			#-------------------------------------------------------------------------
			#	password, hidden
			#-------------------------------------------------------------------------
			case "hidden":			
				$tag = "<input type=\"".$tag_type."\" $name_str $id_str $value_str>\n";
				break;
				
			#-------------------------------------------------------------------------
			#	button, submit
			#-------------------------------------------------------------------------
			case "button":
			case "submit":
				// valueはオプションで。
				$tag = "<input type=\"".$tag_type."\" $name_str $id_str $options_str>\n";
				break;

			#-------------------------------------------------------------------------
			#	text
			#-------------------------------------------------------------------------
			case "text":
			case "date":
				if(!$confirm_flg) {
					//  autocomplete=\"off\"
					$tag = "<input type=\"".$tag_type."\" $name_str $id_str $value_str $options_str>\n";
				} else {
					// readonly
					$tmp_value = (!empty($this->data[$name])) ? htmlspecialchars($this->data[$name]) : "";
						
					$tag = "<input type=\"hidden\" $name_str $id_str $value_str $options_str>\n";
					$tag .= "<span style='line-height:16px;'>".$tmp_value."</span>\n";
				}
				break;
				
			#-------------------------------------------------------------------------
			#	password
			#-------------------------------------------------------------------------
			case "password":
					if(!$confirm_flg) {
						$tag = "<input type=\"".$tag_type."\" $name_str $id_str $value_str $options_str autocomplete=\"off\">\n";
					} else {
						// readonly
						$tag = "<input type=\"hidden\" $name_str $id_str $value_str $options_str>\n";
						$tag .= "<span style='line-height:16px;'>".str_repeat("*", strlen($this->data[$name]))."</span>\n";
					}
					break;

			#-------------------------------------------------------------------------
			#	textarea
			#-------------------------------------------------------------------------
			case "textarea":
	
				if(!$confirm_flg) {
				if(!empty($this->data[$name])){
						$tag = "<textarea $name_str $id_str $options_str>".$this->data[$name]."</textarea>";
					} else {
						$tag = "<textarea $name_str $id_str $options_str></textarea>";
					}
				} else {
					// readonly
					$tag .= "<input type=\"hidden\" $name_str $id_str $value_str $options_str>\n";
					$tag .= "<div style='width:100%;height:100%;border:solid 1px #999999;overflow-y:scroll;border:none;'>";
					$tag .= str_replace("\n","<br>",htmlspecialchars($this->data[$name]))."</div>\n";
				}
				break;
	
			#-------------------------------------------------------------------------
			#	readonly
			#-------------------------------------------------------------------------
			case "readonly":

				$tag .= "<input type=\"hidden\" $name_str $id_str $value_str $options_str>\n";
				if(isset($this->data[$name]))
				 $tag .= "<span id='".$name."_readonly'>".$this->data[$name]."</span>";
				break;
	
			#-------------------------------------------------------------------------
			#	select
			#-------------------------------------------------------------------------
			case "select":
//echoblue($name."---".$this->data[$name]);
//pr3($conf);
				if(!$confirm_flg) {
					$tag = "<select $name_str $id_str $options_str>\n";
					if(!empty($list)) {
						if(empty($conf["select_space"]) || $conf["select_space"]!=1) {
							$tag .= "<OPTION value=\"\" SELECTED>---</OPTION>\n";
						}
						foreach($list as $key => $val) {
							// 選択
							if(isset($this->data[$name]) && (strcmp($key, $this->data[$name])==0)){
								$selected = "SELECTED";
							} else {
								$selected = "";
							}										
							$tag .= "<OPTION value=\"$key\" $selected>$val</OPTION>\n";
						}
					}
					$tag .= "</select>\n";

				} else {
					/******
					 * multiple対策！([]の配列で値が渡される）
					 */
					$name2 = str_replace("[]", "", $name);
					if(isset($this->data[$name2]) && is_array($this->data[$name2])){
						foreach($this->data[$name2] as $v){
							$arr[] = $list[$v];
						}
						$confirm_view = (implode("<br>", $arr));
						// multipleは配列なので、カンマ区切りでデータを渡す！
						$value_str = "value=\"". implode(",", $this->data[$name2]) ."\"";
						
					} else {
						$confirm_view = (!empty($this->data[$name])) ? $list[$this->data[$name]] : "";
						$value_str = (isset($this->data[$name])) ? "value=\"". $this->data[$name2] ."\"" : "";
					}
				
					// readonly
					//$tag .= "<input type=\"text\" $name_str $id_str $value_str $options_str>";	//////option_strいるか？？？？！！
					$tag .= "<input type=\"hidden\" $name_str $id_str $value_str>";	//////option_strいるか？？？？！！
					$tag .= "<span style='line-height:20px;'>{$confirm_view}</span>\n";					
				}
				
				break;
	
			#-------------------------------------------------------------------------
			#	radio
			#-------------------------------------------------------------------------
			case "radio":
	
				// エラーだったら背景に色をつける
				$error_bg = (!empty($this->errors[$name])) ? "style='background-color:#FFE7DF;'" : "";
				$tag = "<div $error_bg>";

				if(!$confirm_flg) {
					if(!empty($list)){						
						foreach($list as $key=>$val) {
							$id = $name.$key;
							$checked = (isset($this->data[$name]) && strcmp($key, $this->data[$name])==0)? "CHECKED": "";	// 選択
				
							$tag.= "<input type=\"radio\" $name_str id=\"{$id}\" value=\"{$key}\" $options_str $checked/>";
							$tag.= "<label for=\"{$id}\"> {$val}</label>　\n";
						}
					}
				} else {
					// readonly					
					if(isset($this->data[$name])) $tag.= $list[$this->data[$name]];
					$tag.= "<input type=\"hidden\" $name_str $id_str $value_str $options_str>\n";
				}
				$tag.="</div>";
				break;
	
			#-------------------------------------------------------------------------
			#	checkbox
			#-------------------------------------------------------------------------
			case "checkbox":
				
				if(empty($list)) return "";
				$tag = "";
	
				// ★編集画面--------------------------------
				if(!$confirm_flg) {

					// エラーだったら背景に色をつける
					$error_bg = (!empty($this->errors[$name])) ? "style='background-color:#FFE7DF;'" : "";
					if(count($list)>1) $tag = "<div $error_bg>";

					foreach($list as $key => $val) {
						$name_str = "name=\"".$name."[]\"";
						$id_str = "id=\"".$name.$key."\"";
						// checked
						$checked= "";
						if(!empty($this->data[$name])){
							// カンマ区切りの場合配列にする
							if(!is_array($this->data[$name])) 								
								$this->data[$name] = explode(",", $this->data[$name]);
							// checked
							if(in_array($key, $this->data[$name]))
								$checked = "checked";
						}
				
						$tag.= "<input type=\"checkbox\" $name_str $id_str value=\"{$key}\" $options_str  $checked/>";
						$tag.= "<label for=\"".$name.$key."\">".$val."</label> \n";						
					}
					if(count($list)>1) $tag.= "</div>\n";

				// ★確認画面 --------------------------------
				} else {

					$value_str = "";
					if(!empty($this->data[$name])) {
						/*
						if(!is_array($this->data[$name])) {
							// 一つもチェックされていない
							$value_str = "value=\"\"";
						} else {
							foreach($this->data[$name] as $val) $keys[] = $list[$val];
							$tag.= implode(",　", $keys);
							$value_str = "value=\"". implode(",", $this->data[$name]) ."\"";							
						}																
						*/
						
						// チェックボックスは配列で値が入ってくるので、カンマ区切りで文字列に変換する
						if(is_array($this->data[$name])) {
							foreach($this->data[$name] as $val) $keys[] = $list[$val];
							$view_str = implode(",　", $keys);
							$tag .= $view_str;
							if(empty($view_str)) $tag .= "<img src='./images/common/check.png'>";
							
							$value_str = "value=\"". implode(",", $this->data[$name]) ."\"";

						}
					}
					$tag .= "<input type=\"hidden\" $name_str $id_str $value_str />\n";	
									
				}
				break;
			
			#-------------------------------------------------------------------------
			#	file
			#-------------------------------------------------------------------------
			case "file":
				// アップロードディレクトリ
				$upload_dir = UPLOAD_DIR."save_image/";
				$tmp_dir = UPLOAD_DIR."tmp/";
				$img_src = "./upload/save_image/";
				$tmp_src = "./upload/tmp/";

				$value = "";
				$value_tmp = "";
				$value_del = "";
		//pr3($_FILES);		

				// $_FILESに値が入っていたら、実行してしまう。
				// validatorでエラーになりconfirm画面に遷移しない場合、空になってしまうから。
				if(!empty($_FILES[$name]['name'])) {
					$tmp_name  = $_FILES[$name]['tmp_name'];
					$ext = strrchr($_FILES[$name]['name'], "."); // 拡張子
					//$file_tmp = str_replace(" ","",$name."_".time().$ext);	// ★name + time + 拡張子
					$filename = str_replace(" ","",$name."_".time().$ext);	// ★name + time + 拡張子

					$this->data[$name."_tmp"] = $filename;
				
					////////////////////////////////////////////////////
					// ファイルの移動　tmpディレクトリに格納する
					////////////////////////////////////////////////////
					if(!file_exists($tmp_dir.$filename)){
						if( ! move_uploaded_file($tmp_name, $tmp_dir.$filename )) {
							die("ファイルの移動に失敗しました<br>formのenctypeは\"multipart/form-data\"になってる？<br>$tmp_name -----> ".UPLOAD_DIR."tmp/".$filename);
						}
									
						////////////////////////////////////////////////////
						// 幅、高さの指定があったらconvert!!
						////////////////////////////////////////////////////
						if(!empty($this->data[$name."_width"]) || !empty($this->data[$name."_height"])) {
							require_once FUNCTIONS_DIR.'gdthumb.php';
							// リサイズ画像の出力
							gdthumb($tmp_dir.$filename, $this->data[$name."_width"], $this->data[$name."_height"], $tmp_dir.$filename, false);
								
							/*
							////////////////////////////////////////////////////
							// 幅、高さの指定があったらconvert!!
							////////////////////////////////////////////////////
							if(!empty($this->data[$name."_width"]) || !empty($this->data[$name."_height"])) {
								require_once FUNCTIONS_DIR.'gdthumb.php';
								// リサイズ画像の出力
								gdthumb($tmp_dir.$file_tmp, $this->data[$name."_width"], $this->data[$name."_height"], $tmp_dir.$file_tmp, false);
							
								convert入っていない
								$scale = $this->data[$name."_width"]."x".$this->data[$name."_height"];
								exec("/usr/bin/convert ".$tmp_dir.$file_tmp." -resize {$scale} ".$tmp_dir.$file_tmp, $ret, $out);
								//echo("/usr/bin/convert ".$tmp_dir.$file_tmp." -resize {$scale} ".$tmp_dir.$file_tmp);
								if($ret==1)	{
								die( $out);
							}
						//	} // if convert
						 * */
				
						} // if convert
						
						
						
						
					} // !file_exists

					$this->data[$name."_tmp"] = $filename;
				} // !empty file
				else if(!empty($this->data[$name])){
					$this->data[$name."_tmp"] = $this->data[$name];
				}

				if(!empty($this->data[$name."_tmp"])){
					$value_str = "value=\"".$this->data[$name."_tmp"]."\"";
					$value_tmp_str = "value=\"".$this->data[$name."_tmp"]."\"";
				} else {
					$value_str = "value=''";
					$value_tmp_str = "value=''";
				}
				

				
				// ★編集画面 ///////////////////////////////////////
				if(!$confirm_flg) {
					$tag = "<input type=\"file\" $name_str $id_str>\n";						// file
					$tag.= "<input type=\"hidden\" name=\"{$name}_tmp\" $value_tmp_str>\n";	// tmp ////////////////////

					// 削除チェックボックス
					if(!empty($this->data[$name])) {
						$checked = (!empty($this->data[$name."_del"]))? "CHECKED": "";						
						$tag.= "<input type=\"checkbox\" name=\"{$name}_del\" value=1 $checked> 削除\n";	
					}	
					

				// ★確認画面 ///////////////////////////////////////
				} else {

					$tag.= "<input type=\"hidden\" $name_str $id_str $value_str>\n";		// file////////////////////
					$tag.= "<input type=\"hidden\" name=\"{$name}_tmp\" $value_tmp_str>\n";	// file_tmp////////////////////
					// 削除チェックボックス
					if(!empty($this->data[$name."_del"]) && $this->data[$name."_del"]==1) {
						$tag.= "<input type=\"hidden\" name=\"{$name}_del\" value=1>\n";
						$tag.= "<img src='/images/common/check.png'> 削除";
					}				
			}
//if($name=="main_list_image")pr3($this->data);
			///////////////////////////////////////////////////
			// イメージの表示。
			if((!empty($this->data[$name]) || !empty($this->data[$name."_tmp"])) && empty($this->data[$name."_del"])) {
				$file = (!empty($this->data[$name."_tmp"])) ? $this->data[$name."_tmp"] : $this->data[$name];

				if(preg_match('/\.gif$|\.png$|\.jpg$|\.jpeg$|\.bmp$/i', $file)){
					$src = file_exists($tmp_src.$file) ? $tmp_src.$file : $img_src.$file;	// tmpかどうか
					$tag.= "<br /><img src='{$src}'>\n";
				}
			}	

			///////////////////////////////////////////////////
			// イメージの幅指定がある場合、hiddenで値を渡すためのタグ。
			// 20140812 add
			if(!empty($conf["image_width"])) $tag.= $this->getFormTag("hidden", $name."_width", array("value=".$conf["image_width"]));
			if(!empty($conf["image_height"])) $tag.= $this->getFormTag("hidden", $name."_height", array("value=".$conf["image_height"]));

		
			break;	
				
		} // switch case
		
		return $tag;
	}
	

	
	//-------------------------------------------------------------------------
	//	更新ボタン
	//-------------------------------------------------------------------------
	function submit_btn($style="", $regist_js="")	{
		// style
		if(empty($style)) $style = "font-size:18px;padding:5px;font-weight:bold;";
	//this.form.submit()
		if($this->mode == "edit")	{
			$str = $this->getFormTag("button", "", array("value='入力内容を確認する'", "onClick=\"{$regist_js}this.form.submit();\"", "class=\"btn_regist\""));
		} else {
			$str = $this->getFormTag("button", "", array("value='戻る'", "onClick=\"this.form.back_flg.value=1;this.form.submit();\"", "class='btn_regist'"))."　　";
			$str.= $this->getFormTag("button", "", array("value='この内容で登録'", "onClick=\"{$regist_js}this.form.submit();\"", "style='".$style."'", "class='btn_regist'"));
			$str.= "<div id=\"submit_msg\">この内容で登録します。よろしければ、【登録】ボタンをクリックしてください.</div>";
		}
		return $str;
	}
	

	

	
	/**
	 * 
	 * @return string
	 */
	function getSearchLimit( $search_limit ="" ){

		$value_str = "";
		if(!empty($search_limit)) $value_str = "value='".$search_limit ."'";
		
		return $this->getFormTag("select", "search_limit", array("onChange=\"this.form.submit();\"", $value_str), getLimitArr(), array("select_space"=>1))."件表示　";
	}


	/**
	 * 
	 */
	function getCsvBtn($frm_name=""){
		if(empty($frm_name)) $frm_name = "frm";
		
		$js = "$('export_flg').value=1;if($('back_flg')) $('back_flg').value=1;document.{$frm_name}.submit();$('export_flg').value=0;return false;";
		
		$tag = $this->getFormTag("hidden", "export_flg");
		$tag .= $this->getFormTag("button", "csv_btn", array("value='csvダウンロード'", "onClick=\"".$js."\""));

		return $tag;
	}
}
/*
						////////////////////////////////////////////////////
						// 幅、高さの指定があったらconvert!!
						////////////////////////////////////////////////////
						if(!empty($this->data[$name."_width"]) || !empty($this->data[$name."_height"])) {
							require_once FUNCTIONS_DIR.'gdthumb.php';
							// リサイズ画像の出力
							gdthumb($tmp_dir.$file_tmp, $this->data[$name."_width"], $this->data[$name."_height"], $tmp_dir.$file_tmp, false);
								
							convert入っていない
							$scale = $this->data[$name."_width"]."x".$this->data[$name."_height"];
							exec("/usr/bin/convert ".$tmp_dir.$file_tmp." -resize {$scale} ".$tmp_dir.$file_tmp, $ret, $out);
							//echo("/usr/bin/convert ".$tmp_dir.$file_tmp." -resize {$scale} ".$tmp_dir.$file_tmp);
							if($ret==1)	{
								die( $out);
							}						
						} // if convert
*/

?>
