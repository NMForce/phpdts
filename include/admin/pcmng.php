<?php
if(!defined('IN_ADMIN')) {
	exit('Access Denied');
}
if(!isset($command)){$command = 'list';}
if(!isset($start)){$start = 0;}
if(!isset($checkmode)){$checkmode = '';}
if(!isset($checkinfo)){$checkinfo = '';}
if(!isset($pagemode)){$pagemode = '';}

$cmd_info = '';
$start = getstart($start,$pagemode);
$resultinfo = '';
if($command != 'submitedit'){	
	$pcdata = dbsearch($start,$checkmode,$checkinfo);
}


if($command == 'kill' || $command == 'live' || $command == 'del') {
	$operlist = $operlist2 = $dfaillist = $gfaillist = array();
	for($i=0;$i<$showlimit;$i++){
		if(isset(${'pc_'.$i})) {
			if(isset($pcdata[$i]) && $pcdata[$i]['pid'] == ${'pc_'.$i}){
				if($command == 'kill'){
					if($pcdata[$i]['hp'] > 0){
						$operlist[${'pc_'.$i}] = $pcdata[$i]['name'];
						$pcdata[$i]['hp'] = 0;
						$pcdata[$i]['state'] = 15;
						$deathnum ++;$alivenum--;
						adminlog('killpc',$pcdata[$i]['name']);
						addnews($now,'death15',$pcdata[$i]['name']);
					}else{
						$gfaillist[] = $pcdata[$i]['name'];
					}					
				}elseif($command == 'live'){
					if($pcdata[$i]['hp'] <= 0){
						$operlist[${'pc_'.$i}] = $pcdata[$i]['name'];
						$pcdata[$i]['hp'] = $pcdata[$i]['mhp'];
						$pcdata[$i]['state'] = 0;
						$deathnum --;$alivenum++;
						adminlog('livepc',$pcdata[$i]['name']);
						addnews($now,'alive',$pcdata[$i]['name']);
					}else{
						$gfaillist[] = $pcdata[$i]['name'];
					}					
				}elseif($command == 'del'){
					
					if($pcdata[$i]['hp'] > 0){					
						$operlist[${'pc_'.$i}] = $pcdata[$i]['name'];	
						$pcdata[$i]['hp'] = 0;
						$pcdata[$i]['state'] = 16;
						$deathnum --;$alivenum++;
						adminlog('delpc',$pcdata[$i]['name']);
						addnews($now,'death16',$pcdata[$i]['name']);
					}else{
						$operlist2[${'pc_'.$i}] = $pcdata[$i]['name'];
						adminlog('delcp',$pcdata[$i]['name']);
						addnews($now,'delcp',$pcdata[$i]['name']);
					}
				}
			}else{
				$dfaillist[] = ${'pc_'.$i};
			}			
		}
	}
	if($operlist || $operlist2 || $dfaillist || $gfaillist){
		if($command == 'kill'){
			$operword = '?????????';
			$qryword = "UPDATE {$tablepre}players SET hp='0',state='15' ";
		}elseif($command == 'live'){
			$operword = '?????????';
			$qryword = "UPDATE {$tablepre}players SET hp=mhp,state='0' ";
		}elseif($command == 'del'){
			$operword = '?????????';
			$qryword = "UPDATE {$tablepre}players SET hp='0',state='16',weps='0',arbs='0',arhs='0',aras='0',arfs='0',arts='0',itms0='0',itms1='0',itms2='0',itms3='0',itms4='0',itms5='0',itms6='0',money='0' ";
			$operword2 = '??????????????????';
			$qryword2 = "UPDATE {$tablepre}players SET weps='0',arbs='0',arhs='0',aras='0',arfs='0',arts='0',itms0='0',itms1='0',itms2='0',itms3='0',itms4='0',itms5='0',itms6='0',money='0' ";
		}
		if($operlist){
			$qrywhere = '('.implode(',',array_keys($operlist)).')';
			$opernames = implode('???',($operlist));
			$db->query("$qryword WHERE pid IN $qrywhere");
			//echo "$qryword WHERE pid IN $qrywhere";
			$cmd_info .= " ?????? $opernames $operword ???<br>";
		}
		if($operlist2){
			$qrywhere2 = '('.implode(',',array_keys($operlist2)).')';
			$opernames = implode(',',($operlist2));
			$db->query("$qryword2 WHERE pid IN $qrywhere2");
			//echo "$qryword2 WHERE pid IN $qrywhere2";
			$cmd_info .= " ?????? $opernames $operword2 ???<br>";
		}
		if($gfaillist){
			$gfailnames = implode(',',($gfaillist));
			$cmd_info .= " ?????? $gfailnames ?????????????????????????????? $operword  ???<br>";
		}
		if($dfaillist){
			$dfailnames = implode(',',($dfaillist));
			$cmd_info .= " PID??? $dfailnames ??????????????????????????????????????????  ???<br>";
		}
		save_gameinfo();
	}else{
		$cmd_info = "???????????????????????????????????????????????????";
	}
	$command = 'list';
} elseif(strpos($command ,'edit')===0) {
	$pid = explode('_',$command);
	$no = (int)$pid[1];
	$pid = (int)$pid[2];
	if(!$pid){
		$cmd_info = "??????UID?????????";
	}elseif(!isset($pcdata[$no]) || $pcdata[$no]['pid'] != $pid){
		$cmd_info = "??????????????????????????????????????????";
	}else{
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$pid' AND type='0'");
		$pc = $db->fetch_array($result);
		if(!$pc) {
			$cmd_info = "??????????????? ".$pcdata[$no]['name']." ???";
		}else{
			$command = 'check';
		}
	}
} elseif($command == 'submitedit') {
	$db->query("UPDATE {$tablepre}players SET gd='$gd',icon='$icon',club='$club',sNo='$sNo',hp='$hp',mhp='$mhp',sp='$sp',msp='$msp',ss='$ss',mss='$mss',att='$att',def='$def',pls='$pls',achievement='$achievement',exp='$exp',money='$money',bid='$bid',inf='$inf',rage='$rage',pose='$pose',tactic='$tactic',killnum='$killnum',wp='$wp',wk='$wk',wg='$wg',wc='$wc',wd='$wd',wf='$wf',teamID='$teamID',achievement='$achievement',wep='$wep',wepk='$wepk',wepe='$wepe',weps='$weps',wepsk='$wepsk',arb='$arb',arbk='$arbk',arbe='$arbe',arbs='$arbs',arbsk='$arbsk',arh='$arh',arhk='$arhk',arhe='$arhe',arhs='$arhs',arhsk='$arhsk',ara='$ara',arak='$arak',arae='$arae',aras='$aras',arask='$arask',arf='$arf',arfk='$arfk',arfe='$arfe',arfs='$arfs',arfsk='$arfsk',art='$art',artk='$artk',arte='$arte',arts='$arts',artsk='$artsk',itm0='$itm0',itmk0='$itmk0',itme0='$itme0',itms0='$itms0',itmsk0='$itmsk0',itm1='$itm1',itmk1='$itmk1',itme1='$itme1',itms1='$itms1',itmsk1='$itmsk1',itm2='$itm2',itmk2='$itmk2',itme2='$itme2',itms2='$itms2',itmsk2='$itmsk2',itm3='$itm3',itmk3='$itmk3',itme3='$itme3',itms3='$itms3',itmsk3='$itmsk3',itm4='$itm4',itmk4='$itmk4',itme4='$itme4',itms4='$itms4',itmsk4='$itmsk4',itm5='$itm5',itmk5='$itmk5',itme5='$itme5',itms5='$itms5',itmsk5='$itmsk5',itm6='$itm6',itmk6='$itmk6',itme6='$itme6',itms6='$itms6',itmsk6='$itmsk6' where pid='$pid'");
	if(!$db->affected_rows()){
		$cmd_info = "?????????????????? $name";
	} else {
		adminlog('editpc',$name);
		addnews($now,'editpc',$name);
		$cmd_info = "?????? $name ?????????????????????";
	}
	$pcdata = dbsearch($start,$checkmode,$checkinfo);
}
include template('admin_pcmng');


