<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	
 *	debug functions
 *----------------------------------------------------------------------------*/
/**
 * query debug
 *
 * query debug
 */
function debug_query(){
	global $log_query;
	echo("<table border=0 width=100% bgcolor=888888>
	        <tr bgcolor=888888 style='font-size:12px'>
	          <td colspan=5 style='color:white; font-weitht:900'>SQL LOG</td>
			</tr>
	        <tr bgcolor=CCCCCC style='font-size:11px; color:black'>
	          <td width=50>TIME</td>
	          <td width=40>NUMROWS </td>
	          <td width=40>AFFECTED</td>
	          <td          >QUERY</td>
	        </tr>
			");

	if(count($log_query) == 0){
			echo("
	        <tr bgcolor=FFFFFF style='font-size:12px'>
	          <td colspan=5 style='color:black; font-weitht:900'>EMPTY LOG</td>
			</tr>");
	}else{
		for($i = 0; $i < count($log_query) ; ++ $i){
			$t = $log_query[$i]["time"] ;
			$a = $log_query[$i]["affected"] ;
			if($a == ""){ $a = "none" ; }
			$n = $log_query[$i]["numrows"] ;
			$q = $log_query[$i]["query"] ;
			echo("<tr bgcolor=white style='font-size:10px; color:black'>
					<td width=90 align=center>$t</td>
					<td width=40  align=right >$n</td>
					<td width=40  align=right >$a</td>
					<td align=left  >$q</td>
					</tr>");
		}
	}
	echo("</table><br>");
}

/**
 * debug status
 *
 * debug status
 *
 * @param string	$module	/td>
 * @param string	$action	/td>
 * @param int		$lap	tion	/td>
 */
function debug_status($module,$action,$lap){
	$sn     = $_SERVER["HTTP_HOST"] ;
	$sa     = $_SERVER["SERVER_ADDR"] ;
	$query  = $_SERVER["QUERY_STRING"] ;
	$cookie = $_SERVER["HTTP_COOKIE"] ;

	$lap = sprintf("%0.3f",$lap);

	$mu = memory_get_usage(true) ;
	$mp = memory_get_peak_usage(true);
	$mu = sprintf("%5s" , (int) $mu / 1024) ;
	$mp = sprintf("%5s" , (int) $mp / 1024) ;


	echo("<table border=0 width=100% bgcolor=888888>
		<tr bgcolor=888888 style='font-size:12px'>
		  <td colspan=6 style='color:white; font-weitht:900'>FRAME WORK STATUS</td>
		</tr>
		<tr bgcolor=FFFFFF style='font-size:12px; color:black'>
		 <td width=115 align=center bgcolor=CCCCCC>STATUS</td>
		 <td width=115 align=center>Module = $module</td>
		 <td width=115 align=center>Action = $action</td>
		 <td width=115 align=center>Time = $lap sec</td>
		 <td width=115 align=center>IP = $sa</td>
		 <td           align=left>&nbsp;HTTP_HOST = $sn</td>
		</tr>
		<tr bgcolor=FFFFFF style='font-size:12px; color:black'>
		 <td colspan=1 width=100 align=center bgcolor=CCCCCC>COOKIE</td>
		 <td colspan=3 align=left>&nbsp;$cookie</td>
		 <td colspan=1 width=100 align=center bgcolor=CCCCCC>Memory</td>
		 <td colspan=1 align=left>&nbsp;Use $mu kb , Peek $mu kb</td>
		</tr>
		<tr bgcolor=FFFFFF style='font-size:12px; color:black'>
		 <td width=100 align=center bgcolor=CCCCCC>QUERYSTR</td>
		 <td colspan=6 align=left>&nbsp;$query</td>
			</tr>
	");
	echo("</table><br>");
}
/**
 * Debug Log write
 *
 * Debug Log write
 * (IS_LOG_OUTrite
 *
 * (IS_LOG_OUTrite
 * SITE_DIR/status/SYSID.log.yyyymmdd
 * SITE_DIR/status/SYSID.log.yyyymmdd
 *
 * SITE_DIR/status/SYSID.log.yyyymmdd
 *
 * @param string $loc
 * @param string $dat
 */
function logsave($loc,$dat){
/*
	//	js action skip
	if(stristr($_SERVER["REQUEST_URI"],"?action=get_state"))
		return ;
	if(stristr($_SERVER["REQUEST_URI"],"?action=get_table_status"))
		return ;
*/

	if (IS_LOG_OUT) {
		$date = date("Ym", time());
		$log = SITE_DIR . "status/" . SYSID . ".log." . $date ;
		$logf = SITE_DIR . "status/" . $loc . ".log." . $date ;

		$log = SITE_DIR . "status/" . SYSID  ."-" . $date . ".log";
		$logf = SITE_DIR . "status/" . $loc . ".log";

		$sid = session_id();
		if($sid == ""){
			$sid = "N/A" ;
		}
		$s = sprintf("[%-32s][%s][%-10s] %s\n",$sid,date("m/d H:i:s") , $loc , $dat);
                
                if(php_sapi_name() == "cli"){
                    $ru = "cli";
                }else{
                    $ru = $_SERVER["REQUEST_URI"] ;
                }
                
		if(stristr($ru,"/action.html")){
	//		return ;
		}

		if($ru != "/adm_system/sys_svc_status.html" && !stristr($ru,"realtime")){
			file_put_contents($log,$s,FILE_APPEND);
			// file_put_contents($logf,$s,FILE_APPEND);
		}
	}
}
/**
 * @param mixed $v
 */
function pr($v) {
	echo("<pre>");
	print_r($v);
	echo("</pre>");
}
/*
 * PDOのプレースフォルダに値を代入したSQL文を返す
 */
function getSql($sql, $prm=array()){
	if(empty($prm)) return $sql;

	$arr = explode("?", $sql);

	$str = "";
	foreach ($prm as $val) {
		$prm2[] = $val;
	}
	foreach($arr as $key=>$val){
		if(count($prm2)!=$key){
			$str .= $val . "'".$prm2[$key]."'";
		} else {
			$str .= $val;
		}
	}
	return str_replace(array("\n", "\t"), " ", $str);
}