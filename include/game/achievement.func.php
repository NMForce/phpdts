<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}

function valid_achievement($s)
{
	global $gamecfg;
	require config("gamecfg",$gamecfg);
	$k=-1;
	//if (strlen($s)<=($achievement_count*4)) return false;
	for ($i=1; $i<=$achievement_count*2; $i++)
	{
		if (strpos($s,"/",$k+1)===false) return false;
		$k=strpos($s,"/",$k+1);
	}
	return true;
}

function init_achievement($t)
{
	global $gamecfg;
	require config("gamecfg",$gamecfg);
	$k=-1; $cnt=0;
	$f=0;
	while (1) 
	{
		if (strpos($t,"/",$k+1)===false) {$f=1;break;}
		$k=strpos($t,"/",$k+1); $cnt++;
	}
	$s=$t;
	if (($f==1)&&($cnt==0)) {$s='';}
	for ($i=1; $i<=$achievement_count*2-$cnt; $i++) $s.="0/";
	return $s;
}

function check_achievement($which, $who)
{
	global $gamecfg,$name,$db,$tablepre;
	$result = $db->query("SELECT achievement FROM {$tablepre}users WHERE username = '$who'");
	$ach = $db->result($result, 0);
	$bg=-1;
	for ($i=1; $i<=$which*2; $i++) $bg=strpos($ach,"/",$bg+1);
	$bg++; $ed=strpos($ach,"/",$bg);
	$db->query("UPDATE {$tablepre}users SET achievement='$ach' WHERE username='".$who."'" );
	$a=(int)substr($ach,$bg,$ed-$bg);
	return $a;
}

function fetch_achievement($which,$who)
{
	global $gamecfg,$name,$db,$tablepre;
	$result = $db->query("SELECT achievement FROM {$tablepre}users WHERE username = '$who'");
	$ach = $db->result($result, 0);
	$bg=-1;
	for ($i=1; $i<=$which*2+1; $i++) $bg=strpos($ach,"/",$bg+1);
	$bg++; $ed=strpos($ach,"/",$bg);
	$db->query("UPDATE {$tablepre}users SET achievement='$ach' WHERE username='".$who."'" );
	$a=(int)substr($ach,$bg,$ed-$bg);
	return $a;
}

function done_achievement($which,$ch,$who)
{
	global $gamecfg,$name,$db,$tablepre;
	$result = $db->query("SELECT achievement FROM {$tablepre}users WHERE username = '$who'");
	$ach = $db->result($result, 0);
	$bg=-1;
	for ($i=1; $i<=$which*2; $i++) $bg=strpos($ach,"/",$bg+1);
	$bg++; $ed=strpos($ach,"/",$bg);
	$ach=substr($ach,0,$bg).$ch.substr($ach,$ed);
	$db->query("UPDATE {$tablepre}users SET achievement='$ach' WHERE username='".$who."'" );
}

function update_achievement($which,$who,$value)
{
	global $cpl,$prc,$name,$db,$tablepre;
	$result = $db->query("SELECT achievement FROM {$tablepre}users WHERE username = '$who'");
	$ach = $db->result($result, 0);
	$bg=-1;
	for ($i=1; $i<=$which*2+1; $i++) $bg=strpos($ach,"/",$bg+1);
	$bg++; $ed=strpos($ach,"/",$bg);
	$ach=substr($ach,0,$bg).$value.substr($ach,$ed);
	$bg=-1;
	for ($i=1; $i<=$which*2+1; $i++) $bg=strpos($ach,"/",$bg+1);
	$bg++; $ed=strpos($ach,"/",$bg);
	$prc[$which]=substr($ach,$bg,$ed-$bg);
	$db->query("UPDATE {$tablepre}users SET achievement='$ach' WHERE username='".$who."'" );
}

function normalize_achievement($ach, &$crd1, &$crd2)
{
	global $gamecfg;
	require config("gamecfg",$gamecfg);
	require config("resources",$gamecfg);
	$crd1=0; $crd2=0;
	
}