function dbsearch($start,$checkmode,$checkinfo){
	global $showlimit,$db,$tablepre,$resultinfo,$cmd_info;
	$limitstr = " LIMIT $start,$showlimit";
	if(($checkmode == 'name')&&($checkinfo)) {
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE name LIKE '%{$checkinfo}%' AND type='0'".$limitstr);
	} elseif($checkmode == 'teamID') {
		if($checkinfo){
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE teamID LIKE '%".$checkinfo."%' AND type='0' ORDER BY teamID".$limitstr);
		} else {
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE type='0' ORDER BY teamID DESC".$limitstr);
		}
	} elseif($checkmode == 'club') {
		if($checkinfo) {
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE club='$checkinfo' AND type='0'".$limitstr);
		} else {
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE type='0' ORDER BY club".$limitstr);
		}
	} else {
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE type='0'".$limitstr);
	}
	if(!$db->num_rows($result)) {
		$cmd_info = '??????????????????????????????';
		$startno = $start + 1;
		$resultinfo = '????????????'.$startno.'?????????';
		$pcdata = Array();
	} else {
		while($pc = $db->fetch_array($result)) {
			$pcdata[] = $pc;
		}
		$startno = $start + 1;
		$endno = $start + count($pcdata);
		$resultinfo = '???'.$startno.'???-???'.$endno.'?????????';
	}
	return $pcdata;
}
?>