<?php

if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function death($death, $kname = '', $ktype = 0, $annex = '') {
	global $now, $db, $tablepre, $alivenum, $deathnum, $name, $state, $deathtime, $type, $lvl, $bid, $killmsginfo, $typeinfo, $hp, $mhp, $wp, $wk, $wg, $wc, $wd, $wf, $sp, $msp, $club, $pls , $nick;
	if (! $death) {
		return;
	}
	$hp = 0;
	if ($death == 'N') {
		$state = 20;
	} elseif ($death == 'P') {
		$state = 21;
	} elseif ($death == 'K') {
		$state = 22;
	} elseif ($death == 'G') {
		$state = 23;
	} elseif ($death == 'C') {
		$state = 24;
	} elseif ($death == 'D') {
		$state = 25;
	} elseif ($death == 'F') {
		$state = 29;
	} elseif ($death == 'J') {
		$state = 23;
	}elseif ($death == 'poison') {
		$state = 26;
	} elseif ($death == 'trap') {
		$state = 27;
	} elseif ($death == 'event') {
		$state = 13;
	} elseif ($death == 'hack') {
		$state = 14;
	} elseif ($death == 'pmove') {
		$state = 12;
	} elseif ($death == 'hsmove') {
		$state = 17;
	} elseif ($death == 'umove') {
		$state = 18;
	} elseif ($death == 'button') {
		$state = 30;
	} elseif ($death == 'suiside') {
		$state = 31;
	} elseif ($death == 'gradius') {
		$state = 33;
	} elseif ($death == 'SCP') {
		$state = 34;
	} elseif ($death == 'salv'){
		$state = 35;
	} elseif ($death == 'kagari1'){
		$state = 36;
	} elseif ($death == 'kagari2'){
		$state = 37;
	} elseif ($death == 'kagari3'){
		$state = 38;
	} elseif ($death == 'gg'){
		$state = 39;
	} elseif ($death == 'fake_dn'){
		$state = 28;
	} else {
		$state = 10;
	}
	
	$killmsg = '';
	if ($ktype == 0 && $kname) {
		$result = $db->query ( "SELECT killmsg FROM {$tablepre}users WHERE username = '$kname'" );
		$killmsg = $db->result ( $result, 0 );
	} elseif ($ktype != 0 && $kname) {
		$killmsg = $killmsginfo [$ktype];
		$kname = "$typeinfo[$ktype] $kname";
	} else {
		$kname = '';
		$killmsg = '';
	}
	
	if (! $type) {
		$result = $db->query ( "SELECT lastword FROM {$tablepre}users WHERE username = '$name'" );
		$lastword = $db->result ( $result, 0 );
		$lwname = $typeinfo [$type] . ' ' . $name;
		/*$result = $db->query("SELECT pls FROM {$tablepre}players WHERE name = '$name' AND type = '$type'");
		$pls = $db->result($result, 0);*/
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	}
	$deathtime = $now;
	$result = $db->query("SELECT nick FROM {$tablepre}players WHERE name = '$kname' AND type = '$type'");
	$knick = $db->result($result, 0);
	addnews ( $now, 'death' . $state, $name, $type, $knick.' '.$kname, $annex, $lastword );
	//$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
	
	if ($type==0 && $club==99 && ($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" || $death=="D" || $death=="F" || $death=="J" || $death=="trap"))	
	{
		addnews($now,'revival',$name);	//?????????????????????????????????
		$hp=$mhp; $sp=$msp;
		$club=17; $state=0;
		$alivenum++;
	}
	
	$alivenum --;
	$deathnum ++;
	save_gameinfo ();
	
	return $killmsg;
}


function kill($death, $dname, $dtype = 0, $dpid = 0, $annex = '') {
	global $now, $db, $tablepre, $alivenum, $deathnum, $name, $w_state, $type, $pid, $typeinfo, $pls, $lwinfo, $w_achievement;
	
	if (! $death || ! $dname) {
		return;
	}
	
	if ($death == 'N') {
		$w_state = 20;
	} elseif ($death == 'P') {
		$w_state = 21;
	} elseif ($death == 'K') {
		$w_state = 22;
	} elseif ($death == 'G') {
		$w_state = 23;
	} elseif ($death == 'J') {
		$w_state = 23;
	} elseif ($death == 'C') {
		$w_state = 24;
	} elseif ($death == 'D') {
		$w_state = 25;
	} elseif ($death == 'F') {
		$w_state = 29;
	} elseif ($death == 'dn') {
		$w_state = 28;
	} else {
		$w_state = 10;
	}
	
	$killmsg = '';
	$result = $db->query ( "SELECT killmsg FROM {$tablepre}users WHERE username = '$name'" );
	$killmsg = $db->result ( $result, 0 );
	
	if (! $dtype) {
		//$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
		$alivenum --;
	}
	$deathnum ++;
	
	
	if ($dtype) {
		if($dtype == 15){//??????AI
			global $gamevars;
			$gamevars['sanmadead'] = 1;
			save_gameinfo();
		}
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		if (is_array ( $lwinfo [$dtype] )) {
			$lastword = $lwinfo [$dtype] [$dname];
		} else {
			$lastword = $lwinfo [$dtype];
		}
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	} else {
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		$result = $db->query ( "SELECT lastword FROM {$tablepre}users WHERE username = '$dname'" );
		$lastword = $db->result ( $result, 0 );
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	}
	$result = $db->query("SELECT nick FROM {$tablepre}players WHERE name = '$name' AND type = '$type'");
	$knick = $db->result($result, 0);
	addnews ( $now, 'death' . $w_state, $dname, $dtype, $knick.' '.$name, $annex, $lastword );
	
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid=$dpid" );
	$res=$db->fetch_array($result);
	$revivaled=false;
	if ($res['type']==0 && $res['club']==99 && ($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" ||$death=="D" || $death=="F" || $death=="J" || $death=="trap"))	
	{
		addnews($now,'revival',$res['name']);	//?????????????????????????????????
		$db->query ( "UPDATE {$tablepre}players SET hp=mhp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET sp=msp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET club=17 WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET state=0 WHERE pid=$dpid" );
		$alivenum++;
		$revivaled=true;
	}
	if (!$revivaled) $db->query ( "UPDATE {$tablepre}players SET hp='0',endtime='$now',deathtime='$now',bid='$pid',state='$w_state' WHERE pid=$dpid" );
//	if($dtype == 1 || $dtype == 9){
//		global $rdown,$bdown;
//		if($dtype == 1){
//			$rdown = 1;
//			storyputchat($now,'rdown');
//		}elseif($dtype == 9){
//			$bdown = 1;
//			storyputchat($now,'bdown');
//		}			
//	}
	
	save_gameinfo ();
	return $killmsg;
}

function lvlup(&$lvl, &$exp, $isplayer = 1) {
	global $baseexp;
	$up_exp_temp = round ( (2 * $lvl + 1) * $baseexp );
	if ($exp >= $up_exp_temp && $lvl < 255) {
		if ($isplayer) {
			$perfix = '';
		} else {
			$perfix = 'w_';
		}
		global ${$perfix . 'name'}, ${$perfix . 'hp'}, ${$perfix . 'mhp'}, ${$perfix . 'sp'}, ${$perfix . 'msp'}, ${$perfix . 'att'}, ${$perfix . 'def'}, ${$perfix . 'upexp'}, ${$perfix . 'club'}, ${$perfix . 'type'}, ${$perfix . 'skillpoint'};
		global ${$perfix . 'wp'}, ${$perfix . 'wk'}, ${$perfix . 'wc'}, ${$perfix . 'wg'}, ${$perfix . 'wd'}, ${$perfix . 'wf'};
		$sklanginfo = Array ('wp' => '??????', 'wk' => '??????', 'wg' => '??????', 'wc' => '??????', 'wd' => '??????', 'wf' => '??????', 'all' => '???????????????' );
		$sknlist = Array (1 => 'wp', 2 => 'wk', 3 => 'wc', 4 => 'wg', 5 => 'wd', 9 => 'wf', 16 => 'all' );
		$skname = $sknlist [${$perfix . 'club'}];
		//????????????
		$lvup = 1 + floor ( ($exp - $up_exp_temp) / $baseexp / 2 );
		$lvup = $lvup > 255 - $lvl ? 255 - $lvl : $lvup;
		$lvuphp = $lvupatt = $lvupdef = $lvupskill = $lvupsp = $lvupspref = 0;
		for($i = 0; $i < $lvup; $i += 1) {
			if (${$perfix . 'club'} == 13) {
				$lvuphp += rand ( 14, 18 );
			} else {
				$lvuphp += rand ( 8, 10 );
			}
			$lvupsp += rand( 4,6);
			if (${$perfix . 'club'} == 14) {
				$lvupatt += rand ( 4, 6 );
				$lvupdef += rand ( 5, 8 );
			} else {
				$lvupatt += rand ( 2, 4 );
				$lvupdef += rand ( 3, 5 );
			}
			
			if ($skname == 'all') {
				$lvupskill += rand ( 2, 4 );
			} elseif ($skname == 'wd' || $skname == 'wf') {
				$lvupskill += rand ( 3, 5 );
			}elseif($skname){
				$lvupskill += rand ( 4, 6 );
			}
			$lvupspref += round(${$perfix . 'msp'} * 0.1);		
		}
		$lvl += $lvup;
		$up_exp_temp = round ( (2 * $lvl + 1) * $baseexp );
		if ($lvl >= 255) {
			$lvl = 255;
			$exp = $up_exp_temp;
		}
		${$perfix . 'upexp'} = $up_exp_temp;
		${$perfix . 'hp'} += $lvuphp;
		${$perfix . 'mhp'} += $lvuphp;
		${$perfix . 'sp'} += $lvupsp;
		${$perfix . 'msp'} += $lvupsp;
		${$perfix . 'att'} += $lvupatt;
		${$perfix . 'def'} += $lvupdef;
		${$perfix . 'skillpoint'} += $lvup;
		if ($skname == 'all') {
			${$perfix . 'wp'} += $lvupskill;
			${$perfix . 'wk'} += $lvupskill;
			${$perfix . 'wg'} += $lvupskill;
			${$perfix . 'wc'} += $lvupskill;
			${$perfix . 'wd'} += $lvupskill;
			${$perfix . 'wf'} += $lvupskill;
		} elseif ($skname) {
			${$perfix . $skname} += $lvupskill;
		}
		
		if (${$perfix . 'sp'}+$lvupspref >= ${$perfix . 'msp'}) {
			$lvupspref =  ${$perfix . 'msp'} - ${$perfix . 'sp'};
			
		}
		${$perfix . 'sp'} += $lvupspref;
		if ($skname) {
			$sklog = "???{$sklanginfo[$skname]}+{$lvupskill}";
		}
		if ($isplayer) {
			global $log;
			$log .= "<span class=\"yellow\">?????????{$lvup}??????????????????+{$lvuphp}???????????????+{$lvupsp}?????????+{$lvupatt}?????????+{$lvupdef}{$sklog}??????????????????{$lvupspref}????????????{$lvup}???????????????</span><br>";
		} elseif (! $w_type) {
			global $w_pid, $now;
			$w_log = "<span class=\"yellow\">?????????{$lvup}??????????????????+{$lvuphp}???????????????+{$lvupsp}?????????+{$lvupatt}?????????+{$lvupdef}{$sklog}??????????????????{$lvupspref}????????????{$lvup}???????????????</span><br>";
			logsave ( $w_pid, $now, $w_log,'s');
		}
	} elseif ($lvl >= 255) {
		$lvl = 255;
		$exp = $up_exp_temp;
	}
	return;
}

/*function lvlup(&$lvl, &$exp, $isplayer = 1) {
	global $log,$baseexp;
	$up_exp_temp = round((2*$lvl+1)*$baseexp);
	if($exp >= $up_exp_temp && $lvl<255) {
		if($isplayer){
			global $name,$hp,$mhp,$sp,$msp,$att,$def,$upexp,$club;
			$sknlist=Array(1=>'wp',2=>'wk',3=>'wc',4=>'wg',5=>'wd',9=>'wf');//??????????????????
			
			$skname=$sknlist[$club];
			if($skname){
				global ${$skname},$skilllaninfo;
			}
			$lvup = 1+floor(($exp - $up_exp_temp)/$baseexp/2);
			$lvup = $lvup > 255-$lvl ? 255-$lvl : $lvup;
			//$log .="$lvup<br>";
			$lvuphp = $lvupatt = $lvupdef = $lvupskill =0;
			
			for ($i=0;$i<$lvup;$i+=1){
				$lvuphp += rand(8,10);$lvupatt += rand(2,4);$lvupdef += rand(3,5);
				if($skname){
					$lvupskill += rand(3,5);
				}
				$sp += ($msp * 0.1);
			}
			$lvl += $lvup;$up_exp_temp = round((2*$lvl+1)*$baseexp);

			if($lvl>=255){$lvl=255;$exp=$up_exp_temp;}
			$upexp=$up_exp_temp;
			$hp += $lvuphp;$mhp += $lvuphp;
			$att += $lvupatt;$def += $lvupdef;
			${$skname} += $lvupskill;
			if($sp >= $msp){$sp = $msp;}
			if($skname){
				$sklog = "???{$skilllaninfo[$skname]}+{$lvupskill}";
			}
			$log .= "<span class=\"yellow\">?????????{$lvup}????????????+{$lvuphp}?????????+{$lvupatt}?????????+{$lvupdef}{$sklog}???</span><br>";
		} else {
			global $now,$w_type,$w_pid,$w_name,$w_hp,$w_mhp,$w_sp,$w_msp,$w_att,$w_def,$w_upexp,$w_club;
			$sknlist=Array(1=>'wp',2=>'wk',3=>'wg',4=>'wc',5=>'wd',9=>'wf');//??????????????????
			$skname=$sknlist[$w_club];
			if($skname){
				global ${'w_'.$skname},$skilllaninfo;
			}
			$lvup = 1+floor(($exp - $up_exp_temp)/$baseexp/2);
			$lvup = $lvup > 255-$lvl ? 255-$lvl : $lvup;
			$lvuphp = $lvupatt = $lvupdef = $lvupskill = 0;
			for ($i=0;$i<$lvup;$i+=1){
				$lvuphp += rand(8,10);$lvupatt += rand(2,4);$lvupdef += rand(3,5);
				if($skname){
					$lvupskill += rand(3,5);
				}
				$w_sp += ($w_msp * 0.1);
			}
			$lvl += $lvup;$up_exp_temp = round((2*$lvl+1)*$baseexp);

			if($lvl>=255){$lvl=255;$exp=$up_exp_temp;}
			$w_upexp=$up_exp_temp;
			$w_hp += $lvuphp;$w_mhp += $lvuphp;
			$w_att += $lvupatt;$w_def += $lvupdef;
			${'w_'.$skname} += $lvupskill;
			if($w_sp >= $w_msp){$w_sp = $w_msp;}
			if(!$w_type){
				if($skname){
					$sklog = "???{$skilllaninfo[$skname]}+{$lvupskill}";
				}
				$w_log = "<span class=\"yellow\">?????????{$lvup}????????????+{$lvuphp}?????????+{$lvupatt}?????????+{$lvupdef}{$sklog}???</span><br>";
				logsave($w_pid,$now,$w_log);
			}
		}
	} elseif($lvl >= 255){$lvl=255;$exp=$up_exp_temp;}
	return;
}*/

//??????????????????????????????????????????


function rest($command) {
	global $now, $log, $mode, $cmd, $state, $endtime, $hp, $mhp, $sp, $msp, $sleep_time, $heal_time, $restinfo, $pose, $inf,$club,$exdmginf;
	
	if ($state == 1) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldsp = $sp;
		$upsp = round ( $msp * $resttime / $sleep_time / 100 );
		if ($pose == 5) {
			$upsp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$upsp = round ( $upsp / 2 );
		}
		if ($club ==16){
			$upsp *= 2;
		}
		$sp += $upsp;
		if ($sp >= $msp) {
			$sp = $msp;
		}
		$upsp = $sp - $oldsp;
		$log .= "?????????????????????<span class=\"yellow\">$upsp</span>??????<br>";
	} elseif ($state == 2) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldhp = $hp;
		$uphp = round ( $mhp * $resttime / $heal_time / 100 );
		if ($pose == 5) {
			$uphp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$uphp = round ( $uphp / 2 );
		}
		if ($club ==16){
			$uphp *= 2;
		}
		$hp += $uphp;
		if ($hp >= $mhp) {
			$hp = $mhp;
		}
		$uphp = $hp - $oldhp;
		$log .= "?????????????????????<span class=\"yellow\">$uphp</span>??????<br>";
	} elseif ($state == 3) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldsp = $sp;
		$upsp = round ( $msp * $resttime / $sleep_time / 100 );
		if ($pose == 5) {
			$upsp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$upsp = round ( $upsp / 2 );
		}
		if ($club ==16){
			$upsp *= 2;
		}
		$sp += $upsp;
		if ($sp >= $msp) {
			$sp = $msp;
		}
		$upsp = $sp - $oldsp;
		$oldhp = $hp;
		$uphp = round ( $mhp * $resttime / $heal_time / 100 );
		if ($pose == 5) {
			$uphp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$uphp = round ( $uphp / 2 );
		}
		if ($club ==16){
			$uphp *= 2;
		}
		$hp += $uphp;
		if ($hp >= $mhp) {
			$hp = $mhp;
		}
		$uphp = $hp - $oldhp;
		$log .= "?????????????????????<span class=\"yellow\">$upsp</span>?????????????????????<span class=\"yellow\">$uphp</span>??????<br>";
		
		$refintv = 90;
		if ($pose == 5) {
			$refintv -= 30;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$refintv += 30;
		}
		$spinf = preg_replace("/[h|b|a|f]/", "", $inf);
		$spinflength = strlen($spinf);
		if($spinf){
			$refflag = false;
			do{
				$dice = rand(0,$refintv);
				if($dice + 15 < $resttime){
					$infno = rand(0,$spinflength-1);
					$refinfstr = substr($spinf,$infno,1);
					$inf = str_replace($refinfstr,'',$inf);
					$spinf = str_replace($refinfstr,'',$spinf);
					$log .= "<span class=\"yellow\">??????{$exdmginf[$refinfstr]}?????????????????????</span><br>";
					$spinflength -= 1;
					$refflag = true;
				}
				$resttime -= $refintv;
			} while ($resttime > 0 && $spinflength > 0);
			if(!$refflag){
				$log .= "??????????????????????????????????????????????????????????????????<br>";
			}
		}
	} else {
		$mode = 'command';
	}
	
	if ($command != 'rest') {
		$state = 0;
		$endtime = $now;
		$mode = 'command';
	}
	return;
}

?>
