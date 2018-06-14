<?php
/* -----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 * -----------------------------------------------------------------------------
 * 	2006-07-24 : initial version
 * 	2009-10-31 : rebuild
 *  2011-05-10 : rebuild by hide CODE"$actg   = $daom->tcd_getAcademicSchoolType();
 *  2015-05-12 : rebuild by hide
 *
 *  html helper
 * ---------------------------------------------------------------------------- */
	/**
	 * date
	 */
	function makeDate($name, $value, $datepicker = true, $option_arr = array()){
		// datePicker
		$dp = '';
		$name_tmp = str_replace(array('[', ']'), array("\\\[", "\\\]"), $name);
		if ($datepicker) {
			$dp = "onclick=\"\$(function(){
			\$('#{$name_tmp}').datepicker();
			\$('#{$name_tmp}').datepicker('option', 'dateFormat', 'yy/mm/dd');});
			$('#{$name_tmp}').datepicker('show');\"";
		}

		if (empty($value)) {
			$value = '';
		} elseif (is_numeric($value)) {
			$value = sprintf('%04d/%02d/%02d', substr($value, 0, 4), substr($value, 4, 2), substr($value, -2));
		}
		$option_str = '';
		if (!empty($option_arr)) {
			$option_str = implode(' ', $option_arr);
		}

		return "<input type='text' id='{$name}' name='{$name}' size='12' maxlength='10' 
			 placeholder='2000/01/01' value='".h($value)."'  $dp $option_str>";
	}
	/*
	 * ZIP_CODE
	 */
	function makeZip($name, $value, $address_name){
		return '<input type="text" name="'.$name.'" value="'.$value.'" style="width:80px;" maxlength="8" '
			. 'onkeyup="AjaxZip3.zip2addr(this,\'\',\''.$address_name.'\',\''.$address_name.'\');">';
	}

	/**
	 * text
	 */
	function makeText($name, $value, $size = 100, $maxlen = '', $placeholder = '', $option_arr = array()){
		$size = 'size="'.$size.'"';
		$smax = '';
		if ($maxlen != '') {
			$smax = " maxlength='".$maxlen."' ";
		}

		$option_str = '';
		if (!empty($option_arr)) {
			foreach($option_arr as $key=>$val){
				$option_str .= $key.'="'.$val.'"';
			}
		}
		return "<input id='{$name}' name='{$name}' value='".h($value)."' type='text' $size placeholder='$placeholder' $smax $option_str> ";
	}
	/**
	 * 勤怠用
	 */
	function makeActivityText($name, $day, $value='', $lock = false, $size = 20, $maxlen = 5, $placeholder = '', $option_arr = array()){
		$readonly_empty = $lock == true ? 'readonly' : '';
		if($name == "EMPTY"){
            return "
                <td class='input-field'><input id='PROCEDURE_ATTENDANCE_TIME$day' name='cell[$day][PROCEDURE_ATTENDANCE_TIME]' value='' type='text' size='20' placeholder=''  maxlength='5'  $readonly_empty onchange='activityInput($day)'> </td>
                <td class='input-field'><input id='PROCEDURE_LEAVING_OFFICE_TIME$day' name='cell[$day][PROCEDURE_LEAVING_OFFICE_TIME]' value='' type='text' size='20' placeholder=''  maxlength='5'  $readonly_empty onchange='activityInput($day)'> </td>
                <td class='input-field'><input id='APPOINTED_OFFICE_HOURS$day' name='cell[$day][APPOINTED_OFFICE_HOURS]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='APPOINTED_OUTSIDE_OFFICE_HOURS$day' name='cell[$day][APPOINTED_OUTSIDE_OFFICE_HOURS]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='LATE_NIGHT_OFFICE_HOURS$day' name='cell[$day][LATE_NIGHT_OFFICE_HOURS]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='APPOINTED_BREAK$day' name='cell[$day][APPOINTED_BREAK]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='APPOINTED_OUTSIDE_BREAK$day' name='cell[$day][APPOINTED_OUTSIDE_BREAK]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='LATE_NIGHT_BREAK$day' name='cell[$day][LATE_NIGHT_BREAK]' value='' type='text' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='nodisp'><input id='INDIRECTLY_01$day' name='cell[$day][INDIRECTLY_01]' value='' type='hidden' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='nodisp'><input id='INDIRECTLY_02$day' name='cell[$day][INDIRECTLY_02]' value='' type='hidden' size='20' placeholder=''  maxlength='5'   $readonly_empty> </td>
                <td class='input-field'><input id='BEING_LATE_TIME$day' name='cell[$day][BEING_LATE_TIME]' value='' type='text' size='20' placeholder=''  maxlength='5' readonly> </td>";
        } 
        
        $size = 'size="'.$size.'"';
		$smax = '';
		if ($maxlen != '') {
			$smax = " maxlength='".$maxlen."' ";
		}

		$option_str = '';
		if (!empty($option_arr)) {
			foreach($option_arr as $key=>$val){
				$option_str .= $key.'="'.$val.'"';
			}
		}
        
        $onchange = '';
        $type = 'text';
        //$readonly = 'readonly';
		$readonly = $lock == true ? 'readonly' : '';
        if($name == "PROCEDURE_ATTENDANCE_TIME" || $name == "PROCEDURE_LEAVING_OFFICE_TIME")
        {
            $onchange = "onchange='activityInput($day)'";
            if(strlen($value)==4){
                $tmp = substr($value,0,2).':'.substr($value,2,2);
                $value = $tmp;
            }
        }
		if($name == "CLASSIFICATION" || $name == "BEING_LATE_TIME"){
			$readonly = 'readonly';
		}
        
        return "<input id='{$name}{$day}' name='cell[{$day}][{$name}]' value='".h($value)."' type='$type' $size placeholder='$placeholder' $smax $option_str $onchange $readonly> ";
	}
    
	/**
	 * hidden
	 */
	function makeHidden($name, $value){
		return "<input type='hidden' name='{$name}' id='{$name}' value='".h($value)."' >";
	}
	/**
	 * readonly
	 */
	function makeReadonly($name, $value){
		$html = "<input type='hidden' name='{$name}' id='{$name}' value='".h($value)."'>";
		$html .= h($value);
		return $html;
	}
	/**
	 * password
	 */
	function makePassword($name, $value){
		return "<input type='password' name='{$name}' id='{$name}' value='".h($value)."' >";
	}
	/**
	 * textarea
	 */
	function makeTextarea($name, $value, $width = 100, $height = 100, $option_arr = array()){
		$option_str = '';
		if (!empty($option_arr)) {
			$option_str = implode(' ', $option_arr);
		}
		return "<textarea name=\"{$name}\" id=\"{$name}\" style=\"width:{$width}px; height:{$height}px;\" $option_str>".h($value).'</textarea>';
	}
	/**
	 * checkbox
	 */
	function makeCheckbox($name, $select_value, $check_value=1, $str=''){
		$checked = ($check_value === $select_value) ? 'checked' : '';
		// チェックボックス オフの時の対策でhiddenで渡す
		$html = "<input type='hidden' name='{$name}' value=''>";
		$html .= "<label><input type='checkbox' name='{$name}' id='{$name}' value='".h($check_value)."' $checked>{$str}</label>";

		return $html;
	}
	/**
	 * checkbox array
	 * TODO
	 */
	function makeCheckboxArray($name, $select_value, $list, $option_arr = array()){
		$option_str = '';
		if (!empty($option_arr)) {
			$option_str = implode(' ', $option_arr);
		}
		$html = '';
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$name_str = $name.'['.$key.']"';
				$checked = (is_array($select_value) && in_array($key, $select_value)) ? 'CHECKED' : ''; // 選択

				$html .= '<input type="hidden"  name="'.$name_str.'" value="">';
				$html .= "<label $option_str >";
				$html .= '<input type="checkbox" name="'.$name_str.' value="'.$key.'" '.$checked.' '.$option_str.'>';
				$html .= h($val).'</label>'. "\n";
			}
		}
		return $html;
	}
	/**
	 * checkbox unk
	 * 表示するチェックボックスの最後に未入力を追加
	 */
	function makeCheckboxUnk($name, $select_values, $list, $option_arr = array()){
		$list['unk'] = '未入力';
		$option_str = '';
		if (!empty($option_arr)) {
			$option_str = implode(' ', $option_arr);
		}

		$select_arr = array();
		if ($select_values != '' && !is_array($select_values)) {
			$select_arr = explode(',', $select_values);
		} elseif (is_array($select_values)) {
			$select_arr = $select_values;
		}

		$html = '';
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$checked = array_key_exists($key, $select_arr) ? "checked='checked'" : ''; // 選択

				$html .= "<label class=\"label-check\" $option_str >";
				$html .= "<input type=\"checkbox\" name=\"{$name}[{$key}]\" value=\"{$key}\" {$checked} {$option_str} />";
				$html .= h($val)."</label> \n";
			}
		}
		return $html;
	}
	/**
	 * radio
	 */
	function makeRadio($name, $select, $list, $options = array()){
		$option_str = (!empty($options)) ? implode(' ', $options) : null;

		$html = '';
		if (!empty($list)) {
			$html = '<span class="radio_group" style="padding:5px;">';
			foreach ($list as $key => $val) {
				$id = $name.$key;
				$checked = ($select != '' && strcmp($key, $select) == 0) ? "checked='checked'" : ''; // 選択

				$html .= '<label>';
				$html .= "<input type='radio' name='{$name}' id='{$id}' value='{$key}' {$checked} {$option_str}>";
				$html .= $val."</label>\n";
			}
			$html .= '</span>';
			// TODO 未選択時対応
			if (strcmp($select, '') == 0) {
				$html .= "<input type='radio' name='{$name}' id='{$id}' value='' checked='checked' style='display:none;'>";
			}
		}
		return $html;
	}
	/**
	 * select
	 */
	function makeSelect($name, $select, $list, $incrude_empty = true, $options = array(), $top_str = '---'){
		// option
		$option_str = (!empty($options)) ? implode(' ', $options) : null;

		$html = "<select name='{$name}' id='{$name}' $option_str>";
		if ($incrude_empty) {
			$html .= "<option value=''>{$top_str}</option>";
		}

		if (!empty($list)) {
			foreach ($list as $k => $v) {
				//if ($select == $code)
				if (strcmp($select, $k) == 0) {
					$html .= "<option value='$k' selected>".h($v).'</option>';
				} else {
					$html .= "<option value='$k' >".h($v).'</option>';
				}
			}
		}
		$html .= '</select>';
		return $html;
	}
	/**
	 * file
	 * options(width, height, view_width, view_height)
	 */
	function makeFile($name, $value, $del_flg_value=null){
		// file
		$html = "<label for='{$name}' id='{$name}_label' class='btn file'>ファイルを選択";
		$html .= "<input type='file' name='{$name}' id='{$name}'></label><br>";
		// 既存データあり。
		if (!empty($value)){
			// リンク付き
			$html .= "<a href='/file.html?file=".urlencode($value)."' id='{$name}_link' target='_blank'>".$value.'</a>';
			// hidden
			$html .= "<input type='hidden' name='{$name}' id='{$name}' value='".h($value)."'>";
			// 削除チェックボックス表示
			if(!empty($del_flg_value)){
				$del_check = ($del_flg_value==1) ? "checked" : "";
				$html .= "<label for='{$name}_file_del'>"
					. "<input type='checkbox' name='{$name}_file_del' id='{$name}_file_del' value='1' {$del_check}>"
					. "削除</label>";
			}
		}
		return $html;
	}
	/**
	 * 写真アップロード
	 * options(width, height, view_width, view_height)
	 */
	function makeFilePic($name, $value, $del_flg_value=null, $options=array()){
		// file
		$html = "<label for='{$name}' id='{$name}_label' class='btn file'>ファイルを選択";
		$html .= "<input type='file' name='{$name}' id='{$name}'></label><br>";
		// 既存データあり。
		if (!empty($value)) {
			// 画像/ファイル名 表示
			if(preg_match('/\.gif$|\.png$|\.jpg$|\.jpeg$|\.bmp$/i', $value)){
				$width = (!empty($options['view_width'])) ? 'width='.$options['view_width'] : '';
				$height = (!empty($options['view_height'])) ? 'height='.$options['view_height'] : '';

				$html .= "<img src=\"/file.html?file={$value}\" $width $height>";
			} else {
				// リンク付き
				$html .= "<a href='/file.html?file=".urlencode($value)."' id='{$name}_link' target='_blank'>".$value.'</a>';
			}
			// hidden
			$html .= "<input type='hidden' name='{$name}' id='{$name}' value='".h($value)."'>";
			// 削除チェックボックス表示
			$del_check = ($del_flg_value==1) ? "checked" : "";
			$html .= "<label for='{$name}_file_del'>"
				. "<input type='checkbox' name='{$name}_file_del' id='{$name}_file_del' value='1' {$del_check}>"
				. "削除</label>";
		}
		// 幅指定(convert)
		if (!empty($options['width'])) {
			$html .= "<input type='hidden' name='{$name}_width' value='{$options['width']}'>";
		}
		// 縦指定(convert)
		if (!empty($options['height'])) {
			$html .= "<input type='hidden' name='{$name}_height' value='{$options['height']}'>";
		}
		return $html;
	}

	// ドリルダウン(親コードが配列でくる)
	function makeSelectDownArray($name, $value, $p_value, $arr, $p_arr, $inc_empty = true, $echo_select_tag = true)	{
		$str = '';
		if ($echo_select_tag) {
			$str .= "<label class='select-wrap entypo-down-open-mini'>";
			$str .= "<select name='{$name}' id='{$name}'>";
		}
		if ($inc_empty) {
			$str .= "<option value=''>すべて</option>";
		}

		if (!empty($p_value)) {
			// 親コードが選択されていたら
			foreach ($p_value as $pkey) {
				if (!isset($arr[$pkey])) {
					continue;
				}
				foreach ($arr[$pkey] as $k => $v) {
					$code = $pkey.'___'.$k; // 親コード＋コード

					$selected = '';
					if (is_array($value) && in_array($code, $value)) {
						$selected = 'selected';
					} elseif ($code === $value) {
						$selected = 'selected';
					}
					$str .= "<option value=$code $selected>{$v}(".$p_arr[$pkey].')</option>';
				}
			}
		} else {
			// １つもチェックされていなかったら、全て表示
			foreach ($arr as $pkey => $val) {
				foreach ($val as $k => $v) {
					$code = $pkey.'___'.$k; // 親コード＋コード

					if (isset($p_arr[$pkey])) {
						$selected = '';
						$str .= "<option value=$code $selected>{$v}(".$p_arr[$pkey].')</option>';
					}
				}
			}
		}
		if ($echo_select_tag) {
			$str .= '</select>';
			$str .= '</label>';
		}

		return $str;
	}

	// ドリルダウン
	function makeSelectDown($name, $value, $arr, $inc_empty = true, $top_str = '---')	{
		$str = '';
		$str .= "<label class='select-wrap entypo-down-open-mini'>";
		$str .= "<select name='{$name}' id='{$name}'>";

		if ($inc_empty) {
			$str .= "<option value=''>{$top_str}</option>";
		}

		foreach ($arr as $k => $v) {
			$selected = ($k == $value) ? 'selected' : '';
			$str .= "<option value=$k $selected>".h($v).'</option>';
		}

		$str .= '</select>';
		$str .= '</label>';

		return $str;
	}