function check_mixitem_achievement($nn,$item)
{
	global $now,$validtime,$starttime,$gamecfg,$name,$db,$tablepre;
	//0. KEY?????????
	if ($item=="???KEY???????????????") 
	{
		update_achievement(0,$nn,((int)fetch_achievement(0,$nn))+1);
		if ((int)fetch_achievement(0,$nn)>=30 && (check_achievement(0,$nn)<999)) {
		done_achievement(0,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+700 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
		elseif ((int)fetch_achievement(0,$nn)>=5 && (check_achievement(0,$nn)<2)) {
		done_achievement(0,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+200 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
		elseif ((int)fetch_achievement(0,$nn)>=1 && (check_achievement(0,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+10 WHERE username='".$nn."'" );
		done_achievement(0,1,$nn);
		}
	}
	//1. ??????KEY?????????
	if ($item=="???KEY???????????????")
	{
		$timeused=$now-$starttime; $besttime=(int)fetch_achievement(1,$nn);
		if ($timeused<$besttime || $besttime==0) update_achievement(1,$nn,$timeused);
		if (!check_achievement(1,$nn) && $timeused<=300) {
		done_achievement(1,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+30 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+16 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("KEY???",$nn);
		}
	}
	//14. ???????????????
	if ($item=="???KEY???????????????") 
	{
		update_achievement(14,$nn,((int)fetch_achievement(14,$nn))+1);
		if ((int)fetch_achievement(14,$nn)>=30 && (check_achievement(14,$nn)<999)) {
		done_achievement(14,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+700 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("TERRA",$nn);
		}
		elseif ((int)fetch_achievement(14,$nn)>=5 && (check_achievement(14,$nn)<2)) {
		done_achievement(14,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+200 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
		elseif ((int)fetch_achievement(14,$nn)>=1 && (check_achievement(14,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+10 WHERE username='".$nn."'" );
		done_achievement(14,1,$nn);
		}
	}
	//15. ???????????????
	if ($item=="???KEY???????????????") 
	{
		update_achievement(15,$nn,((int)fetch_achievement(15,$nn))+1);
		if ((int)fetch_achievement(15,$nn)>=30 && (check_achievement(15,$nn)<999)) {
		done_achievement(15,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+700 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
		elseif ((int)fetch_achievement(15,$nn)>=5 && (check_achievement(15,$nn)<2)) {
		done_achievement(15,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+200 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
		elseif ((int)fetch_achievement(15,$nn)>=1 && (check_achievement(15,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+10 WHERE username='".$nn."'" );
		done_achievement(15,1,$nn);
		}
	}
	//33. ???????????????
	if ($item=="??????????????????") 
	{
		update_achievement(33,$nn,((int)fetch_achievement(33,$nn))+1);
		if ((int)fetch_achievement(33,$nn)>=1 && (check_achievement(15,$nn)<999)) {
		done_achievement(33,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+522 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
	}
}
function check_end_achievement($w,$m)
{
	global $now,$validtime,$starttime,$gamecfg,$name,$db,$tablepre;
	//16. ??????????????????
	//$result = $db->query("SELECT achievement FROM {$tablepre}users WHERE username = '$w' AND type = 0");
	//$ach = $db->result($result, 0);
	if ($m==2)
	{
		update_achievement(16,$w,((int)fetch_achievement(16,$w))+1,$w);
		if (!check_achievement(16,$w)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits+150 WHERE username='".$w."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$w."'" );
		done_achievement(16,999,$w);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$w);
		}
	}
	//17. ??????????????????
	if ($m==5)
	{
		update_achievement(17,$w,((int)fetch_achievement(17,$w))+1,$w);
		if (!check_achievement(17,$w)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$w."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+100 WHERE username='".$w."'" );
		done_achievement(17,999,$w);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("???????????????",$w);
		}
	}
	//18. ??????????????????
	if ($m==3)
	{
		update_achievement(18,$w,((int)fetch_achievement(18,$w))+1);
		if (!check_achievement(18,$w)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits+500 WHERE username='".$w."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$w."'" );
		done_achievement(18,999,$w);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("???????????????",$w);
		}
	}
	//19. ??????????????????
	if ($m==7)
	{
		update_achievement(19,$w,((int)fetch_achievement(19,$w))+1);
		if (!check_achievement(19,$w)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits+1000 WHERE username='".$w."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+1000 WHERE username='".$w."'" );
		done_achievement(19,999,$w);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("???????????????",$w);
		}
	}
}

function check_battle_achievement($n,$is_npc,$killname,$wp)
{
	global $gamecfg,$w_name,$name,$db,$tablepre;
	$nn=$n;
	if ($nn==$killname){$nn=$w_name;}
	//2. ??????????????????
	if (!$is_npc)
	{
		update_achievement(2,$nn,((int)fetch_achievement(2,$nn))+1);
		if ((int)fetch_achievement(2,$nn)>=1000 && (check_achievement(2,$nn)<999)) {
		done_achievement(2,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+200 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("G.D.M",$nn);
		}
		elseif ((int)fetch_achievement(2,$nn)>=100 && (check_achievement(2,$nn)<2)) {
		done_achievement(2,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+500 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
		elseif ((int)fetch_achievement(2,$nn)>=10 && (check_achievement(2,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits+100 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		done_achievement(2,1,$nn);
		}
	}
	//31. ReturnToSender??????
	if (!$is_npc)
	{
		$result = $db->query("SELECT nicks FROM {$tablepre}users WHERE username = '$killname'");
		$ns = $db->result($result, 0);
		if ((strpos($ns,"KEY???")!==false)&&($wp=='???KEY???????????????')){
			update_achievement(31,$nn,((int)fetch_achievement(31,$nn))+1);
			if ((int)fetch_achievement(31,$nn)>=1 && (check_achievement(31,$nn)<999)) {
				done_achievement(31,999,$nn);
				include_once GAME_ROOT.'./include/game/titles.func.php';
				get_title("R.T.S",$nn);
				get_title("????????????",$killname);
				}
		}
	}
	//32. ??????
	if (!$is_npc)
	{
		$result = $db->query("SELECT nicks FROM {$tablepre}users WHERE username = '$killname'");
		$ns1 = $db->result($result, 0);
		$result = $db->query("SELECT nicks FROM {$tablepre}users WHERE username = '$nn'");
		$ns2 = $db->result($result, 0);
		if ((strpos($ns2,"LOOP")!==false)||(strpos($ns1,"LOOP")!==false)){
			if (check_achievement(32,$nn)<999) done_achievement(32,999,$nn);
			if (check_achievement(32,$killname)<999) done_achievement(32,999,$killname);
			include_once GAME_ROOT.'./include/game/titles.func.php';
			get_title("LOOP",$nn);
			get_title("LOOP",$killname);
		}
	}
	//3. ??????NPC?????? 
	if ($is_npc)
	{
		update_achievement(3,$nn,((int)fetch_achievement(3,$nn))+1);
		if ((int)fetch_achievement(3,$nn)>=10000 && (check_achievement(3,$nn)<999)) {
		done_achievement(3,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+500 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+15 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
		elseif ((int)fetch_achievement(3,$nn)>=500 && (check_achievement(3,$nn)<2)) {
		done_achievement(3,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+200 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
		elseif ((int)fetch_achievement(3,$nn)>=100 && (check_achievement(3,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+5 WHERE username='".$nn."'" );
		done_achievement(3,1,$nn);
		}
	}
	//4. ??????????????????
	if ($is_npc && ($killname=="??????" || $killname=="???????????? ??????")) 
	{
		update_achievement(4,$nn,((int)fetch_achievement(4,$nn))+1);
		if ((int)fetch_achievement(4,$nn)>=9 && (check_achievement(4,$nn)<999)) {
		done_achievement(4,999,$nn);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
		elseif ((int)fetch_achievement(4,$nn)>=1 && (check_achievement(4,$nn)<1)) {
		done_achievement(4,1,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+50 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+75 WHERE username='".$nn."'" );
		}
	}
	//13. ??????????????????
	if ($is_npc && ($killname=="??????" || $killname=="???????????? ??????")) 
	{
		update_achievement(13,$nn,((int)fetch_achievement(13,$nn))+1);
		if ((int)fetch_achievement(13,$nn)>=3 && (check_achievement(13,$nn)<999)) {
		done_achievement(13,999,$nn);
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
		elseif ((int)fetch_achievement(13,$nn)>=1 && (check_achievement(13,$nn)<1)) {
		done_achievement(13,1,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+50 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+75 WHERE username='".$nn."'" );
		}
	}
	//20. ??????????????????
	if ($is_npc && ($killname=="??????" || $killname=="?????? ??????")) 
	{
		update_achievement(20,$nn,((int)fetch_achievement(20,$nn))+1);
		if ((int)fetch_achievement(20,$nn)>=1 && (check_achievement(20,$nn)<999)) {
		done_achievement(20,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+268 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+263 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
	}
	//21. ??????????????????
	if ($is_npc && ($killname=="??????" || $killname=="?????? ??????")) 
	{
		update_achievement(21,$nn,((int)fetch_achievement(21,$nn))+1);
		if ((int)fetch_achievement(21,$nn)>=1 && (check_achievement(21,$nn)<999)) {
		done_achievement(21,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+233 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+233 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
	}
	//22. ??????????????????
	if ($is_npc && ($killname=="?????????MK-II" || $killname=="?????? ?????????MK-II")) 
	{
		update_achievement(22,$nn,((int)fetch_achievement(22,$nn))+1);
		if ((int)fetch_achievement(22,$nn)>=1 && (check_achievement(22,$nn)<999)) {
		done_achievement(22,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+2333 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("l33t",$nn);
		}
	}
	//23. ??????????????????
	if ($is_npc && ($killname=="???????????????BOSS" || $killname=="?????? ???????????????BOSS")) 
	{
		update_achievement(23,$nn,((int)fetch_achievement(23,$nn))+1);
		if ((int)fetch_achievement(23,$nn)>=1 && (check_achievement(23,$nn)<999)) {
		done_achievement(23,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+888 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
	}
	//24. ??????????????????
	if ($is_npc && ($killname=="???????????????" || $killname=="?????? ???????????????")) 
	{
		update_achievement(24,$nn,((int)fetch_achievement(24,$nn))+1);
		if ((int)fetch_achievement(24,$nn)>=1 && (check_achievement(24,$nn)<999)) {
		done_achievement(24,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+211 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+299 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
	}
	//25. ??????yoshiko??????
	if ($is_npc && ($killname=="Yoshiko-G" || $killname=="?????? Yoshiko-G")) 
	{
		update_achievement(25,$nn,((int)fetch_achievement(25,$nn))+1);
		if ((int)fetch_achievement(25,$nn)>=1 && (check_achievement(25,$nn)<999)) {
		done_achievement(25,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+111 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+333 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
	}
	//26. ?????????????????????
	if ($is_npc && ($killname=="?????????" || $killname=="????????? ?????????")) 
	{
		update_achievement(26,$nn,((int)fetch_achievement(26,$nn))+1);
		if ((int)fetch_achievement(26,$nn)>=1 && (check_achievement(26,$nn)<999)) {
		done_achievement(26,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+1 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+111 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
	}
	//27. ??????????????? 
	if (($is_npc>=20)&&($is_npc<=22))
	{
		update_achievement(27,$nn,((int)fetch_achievement(27,$nn))+1);
		if ((int)fetch_achievement(27,$nn)>=100 && (check_achievement(27,$nn)<999)) {
		done_achievement(27,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+500 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("????????????",$nn);
		}
		elseif ((int)fetch_achievement(27,$nn)>=30 && (check_achievement(27,$nn)<2)) {
		done_achievement(27,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+300 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		}
		elseif ((int)fetch_achievement(27,$nn)>=1 && (check_achievement(27,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+10 WHERE username='".$nn."'" );
		done_achievement(27,1,$nn);
		}
	}
}


function check_item_achievement($nn,$i,$ie,$is,$ik,$isk)
{
	global $gamecfg,$name,$db,$tablepre,$now,$starttime,$gamestate;
	//28. ????????????
	if (($gamestate==50)&&($i=="???????????????ID???")) 
	{
		$t=$now-$starttime;
		$besttime=(int)fetch_achievement(28,$nn);
		if ($t<$besttime || $besttime==0) update_achievement(28,$nn,$t);
		if (!check_achievement(28,$nn) && $t<=1800) {
		done_achievement(28,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits+250 WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
	}
	//29. ????????????
	if (($ik=='HS')||($ik=='HH')||($ik=='HB'))
	{
		$heal=$ie;
		if ($ik=='HB') $heal+=$ie;
		$uu=((int)fetch_achievement(29,$nn))+$heal;
		if ($uu>9999999) $uu=9999999;
		update_achievement(29,$nn,$uu);
		if (((int)fetch_achievement(29,$nn)>=999983) && (check_achievement(29,$nn))<999) {
		done_achievement(29,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+200 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("???????????????",$nn);
		}
		elseif ((int)fetch_achievement(29,$nn)>=142857 && (check_achievement(29,$nn)<2)) {
		done_achievement(29,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+50 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("?????????",$nn);
		}
		elseif ((int)fetch_achievement(29,$nn)>=32767 && (check_achievement(29,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+5 WHERE username='".$nn."'" );
		done_achievement(29,1,$nn);
		}
	}
	//30. ????????????
	$kk=substr($ik,0,1);
	if (($kk=='P')&&($ie>=30))
	{
		update_achievement(30,$nn,((int)fetch_achievement(30,$nn))+1);
		if (((int)fetch_achievement(30,$nn)>=365) && (check_achievement(30,$nn))<999) {
		done_achievement(30,999,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+200 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
		elseif ((int)fetch_achievement(30,$nn)>=133 && (check_achievement(30,$nn)<2)) {
		done_achievement(30,2,$nn);
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+50 WHERE username='".$nn."'" );
		include_once GAME_ROOT.'./include/game/titles.func.php';
		get_title("??????",$nn);
		}
		elseif ((int)fetch_achievement(30,$nn)>=5 && (check_achievement(30,$nn)<1)) {
		$db->query("UPDATE {$tablepre}users SET credits=credits WHERE username='".$nn."'" );
		$db->query("UPDATE {$tablepre}users SET credits2=credits2+5 WHERE username='".$nn."'" );
		done_achievement(30,1,$nn);
		}
	}
}


?>
