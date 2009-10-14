<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * view, new, edit, resolve, close, activate a bug.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */
/* Init BugFree system. */
require('Include/Init.inc.php');

$BugList = dbGetList('BugInfo', '*', "BugStatus <> 'Closed' AND IsDroped = '0'");

date_default_timezone_set(PRC);

// 设置进行中
$temp = mktime(0,0,0,date('m'),date('d')+3, date('Y'));
$mark_1 = date('Y-m-d H:i:s', $temp);  // 3天后

$temp = mktime(0,0,0,date('m'),date('d')+1, date('Y'));
$mark_2 = date('Y-m-d H:i:s', $temp);  // 1天后

$temp = mktime(0,0,0,date('m'),date('d')-1, date('Y'));
$mark_3 = date('Y-m-d H:i:s', $temp);  // 1天前

$temp = mktime(0,0,0,date('m'),date('d')-3, date('Y'));
$mark_4 = date('Y-m-d H:i:s', $temp);  // 3天前

	
foreach($BugList as $key => $value)
{	
	if ($value['Deadline'] == '0000-00-00 00:00')
	{
		continue;
	}
		
	if( $value['Deadline'] > $mark_1 )
	{
		if ($value['DeadlineStatus'] != $_LANG['DeadlineStatus'][1])
		{
			dbUpdateRow('buginfo','DeadlineStatus',"'{$_LANG[DeadlineStatus][1]}'", "BugID={$value[BugID]}");
		}
	}
	else if( $value['Deadline'] <= $mark_1 &&
			 $value['Deadline'] > $mark_3 )
	{
		if ($value['DeadlineStatus'] != $_LANG['DeadlineStatus'][2])
		{
			dbUpdateRow('buginfo','DeadlineStatus',"'{$_LANG[DeadlineStatus][2]}'", "BugID={$value[BugID]}");
		}
	}
	else if( $value['Deadline'] <= $mark_2 &&
			 $value['Deadline'] > $mark_3 )
	{
		if ($value['DeadlineStatus'] != $_LANG['DeadlineStatus'][3])
		{
			dbUpdateRow('buginfo','DeadlineStatus',"'{$_LANG[DeadlineStatus][3]}'", "BugID={$value[BugID]}");
		}
	}
	else if( $value['Deadline'] <= $mark_3 &&
			 $value['Deadline'] > $mark_4 )
	{
		if ($value['DeadlineStatus'] != $_LANG['DeadlineStatus'][4])
		{
			dbUpdateRow('buginfo','DeadlineStatus',"'{$_LANG[DeadlineStatus][4]}'", "BugID={$value[BugID]}");
		}
	}
	else if( $value['Deadline'] <= $mark_4 )
	{
		if ($value['DeadlineStatus'] != $_LANG['DeadlineStatus'][5])
		{
			dbUpdateRow('buginfo','DeadlineStatus',"'{$_LANG[DeadlineStatus][5]}'", "BugID={$value[BugID]}");
		}
	}
}

?>
