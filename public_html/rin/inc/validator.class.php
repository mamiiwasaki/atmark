<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.1
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version ushizawa
 *	2014-07-01 : rebuild
 *
 *	validator class
 *----------------------------------------------------------------------------*/
define("CHARSET", "EUC-JP");

/**
 * �ե����फ�����Ϥ��줿�ǡ����Υ����å���Ԥ�
 */
class validator {
	var $data;
	var $errors = array();

	//-------------------------------------------------------------------------
	//	���顼��å������Υꥹ�Ȥ��������
	//-------------------------------------------------------------------------
	function makeErrorList() {
		// ���顼��å�����
		if(!empty($this->errors)) {
			foreach($this->errors As $key=>$message) {
				if(!empty($message)) $tmp[$key] = "<span style='cursor:pointer;' onClick=\"$('{$key}').focus();\">".$message."</span>\n";
			}
			return "<div id=\"error_list\">".implode("<br>", $tmp)."</div><br />";
	
		} else {
			return "";
		}
	}
	
	/**
	 * ���٤�POST�ǡ����򥨥������פ���
	 * @return boolean
	 */
	function escape(){
		if(empty($this->data)) return true;
		
		$pattern = "/<script.*?>|<\/script>|javascript:/i";
		$convert = '#script tag escaped#';
		
		foreach($this->data as $key => $val){
			
			if(is_array($val)) continue;
			
			if (preg_match_all($pattern, $val, $matches)) {
				$this->data[$key] = preg_replace($pattern, $convert, $val);
			}
		}
	}
	/**
	 * �ե��������ϥ����å�
	 * @param string $fieldName
	 * @param string $rules
	 * @param string $message
	 * @return boolean
	 */
	function validate($fieldName, $rules, $message) {
		
		// 2�İʾ�����å����ܤ�������
		if(!empty($this->errors[$fieldName])) return true;

		// rule, val
		$rule = $rules[0];
		$val = (isset($this->data[$fieldName])) ? $this->data[$fieldName] : "";
		if(!is_array($val)) $val = trim($val);
		
		// ���ϥ����å��ʳ��Ƕ��Ǥ����顢���롼		
		if($rule!="checkEmpty" && empty($val)) return true;
		
		// Ⱦ�ѥ����å����ä��顢���ѡ��Ⱦ�Ѥˤ������
		if($rule=="checkNum"){
			$this->data[$fieldName] = mb_convert_kana($this->data[$fieldName], "n", CHARSET);
			$val = $this->data[$fieldName];
		}

		
		switch( $rule ) {
			// ����ɬ��
			case "checkEmpty":
			//	if(empty($val)) $this->errors[$fieldName] = $message;	
				if(is_array($val)){
					if(empty($val)) $this->errors[$fieldName] = $message;
				} else {
					if(empty($val) && strcmp($val,0)!==0) $this->errors[$fieldName] = $message; // empty����0 ��̤���Ϥ�Ƚ�ꤵ��Ƥ��ޤ��Τ�
				}
				break;
	
			// ID���ѥ���� ���������
			case "checkAccount":
				if(!$this->checkAccount($val, $rules[1], $rules[2])){
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// 2�ĤΥǡ��������
			case "checkCompare":
				if(!$this->checkCompare($val, $rules[1], $rules[2])){
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// �ѥ���ɤʤɤ����ϳ�ǧ�ѥǡ����ȤΥ����å�
			case "checkConfirm":
				if(!$this->checkCompare($val, $this->data[$fieldName."_confirm"]))	{
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// ��ˡ��������å�
			case "checkUnique":
				if(!empty($rules[3])) {
					$res = $this->checkUnique($val, $rules[1], $rules[2], $rules[3]);
				} else {
					$res = $this->checkUnique($val, $rules[1], $rules[2]);
				}
				if(!$res){
					$this->errors[$fieldName] = $message;
				}
				break;
	
	
			// ����ɽ������
			case "checkSeiki":
				if(!$this->checkSeiki($val, $rules[1]))	{
					$this->errors[$fieldName] = $message;
				}
				break;
	
			// �ǥե���� ����¾
			default:
				$rule = $rules[0];
				if(isset($rules[2])) {			// �ѥ�᡼��2��
					if(!$this->$rule($val, $rules[1], $rules[2])){
						$this->errors[$fieldName] = $message;
					}
				} else if(isset($rules[1])) {	// �ѥ�᡼������
					if(!$this->$rule($val, $rules[1])){
						$this->errors[$fieldName] = $message;
					}
				} else {						// �ѥ�᡼���ʤ�
					if(!$this->$rule($val)) {
						$this->errors[$fieldName] = $message;
					}
				}
				break;
		}
		return true;
	}
	
	
	
	
	
	#----------------------------------------------------------------
	# Ⱦ�ѿ���
	#----------------------------------------------------------------
	function checkNum($val) {
		//$val = mb_convert_kana($val, "n", CHARSET);
		return (is_numeric($val)) ? true : false;
	}	
	#----------------------------------------------------------------
	# ��ˡ��� �ǡ����ν�ʣ�����å�
	#----------------------------------------------------------------
	function checkUnique($val, $tablename, $colname, $id=null) {
		$common_dao = new common_dao();

		// id�Υ����̾�����
		$con = $common_dao->query("SHOW COLUMNS FROM $tablename");
		for($i=0; $i<$con->numrows(); $i++){
			$tmp = $con->fetch();
			if($tmp["Key"] == "PRI"){
				$idname = $tmp["Field"];
				break;
			}
		}

		$where_str = $colname."='{$val}' AND del_flg=0";
		if(!empty($id)) $where_str .= " AND $idname<>".$id;
		$cnt = $common_dao->getOne("Count(*)", $tablename, $where_str);
		
		return ($cnt==0) ? true : false;	
	}
	#----------------------------------------------------------------
	# ���ե����å�
	#----------------------------------------------------------------
	function checkDate($val) {
		if($val=="0000-00-00") return true;	
		if(!preg_match("/^\d{4}(-|\/)\d{1,2}(-|\/)\d{1,2}$/", $val)) return false;
	
		$sep = substr($val, 4,1);
		$tmp = explode($sep, $val);
		return checkdate($tmp[1], $tmp[2], $tmp[0]);	
	}	
	#----------------------------------------------------------------
	# ���������
	#----------------------------------------------------------------
	function checkAccount($val, $keta1, $keta2) {
		return preg_match("/^[0-9A-Za-z\-\/\_]{{$keta1},{$keta2}}$/", $val) ? true : false;
	}	
	#----------------------------------------------------------------
	# ����塢�Ǿ���
	#----------------------------------------------------------------
	function maxLength($val, $max) {
		return (mb_strlen($val, CHARSET) <= $max);
	}
	function minLength($val, $min) {
		return (mb_strlen($val, CHARSET) >= $min);
	}	
	#----------------------------------------------------------------
	# ���
	#----------------------------------------------------------------
	function checkCompare($val1, $val2, $togo="=") {		
		if(empty($val2)) return true;
		switch($togo) {
			case "=":
			case "equal": 
				return trim($val1) == trim($val2); 
				break;
			case ">": return trim($val1) > trim($val2);	break;	
			case "<": return trim($val1) < trim($val2); break;
			case ">=": return trim($val1) >= trim($val2); break;	
			case "<=": return trim($val1) <= trim($val2); break;
		}
	}
	#----------------------------------------------------------------
	# ��
	#----------------------------------------------------------------
	function checkKeta($val, $keta) {
		return (mb_strlen($val)==$keta) ? true : false;
	}
	#----------------------------------------------------------------
	# Ǥ�դμ�
	#----------------------------------------------------------------
	function checkSeiki($val, $seiki) {
		return preg_match($seiki, $val) ? true : false;
	}
	#----------------------------------------------------------------
	# Tel
	#----------------------------------------------------------------
	function checkTel($val) {
		if(empty($val)) return true;
		return preg_match('/\d{2,5}-\d{2,4}-\d{3,4}$/', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# tel2���ϥ��ե�ʤ�
	#----------------------------------------------------------------
	function checkTel2($val) {
		if(empty($val)) return true;
		return preg_match('/\d{10,12}$/', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# time
	#----------------------------------------------------------------
	function checkTime($val) {
		if(empty($val)) return true;
		return ereg('^([0-1][0-9]|[2][0-3]):[0-5][0-9]$', $val) ? true : false;
	}
	#----------------------------------------------------------------
	# PostNo
	#----------------------------------------------------------------
	function checkPostNo($val) {
		if(empty($val)) return true;
		return preg_match("/^\d{3}\-\d{4}$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# url
	#----------------------------------------------------------------
	function checkURL($val) {
		if(empty($val)) return true;
		return preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# mail
	#----------------------------------------------------------------
	function checkEmail($val) {
		if(empty($val)) return true;
		return preg_match("/^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# ���ѥ���
	#----------------------------------------------------------------
	function checkKana($val) {
		//$val = mb_convert_encoding($val, "UTF-8", "EUC-JP");
		//mb_regex_encoding("UTF-8");
		// Ⱦ�Ѥ��̤���
		$val = $this->han_kaku_to_jen_kaku($val);	
		//return (preg_match("/^(\xe3(\x82[\xa1-\xbf]|\x83[\x80-\xb6]|\x83\xbc))*$/",$val))? true: false;	
		return (preg_match("/^(\xa5[\xa1-\xf6]|\xa1[\xb3\xb4\xbc])+$/",$val))? true: false;	// euc
		//return (preg_match("/^[��-����]+$/u", $val))? true: false; // utf08
	}	
	#----------------------------------------------------------------
	# �Ҥ餬��
	#----------------------------------------------------------------
	function checkHiragana($val) {
		mb_regex_encoding("UTF-8");  
		return (preg_match("/^[��-��]+$/u", $val))? true: false;	
	}
	#----------------------------------------------------------------
	# Ⱦ�ѱѿ���
	#----------------------------------------------------------------
	function checkEisu($val) {
		$val = mb_convert_kana($val, "n", "EUC-JP");
		return preg_match("/^[0-9A-Za-z \t\/._-]+$/", $val) ? true : false;
	}
	#----------------------------------------------------------------
	# �ե����륵���������å�
	#----------------------------------------------------------------
	function checkFileSize($val, $size)	{
	
	//////////////////////////////////////	if(empty($this->data[$this->name]['file_'.$field]['name'])) return true;
		if(empty($val)) return true;
	
	}

	#----------------------------------------------------------------
	# ǯ������դ�������������å�
	#----------------------------------------------------------------
	function checkNengo($data, $nengo, $yy, $mm, $dd) {
	
		$nengo 	= $this->data[$this->alias][$nengo];
		$yy 		= $this->data[$this->alias][$yy];
		$mm 		= $this->data[$this->alias][$mm];
		$dd 		= $this->data[$this->alias][$dd];
	
		if($nengo=="" || $yy=="" || $mm=="" || $dd=="") return true;
	
		switch($nengo){
			case "����": $yy+=1867; break;
			case "����": $yy+=1911; break;
			case "����": $yy+=1925; break;
			case "ʿ��": $yy+=1988; break;
		}
	
		// ���դ�������
		if(!checkdate($mm, $dd, $yy)) return false;
	
		if($nengo=="ʿ��" && "{$yy}/{$mm}/{$dd}">="1989/1/8" && $yy<=date("Y")){
			$flg = true;
		} else if($nengo=="����" && "{$yy}/{$mm}/{$dd}">="1925/12/25" && "{$yy}/{$mm}/{$dd}"<="1989/1/7"){
			$flg = true;
		} else if($nengo=="����" && "{$yy}/{$mm}/{$dd}">="1912/7/30" && "{$yy}/{$mm}/{$dd}"<="1926/12/24"){
			$flg = true;
		} else if($nengo=="����" && "{$yy}/{$mm}/{$dd}">="1867/7/29"){
			$flg = true;
		} else {
			$flg = false;
		}
		return $flg;
	
	}
	
	#----------------------------------------------------------------
	# Ⱦ�ѥ��ʢ����ѥ���
	#----------------------------------------------------------------
	function han_kaku_to_jen_kaku($val) {
			$replace_of = array('����','����','����','����','����','����','����','����','����','����','����','����','����','��','�Î�','�Ď�','�ʎ�','�ˎ�','�̎�','�͎�','�Ύ�','�ʎ�','�ˎ�','�̎�','�͎�','�Ύ�');
			$replace_by = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');
			$_result = str_replace($replace_of, $replace_by, $val);
			
			$replace_of = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
			    '��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
			    '��','��','��','��'	);
			$replace_by = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
			    '��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
			    '��','��','��',''	); 
			$_result = str_replace($replace_of, $replace_by, $_result);
			return $_result;
	}
	
	/*
	
	// ��������Ͽ���ѹ����줿��������å�
	function checkUpload($fieldname) {
		$model = $this->name;
		$file = $this->data[$model][$fieldname];
	
		if(!empty($this->data[$model]['id'])) {
			$ex = $this->field($fieldname, "id=".$this->data[$model]['id']);
	echo $ex."---";
	echo $file."<br>";
	
			if($ex!=$file) {
				return true;
			}
		}
		return false;
	}
*/
	/**
	 * ���դ����Ĥ�ʬ���줿���ϥե����ࡡ
	 * ǯ��������٤����򤵤�Ƥ��뤫�ɤ���������å�����
	 * @param string $year_name
	 * @param string $month_name
	 * @param string $day_name
	 * @param string $msg
	 * @return boolean
	 */
	function checkDate3($year_name, $month_name, $day_name, $msg) {
		if(empty($this->data[$year_name]) && empty($this->data[$month_name]) && empty($this->data[$day_name])) {
			return true;
		}
		
		if(((!empty($this->data[$year_name]) && (empty($this->data[$month_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$month_name]) && (empty($this->data[$year_name]) || empty($this->data[$day_name])))
		|| (!empty($this->data[$day_name]) && (empty($this->data[$year_name]) || empty($this->data[$month_name])))
		) || !checkdate($this->data[$month_name], $this->data[$day_name], $this->data[$year_name])	
		) {
			$this->errors[$year_name] = $msg;
			$this->errors[$month_name] = "";
			$this->errors[$day_name] = "";
		}
	}
		
	/**
	 * TEL�����Ĥ�ʬ���줿���ϥե����ࡡ
	 * tel01��tel02��tel03���٤����Ϥ���Ƥ��뤫�ɤ���������å�����
	 * @param int $name1
	 * @param int $name2
	 * @param int $name3
	 * @param string $msg
	 * @return boolean
	 */
	function checkTel3($name1, $name2, $name3, $hiss_flg=false, $name="�����ֹ�") {
		
		if(!$hiss_flg && empty($this->data[$name1]) && empty($this->data[$name2]) && empty($this->data[$name3])) {
			// ����ɬ�ܹ��ܤǤʤ������٤ƶ����ä��饹�롼
			return true;
		}

		$msg = $name."�����������Ϥ��Ƥ�������";
		
		// ��������Ѣ�Ⱦ�Ѥν���
		if(!empty($this->data[$name1])) $this->data[$name1] = mb_convert_kana($this->data[$name1], "n", CHARSET);
		if(!empty($this->data[$name2])) $this->data[$name2] = mb_convert_kana($this->data[$name2], "n", CHARSET);
		if(!empty($this->data[$name3])) $this->data[$name3] = mb_convert_kana($this->data[$name3], "n", CHARSET);

		$flg = true;
		if($hiss_flg && (empty($this->data[$name1]) || empty($this->data[$name2]) || empty($this->data[$name3]))) $flg = false;
			
		if(!empty($this->data[$name1]) && (empty($this->data[$name2]) || empty($this->data[$name3]))) $flg = false;
		if(!empty($this->data[$name2]) && (empty($this->data[$name1]) || empty($this->data[$name3]))) $flg = false;
		if(!empty($this->data[$name3]) && (empty($this->data[$name2]) || empty($this->data[$name1]))) $flg = false;
		if(!empty($this->data[$name1]) && !$this->checkNum($this->data[$name1])) $flg = false;
		if(!empty($this->data[$name2]) && !$this->checkNum($this->data[$name2])) $flg = false;
		if(!empty($this->data[$name3]) && !$this->checkNum($this->data[$name3])) $flg = false;
		
		if(!$flg){
			$this->errors[$name1] = $msg;
			$this->errors[$name2] = "";
			$this->errors[$name3] = "";
		}
	}
}
?>
