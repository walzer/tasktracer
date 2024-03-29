<?php
/**
 * TaskTracer is free software under the terms of the FreeBSD License.
 *
 * chinese language file.
 *
 * @link        http://www.walzer.cn
 * @package     TaskTracer
 */
/* Init TaskTracer system. */
$_LANG['Charset'] = 'UTF-8';

/* The error message to display when a user not logged in. */
$_LANG['Message'] =  array
(
    'NotLogin'     => '您还没有登录到系统。\\n请首先登录 TaskTracer!',
    'ErrorLogin'   => '您的用户名或密码不正确。请重新输入!',
    'NoPriv'       => "您现在没有访问任何项目的权限，<br />请联系系统管理员为您分配权限。",
    "SucceedLogin" => "登录成功"
);

$_LANG['SysErrorMsg'] = '请求的URL没有找到或者无权限访问。';

/* Settting for DB */
$_LANG['DBErrorConnection']    = '数据库连接失败！';
$_LANG['DBSureExists']         = '请确认是否存在数据库';
$_LANG['DBSureUserNameAndPwd'] = '请确认数据库的用户名和密码是否正确。';
$_LANG['DBSureHost']           = '请确认数据库的服务器地址是否正确。';
$_LANG['DBSureRunning']        = '请确认数据库是否在运行。';

/* TaskTracer team info. Don't change. */
$_LANG['AboutBF']    = '关于';
$_LANG['BFHome']     = '官方网站';
$_LANG['BFHomePage'] = 'http://www.walzer.cn';
$_LANG['ProductName']= 'TaskTracer';
$_LANG['Version']    = '0.0.1';

/* Define css style list. */
$_LANG['StyleList'] = array
(
  'Default' => '默认风格',
);

$_LANG['DefaultStyle'] = 'Default';

$_LANG['UCAssignedToMe'] = '指派给我';
$_LANG['UCOpenedByMe']   = '由我创建';
$_LANG['UCMyQuery']      = '我的查询';

/* Define the length of the title to show in result list window and user control window. */
$_LANG['QueryTitleLength']   = 75;    // Used in QueryBug.php
$_LANG['ControlTitleLength'] = 36;    // Used in UserControl.php

/* Define overlib's value */
$_LANG['OLWIDTH']   = '300';
$_LANG['OLBGCOLOR'] = '#75736E';
$_LANG['OLFGCOLOR'] = '#F6F6F6';

/* And Or list. */
$_LANG['AndOr'] = array('And' => '并且', 'Or'  => '或者');

$_LANG['FieldTypeOperationName'] = array
(
    'Project'=> '等于,不等于,包含,不包含',
    'Number' => '等于,不等于,大于,小于,大于等于,小于等于,',
    'Date'   => '等于,不等于,大于,小于,大于等于,小于等于,',
    'String' => '包含,不包含',
    'People' => '等于,不等于',
    'MutilPeople' => '等于',
    'Option' => '等于,不等于',
    'Path'   => '包含,不包含,在某路径下,'
);

$_LANG['QueryCondition'] = '查询条件';
$_LANG['QueryTitle']     = '查询标题';
$_LANG['SaveQuery']      = '保存查询';
$_LANG['ResetQuery']     = '重置查询';
$_LANG['NoQueryTitle']   = '没有输入查询标题';
$_LANG['DuplicateQueryTitle'] = '查询标题已存在';
$_LANG['AddBuild'] = '新增 Build';

$_LANG['ConfirmDeleteTestFile']  = '确认删除该附件吗？';
$_LANG['ErrorExceedSize']        = '超过了附件的限制尺寸';
$_LANG['ErrorDangerousFileType'] = '是危险的文件类型';

/*-BASIC SETTING FOR BUG RELATED FEATURES -*/
/* The fields of BugInfo table. */
$_LANG['BugFields'] = array
(
    'BugID'          => '任务 ID',
    'ProjectID'      => '项目 ID',
    'ProjectName'    => '项目名称',
    'ModuleID'       => '子项 ID',
    'ModulePath'     => '子项路径',
    'BugTitle'       => '任务标题',
    'BugSeverity'    => '严重程度',
    'BugPriority'    => '优先级',
    'BugType'        => '任务类型',
    'BugOS'          => '操作系统',
	'BugScore'       => '分值',
	'FixQuality'  	 => '质量',
	'Deadline'		 => '预计完成时间',
	'DeadlineStatus' => '到期状态',
    'BugBrowser'     => '浏览器',
    'BugMachine'     => '机器配置',
    'HowFound'       => '如何发现',
    'ReproSteps'     => '解决步骤',
    'BugStatus'      => '任务状态',
    'BugSubStatus'   => '处理状态',
    'LinkID'         => '相关任务',
    'DuplicateID'    => '重复任务',
    'MailTo'         => '抄送给',
    'OpenedBy'       => '创建者',
    'OpenedDate'     => '创建日期',
    'OpenedBuild'    => '创建 Build',
    'AssignedTo'     => '指派给',
    'AssignedDate'   => '指派日期',
    'ResolvedBy'     => '解决者',
    'Resolution'     => '解决方案',
    'ResolvedBuild'  => 'SVN 版本',
    'ResolvedDate'   => '解决日期',
    'ModifiedBy'     => '曾经修改者',
    'ClosedBy'       => '关闭者',
    'ClosedDate'     => '关闭日期',
    'LastEditedBy'   => '最后修改者',
    'LastEditedDate' => '最后修改日期',
    'BugKeyword'     => '关键词',
    'CaseID'         => '相关模块',
    'ResultID'       => '相关测试点',
);

/* The fields used to query in QueryBug.php.(Note: the field will be displayed in the order you defined here). */
$_LANG['BugQueryField'] = array
(
   'BugID'          => $_LANG['BugFields']['BugID'],
   'BugTitle'       => $_LANG['BugFields']['BugTitle'],
   'ProjectName'    => $_LANG['BugFields']['ProjectName'],
   'ModulePath'     => $_LANG['BugFields']['ModulePath'],
   'BugStatus'      => $_LANG['BugFields']['BugStatus'],
	'BugScore'      => $_LANG['BugFields']['BugScore'],
	'FixQuality'	=> $_LANG['BugFields']['FixQuality'],
	'Deadline'		=> $_LANG['BugFields']['Deadline'],
	'DeadlineStatus' => $_LANG['BugFields']['DeadlineStatus'],
   'AssignedTo'     => $_LANG['BugFields']['AssignedTo'],
   'MailTo'         => $_LANG['BugFields']['MailTo'],
   'BugSeverity'    => $_LANG['BugFields']['BugSeverity'],
   'BugPriority'    => $_LANG['BugFields']['BugPriority'],
   'BugType'        => $_LANG['BugFields']['BugType'],
   'HowFound'       => $_LANG['BugFields']['HowFound'],
   'BugOS'          => $_LANG['BugFields']['BugOS'],
   'BugBrowser'     => $_LANG['BugFields']['BugBrowser'],
   'LastEditedBy'   => $_LANG['BugFields']['LastEditedBy'],
   'LastEditedDate' => $_LANG['BugFields']['LastEditedDate'],
   'OpenedBy'       => $_LANG['BugFields']['OpenedBy'],
   'OpenedDate'     => $_LANG['BugFields']['OpenedDate'],
   'OpenedBuild'    => $_LANG['BugFields']['OpenedBuild'],
   'ResolvedBy'     => $_LANG['BugFields']['ResolvedBy'],
   'ResolvedDate'   => $_LANG['BugFields']['ResolvedDate'],
   'ResolvedBuild'  => $_LANG['BugFields']['ResolvedBuild'],
   'Resolution'     => $_LANG['BugFields']['Resolution'],
   'DuplicateID'    => $_LANG['BugFields']['DuplicateID'],
   'ClosedBy'       => $_LANG['BugFields']['ClosedBy'],
   'ClosedDate'     => $_LANG['BugFields']['ClosedDate'],
   'BugSubStatus'   => $_LANG['BugFields']['BugSubStatus'],
   'BugMachine'     => $_LANG['BugFields']['BugMachine'],
   'BugKeyword'     => $_LANG['BugFields']['BugKeyword'],
   'LinkID'         => $_LANG['BugFields']['LinkID'],
   'CaseID'         => $_LANG['BugFields']['CaseID'],
   'ResultID'       => $_LANG['BugFields']['ResultID'],
   'ModifiedBy'     => $_LANG['BugFields']['ModifiedBy'],
);

$_LANG["DefaultBugQueryFields"] = array
(
   "BugID"        => $_LANG["BugFields"]["BugID"],
   "BugSeverity"  => $_LANG["BugFields"]["BugSeverity"],
   "BugPriority"  => $_LANG["BugFields"]["BugPriority"],
   "BugTitle"     => $_LANG["BugFields"]["BugTitle"],
   "OpenedBy"     => $_LANG["BugFields"]["OpenedBy"],
   "AssignedTo"   => $_LANG["BugFields"]["AssignedTo"],
   "ResolvedBy"   => $_LANG["BugFields"]["ResolvedBy"],
   "Resolution"   => $_LANG["BugFields"]["Resolution"],
   "LastEditedDate"   => $_LANG["BugFields"]["LastEditedDate"]
);

/* Bug Severity. */
$_LANG['BugSeveritys'] = array
(
   '' => '',
   1 => '1',
   2 => '2',
   3 => '3',
   4 => '4',
);

/* Bug Priority. */
$_LANG['BugPriorities'] = array
(
   '' => '',
   1 => '1',
   2 => '2',
   3 => '3',
   4 => '4',
);

/* Define the OS list. */
$_LANG['BugOS'] = array
(
    ''        => '',
    'All'     => '全部',
    'Win7'    => 'Windows 7',
    'WinVista'=> 'Windows Vista',
    'WinXP'   => 'Windows XP',
    'Win2000' => 'Windows 2000',
    'Linux'   => 'Linux',
    'FreeBSD' => 'FreeBSD',
    'Unix'    => 'Unix',
    'Others'  => '其他',
);

/* Define the Browser list. */
$_LANG['BugBrowser'] = array
(
 ''           => '',
 'All'        => '全部',
 'IE8'        => 'IE 8.0',
 'IE7'        => 'IE 7.0',
 'IE6'        => 'IE 6.0',
 'FireFox3.0' => 'FireFox 3.0',
 'FireFox2.0' => 'FireFox 2.0',
 'Opera'      => 'Opera',
 'Others'     => '其他',
);

/* Define the types. */
$_LANG['BugTypes'] = array
(
	''			=> '',
	'Develop' 	=> '开发制作',
	'BugFix'    => 'Bug修复',
	'Test'   	=> '测试评估',
	'Research'  => '方案预研',
	'Design' 	=> '需求设计',
	'Document' 	=> '文档编撰',
	'Other'   	=> '其他',
);

$_LANG['CannotOpenDevelopFromIntegration'] = '无法从模块组里建立任务';
$_LANG['CannotOpenTestFromIntegration'] = '无法从模块组里建立测试点';
/************
$_LANG['BugTypes'] = array
(
  ''             => '',
  'CodeError'    => '代码错误',
  'Interface'    => '用户界面',
  'DesignChange' => '需求变动',
  'NewFeature'   => '新增需求',
  'DesignDefect' => '设计文档',
  'Config'       => '配置相关',
  'Install'      => '安装部署',
  'Security'     => '安全相关',
  'Performance'  => '性能压力',
  'Standard'     => '标准规范',
  'Automation'   => '测试脚本',
  'TrackThings'  => '事务跟踪',
  'Others'       => '其他'
);
***************/

/* Define the status. */
$_LANG['BugStatus'] = array
(
    ''         => '',
    'Active'   => '解决中',
    'Resolved' => '审核中',
    'Closed'   => '已关闭'
);

/* Define the substatus. */
$_LANG['BugSubStatus'] = array
(
    ''              => '',
    'Hold'          => 'Hold',
    'LocalFix'      => 'Local Fix',
    'CheckedIn'     => 'Checked In',
    'CannotRegress' => 'Can\'t Regress'
);

/* Define the Resolution. */
$_LANG['BugResolutions'] = array
(
    ''             => '',
    'Fixed'        => '已解决',
    'By Design'    => '设计如此',
    'Duplicate'    => '重复的问题',
    'External'     => '外部原因造成',
    'Not Repro'    => 'BUG无法重现',
    'Postponed'    => '以后版本解决',
    'Will not Fix' => "无法修改"
);

/* Define the HowFound. */
$_LANG['BugHowFound'] = array
(
    ''             => '',
    'FuncTest'     => '功能测试',
    'SpecReview'   => '需求检查',
    'CodeReview'   => '代码检查',
    'UnitTest'     => '单元测试',
    'BVT'          => '版本验证测试',
    'Integrate'    => '集成测试',
    'System'       => '系统联调',
    'SmokeTest'    => '冒烟测试',
    'PostRTW'      => '上线遗漏',
    'BugBash'      => 'Bug Bash',
    'AdHoc'        => '随机测试',
    'Regression'   => '回归测试',
    'Customer'     => '客户反馈',
    'Partner'      => '合作伙伴',
    'Other'        => '其他',
);

/*-BASIC SETTING FOR BUG RELATED FEATURES -*/
/* The fields of BugInfo table. */
$_LANG['CaseFields'] = array
(
  'CaseID'            => '模块 ID',
  'ProjectID'         => '项目 ID',
  'ProjectName'       => '项目名称',
  'ModuleID'          => '子项 ID',
  'ModulePath'        => '子项路径',
  'CaseTitle'         => '模块标题',
  'CasePriority'      => '优先级',
  'CaseType'          => '模块类型',
  'CaseStatus'        => '模块状态',
  'CaseMethod'        => '测试方法',
  'CasePlan'          => '测试计划',
  'CaseSteps'         => '步骤',
  'OpenedBy'          => '创建者',
  'OpenedDate'        => '创建日期',
  'AssignedTo'        => '指派给',
  'AssignedDate'      => '指派日期',
  'LastEditedBy'      => '最后修改者',
  'LastEditedDate'    => '最后修改日期',
  'ModifiedBy'        => '曾经修改者',
  'ScriptStatus'      => '脚本状态',
  'ScriptedBy'        => '编写者',
  'ScriptedDate'      => '编写日期',
  'ScriptLocation'    => '脚本地址',
  'MailTo'            => '抄送给',
  'CaseKeyword'       => '关键词',
  'LinkID'            => '相关模块',
  'BugID'             => '相关任务',
  'ResultID'          => '相关测试例',
  'MarkForDeletion'   => '标记删除',
  'ScoreTotal'		  => '总分值',
  'ScoreResolved'	  => '已完成分值',
  'ScoreUnresolved'   => '未完成分值',
  'ResolvedPercent'   => '已完成百分比',
  'ParentID'		  => '父模块ID',
);

/* The fields used to query in QueryCase.php.(Note: the field will be displayed in the order you defined here). */
$_LANG['CaseQueryField'] = array
(
   'CaseID'          => $_LANG['CaseFields']['CaseID'],
   'CaseTitle'       => $_LANG['CaseFields']['CaseTitle'],
   'ProjectName'     => $_LANG['CaseFields']['ProjectName'],
   'ModulePath'      => $_LANG['CaseFields']['ModulePath'],
   'CaseStatus'      => $_LANG['CaseFields']['CaseStatus'],
   'AssignedTo'      => $_LANG['CaseFields']['AssignedTo'],
   'MailTo'          => $_LANG['CaseFields']['MailTo'],
   'CasePriority'    => $_LANG['CaseFields']['CasePriority'],
   'CaseType'        => $_LANG['CaseFields']['CaseType'],
   'CaseMethod'      => $_LANG['CaseFields']['CaseMethod'],
   'CasePlan'        => $_LANG['CaseFields']['CasePlan'],
   'LastEditedBy'    => $_LANG['CaseFields']['LastEditedBy'],
   'LastEditedDate'  => $_LANG['CaseFields']['LastEditedDate'],
   'OpenedBy'        => $_LANG['CaseFields']['OpenedBy'],
   'OpenedDate'      => $_LANG['CaseFields']['OpenedDate'],
   'ScriptStatus'    => $_LANG['CaseFields']['ScriptStatus'],
   'ScriptedBy'      => $_LANG['CaseFields']['ScriptedBy'],
   'ScriptedDate'    => $_LANG['CaseFields']['ScriptedDate'],
   'ScriptLocation'  => $_LANG['CaseFields']['ScriptLocation'],
   'MarkForDeletion' => $_LANG['CaseFields']['MarkForDeletion'],
   'CaseKeyword'     => $_LANG['CaseFields']['CaseKeyword'],
   'BugID'           => $_LANG['CaseFields']['BugID'],
   'LinkID'          => $_LANG['CaseFields']['LinkID'],
   'ResultID'        => $_LANG['CaseFields']['ResultID'],
   'ModifiedBy'      => $_LANG['CaseFields']['ModifiedBy'],
   'ScoreTotal'		 => $_LANG['CaseFields']['ScoreTotal'],
   'ScoreResolved'	 => $_LANG['CaseFields']['ScoreResolved'],
   'ScoreUnresolved' => $_LANG['CaseFields']['ScoreUnresolved'],
   'ResolvedPercent' => $_LANG['CaseFields']['ResolvedPercent'],
   'ParentID'        => $_LANG['CaseFields']['ParentID'],
);


$_LANG['DefaultCaseQueryFields'] = array
(
   'CaseID'          => $_LANG['CaseFields']['CaseID'],
   'CasePriority'    => $_LANG['CaseFields']['CasePriority'],
   'CaseTitle'       => $_LANG['CaseFields']['CaseTitle'],
   'CaseStatus'      => $_LANG['CaseFields']['CaseStatus'],
   'OpenedBy'        => $_LANG['CaseFields']['OpenedBy'],
   'AssignedTo'      => $_LANG['CaseFields']['AssignedTo'],
   'LastEditedDate'      => $_LANG['CaseFields']['LastEditedDate'],
);


/* Define the CasePriority */
$_LANG['CaseStatuses'] = array
(
 'Active'      => 'Active',
 'Blocked'     => 'Blocked',
 'Investigate' => 'Investigating',
 'Reviewed'    => 'Reviewed',
);

$_LANG['CasePriorities'] = array
(
 ''=>'',
 '1'=>'1',
 '2'=>'2',
 '3'=>'3',
 '4'=>'4',
);

$_LANG['CaseTypes'] = array
(
 ''              => '',
 'Functional'    => '功能',
 'Configuration' => '配置相关',
 'Setup'         => '安装部署',
 'Security'      => '安全相关',
 'Performance'   => '性能压力',
 'Other'         => '其他',
);

$_LANG['CaseMethods'] = array
(
 ''           => '',
 'Manual'     => '手动执行',
 'Automation' => '自动化脚本'
);

$_LANG['CasePlans'] = array
(
 ''           => '',
 'UnitTest'   => '单元测试',
 'BVT'        => '版本验证测试',
 'Function'   => '功能测试',
 'Intergrate' => '集成测试',
 'System'     => '系统联调',
 'Smoke'      => '冒烟测试',
);

$_LANG['ScriptStatuses'] = array
(
 ''              => '',
 'NotPlanned'    => '未计划',
 'Planning'      => '计划',
 'Blocked'       => '被阻止',
 'Coding'        => '正在编写',
 'CodingDone'    => '已完成',
 'Reviewed'      => '已评审',
);


$_LANG['MarkForDeletions'] = array
(
 '0' => '否',
 '1' => '是'
);

/* The fields of ResultInfo table. */
$_LANG['ResultFields'] = array
(
  'ResultID'      => '测试点 ID',
  'ProjectID'     => '项目 ID',
  'ProjectName'   => '项目名称',
  'ModuleID'      => '子项 ID',
  'ModulePath'    => '子项路径',
  'CaseID'        => '相关 CASE',
  'ResultTitle'   => '测试例标题',
  'ResultValue'   => '执行结果',
  'ResultStatus'  => '执行状态',
  'ResultSteps'   => '步骤',
  'OpenedBy'      => '创建者',
  'OpenedDate'    => '创建日期',
  'AssignedTo'    => '指派给',
  'MailTo'        => '抄送给',
  'AssignedDate'  => '指派日期',
  'LastEditedBy'  => '最后修改者',
  'LastEditedDate'=> '最后修改时间',
  'ModifiedBy'    => '曾经修改者',
  'ResultOS'      => '操作系统',
  'ResultBrowser' => '浏览器',
  'ResultMachine' => '机器配置',
  'ResultBuild'   => '运行 Build',
  'ResultKeyword' => '关键词',
  'BugID'         => '相关 任务',
);

/* The fields used to query in QueryCase.php.(Note: the field will be displayed in the order you defined here). */
$_LANG['ResultQueryField'] = array
(
   'ResultID'       => $_LANG['ResultFields']['ResultID'],
   'ResultTitle'    => $_LANG['ResultFields']['ResultTitle'],
   'ProjectName'    => $_LANG['ResultFields']['ProjectName'],
   'ModulePath'     => $_LANG['ResultFields']['ModulePath'],
   'ResultValue'    => $_LANG['ResultFields']['ResultValue'],
   'ResultStatus'   => $_LANG['ResultFields']['ResultStatus'],
   'AssignedTo'     => $_LANG['ResultFields']['AssignedTo'],
   'MailTo'         => $_LANG['ResultFields']['MailTo'],
   'LastEditedBy'   => $_LANG['ResultFields']['LastEditedBy'],
   'LastEditedDate' => $_LANG['ResultFields']['LastEditedDate'],
   'OpenedBy'       => $_LANG['ResultFields']['OpenedBy'],
   'OpenedDate'     => $_LANG['ResultFields']['OpenedDate'],
   'ResultBuild'    => $_LANG['ResultFields']['ResultBuild'],
   'ResultOS'       => $_LANG['ResultFields']['ResultOS'],
   'ResultBrowser'  => $_LANG['ResultFields']['ResultBrowser'],
   'ResultMachine'  => $_LANG['ResultFields']['ResultMachine'],
   'ResultKeyword'  => $_LANG['ResultFields']['ResultKeyword'],
   'CaseID'         => $_LANG['ResultFields']['CaseID'],
   'BugID'          => $_LANG['ResultFields']['BugID'],
   'ModifiedBy'     => $_LANG['ResultFields']['ModifiedBy'],
);

$_LANG['DefaultResultQueryFields'] = array
(
   'ResultID'       => $_LANG['ResultFields']['ResultID'],
   'ResultTitle'    => $_LANG['ResultFields']['ResultTitle'],
   'OpenedBy'       => $_LANG['ResultFields']['OpenedBy'],
   'AssignedTo'     => $_LANG['ResultFields']['AssignedTo'],
   'CaseID'         => $_LANG['ResultFields']['CaseID'],
   'ResultValue'    => $_LANG['ResultFields']['ResultValue'],
   'LastEditedDate' => $_LANG['ResultFields']['LastEditedDate'],
);


$_LANG['ResultStatuses'] = array
(
 'Completed'   => 'Completed',
 'Investigate' => 'Investigating',
 'Resolved'    => 'Resolved',
);

$_LANG['ResultOS']      = $_LANG['BugOS'];
$_LANG['ResultBrowser'] = $_LANG['BugBrowser'];
$_LANG['ResultMachine'] = $_LANG['BugMachine'];

$_LANG['ResultValues'] = array
(
 ''     => '',
 'Pass' => 'Passed',
 'Fail' => 'Failed',
);

/* nomal value. */
$_LANG['Confirm'] = '确定';
$_LANG['Cancle']  = '取消';

$_LANG['Edit']    = '编辑';
$_LANG['Save']    = '保存';
$_LANG['Delete']  = '删除';

$_LANG['AddTime']  = '添加时间';
$_LANG['AddedBy']  = '添加者';
$_LANG['LastTime'] = '修改时间';
$_LANG['LastModifiedBy'] = '修改者';

/* Define the Case Action  */
$_LANG['OpenCase'] = '新建 模块';
$_LANG['ViewCase'] = 'CASE';

/* Define the language about admin management */
$_LANG['ManageProject']    = '项目管理';
$_LANG['ManageModule']     = '子项管理';
$_LANG['ManageBugModule']  = '纵向任务分类管理';
$_LANG['ManageAffair']	   = '横向事务分类管理';
$_LANG['ManageCaseModule'] = '模块管理';
$_LANG['ManageUser']       = '用户管理';
$_LANG['ManageGroup']      = '用户组管理';
$_LANG['UserLog']          = '用户日志';

/* Project */
$_LANG['ProjectList']      = '项目列表';
$_LANG['BackToProjectList'] = '返回项目列表';
$_LANG['ProjectID']        = '项目 ID';
$_LANG['ProjectName']      = '项目名称';
$_LANG['AllProject']       = '所有项目';
$_LANG['AddProject']       = '添加项目';
$_LANG['GoOnAddPro']       = '继续添加';
$_LANG['SaveProject']      = '保存项目';
$_LANG['EditProject']      = '编辑项目';
$_LANG['DropProject']      = '禁用';
$_LANG['ConfirmDropProject'] = '确认禁用该项目？';
$_LANG['ActiveProject']    = '激活';
$_LANG['ConfirmActiveProject'] = '确认激活该项目？';
$_LANG['ProjectExist']     = '项目名称已被占用,请选择其它的名称';
$_LANG['NoProjectName']    = '没有输入项目名称';
$_LANG['NoEditPro']        = '没有任何改动';
$_LANG['SucceedEditPro']   = '编辑项目成功。' . htmlLink($_LANG['BackToProjectList'], 'AdminProjectList.php');
$_LANG['SucceedAddPro']    = '添加成功。';
$_LANG['ProjectGroup']     = '项目用户组';
$_LANG['ProjectManager']   = '项目管理员';
$_LANG['MergeProject']     = '合并项目';
$_LANG['MergeTo']          = '将项目合并至';
$_LANG['MergeName']        = '合并后的名称';
$_LANG['MergeSelfDenied']  = '项目不能与自己合并';
$_LANG['SucceedMergeProject'] = '合并项目成功';

/* Module */
$_LANG['ModuleList']       = '子项列表';
$_LANG['AddModule']        = '增加子项';
$_LANG['EditModule']       = '编辑子项';
$_LANG['DeleteModule']     = '删除子项';
$_LANG['ConfirmDeleteModule'] = '确认删除该子项？';
$_LANG['ModuleName']       = '子项名称';
$_LANG['OwnProject']       = '所属项目';
$_LANG['ParentModule']     = '父项';
$_LANG['DisplayOrder']     = '显示顺序';
$_LANG['DisplayOrderNote'] = '请输入0~255之间的一个整数';
$_LANG['ModuleOwner']      = '子项负责人';
$_LANG['SaveModule']       = '保存子项';
$_LANG['NoModuleName']     = '没有输入子项名称';
$_LANG['ModuleExist']      = '该模块名称已经被占用，请选择其他的名称';
$_LANG['ModuleNotBeRecursive'] = '子项不能成为自己的子模块或自己下级子项目的子模块';
$_LANG['ModuleHasChildModule'] = '该模块有下级子项，不能删除';
$_LANG['SucceedAddModule'] = '添加子项成功';
$_LANG['SucceedEditModule'] = '编辑子项成功';
$_LANG['SucceedDeleteModule'] = '删除子项成功';


/* User */
$_LANG['UserList']         = '用户列表';
$_LANG['BackToUserList']   = '返回用户列表';
$_LANG['UserID']           = '用户 ID';
$_LANG['UserName']         = '用户名';
$_LANG['RealName']         = '真实姓名';
$_LANG['Email']            = 'Email';
$_LANG['RawUserPassword']  = '原密码';
$_LANG['UserPassword']     = '密码';
$_LANG['RepeatUserPassword'] = '重复密码';
$_LANG['RealNameNote']     = '请输入指派用户显示的名称';
$_LANG['RawUserPasswordNote'] = '请输入用户首次登陆的默认密码';
$_LANG['EmailNote']        = '请输入邮件地址用于接受邮件通知';
$_LANG['WrongRawUserPassword'] = '原始密码错误';
$_LANG['PasswordNotEqual'] = '两次输入的密码不一致';
$_LANG['AddUser']          = '添加用户';
$_LANG['GoOnAddUser']      = '继续添加用户';
$_LANG['SaveUser']         = '保存用户';
$_LANG['EditUser']         = '编辑用户';
$_LANG['DropUser']         = '禁用';
$_LANG['AlreadyDropedUser'] = '该用户已禁用';
$_LANG['ActiveUser']       = '激活';
$_LANG['UserExist']        = '用户名已存在！';
$_LANG['NoUserName']       = '没有输入用户名';
$_LANG['NoRealName']       = '没有输入用户真实姓名';
$_LANG['NoEmail']          = '没有输入Email';
$_LANG['InvalidEmail']     = '无效的Email地址';
$_LANG['NoPassword']       = '没有输入密码';
$_LANG['NoEditUser']       = '没有任何改动';
$_LANG['SucceedEditUser']  = '编辑用户成功。' . htmlLink($_LANG['BackToUserList'], 'AdminUserList.php');
$_LANG['SuccessEditMyInfo'] = '您的用户信息已经更新';
$_LANG['SucceedAddUser']   = '添加用户成功。';
$_LANG['ConfirmDelUser']   = '确认禁用该用户？';
$_LANG['ConfirmActiveUser'] = '确认激活该用户？';

/* Group */
$_LANG['GroupList']        = '用户组列表';
$_LANG['BackToGroupList']  = '返回用户组列表';
$_LANG['GroupID']          = '用户组 ID';
$_LANG['GroupName']        = '用户组名';
$_LANG['GroupUser']        = '用户组用户';
$_LANG['GroupManager']     = '用户组管理员';
$_LANG['GroupACL']         = '用户组权限';
$_LANG['AddGroup']         = '添加用户组';
$_LANG['GoOnAddGroup']     = '继续添加用户组';
$_LANG['SaveGroup']        = '保存用户组';
$_LANG['EditGroup']        = '编辑用户组';
$_LANG['DropGroup']        = '删除用户组';
$_LANG['GroupNameExist']   = '用户组名已存在！';
$_LANG['GroupExist']       = '相同的用户组已存在！';
$_LANG['NoGroupName']      = '没有输入用户组名';
$_LANG['NoEditGroup']      = '没有任何改动';
$_LANG['SucceedEditGroup'] = '编辑用户组成功。' . htmlLink($_LANG['BackToGroupList'], 'AdminGroupList.php');
$_LANG['SucceedAddGroup']  = '添加用户组成功。';
$_LANG['ConfirmDelGroup']  = '确认删除该用户组？';
$_LANG['AllUserGroupName'] = '[All Users]';

/* User log */
$_LANG['UserLogList']      = '用户登录日志列表';
$_LANG['LogID']            = '日志 ID';
$_LANG['LoginIP']          = '登录 IP';
$_LANG['LoginTime']        = '登录时间';

/* Setting the header menu */
$_LANG['Welcome'] = '欢迎';
$_LANG['EditPer'] = '编辑我的信息';
$_LANG['Admin']   = '后台管理';
$_LANG['Logout']  = '退出';
$_LANG['Manual']  = '帮助';

/* Setting the upload file */
$_LANG['Accessories']  = '附件';
$_LANG['AttachFile']   = '上传附件';
$_LANG['FileShowName'] = '显示名称';


/*========================================Setting for single page=========================*/
/* Setting of Login.php */
$_LANG['LoginTitle']     = '欢迎使用 TaskTracer';
$_LANG['TestUserName']   = '用户名:';
$_LANG['TestUserPWD']    = '密&nbsp;&nbsp;&nbsp;码:';
$_LANG['ButtonLogin']    = '登录(L)';
$_LANG['SelectLanguage'] = '选择语言:';
$_LANG['SelectStyle']    = '选择风格:';
$_LANG['RememberMe']     = '记住密码';

/* Setting of UserControl.php */
$_LANG['ConfirmDelQuery'] = '确认删除这个查询？';


/*====================================Setting for bug, case, result=========================*/
$_LANG['PreButton']  = '上一个(P)';
$_LANG['NextButton'] = '下一个(N)';
$_LANG['SaveButton'] = '保存(S)';
$_LANG['Comments']   = '注释';

/*=====================================Setting for open, edit, fix bug====================*/
$_LANG['OpenBug']    = '新建任务';
$_LANG['ViewBug']    = 'TASK';
$_LANG['EditBug']    = '编辑任务';
$_LANG['ResolveBug'] = '解决任务';
$_LANG['CloseBug']   = '关闭任务';
$_LANG['ActiveBug']  = '激活任务';
$_LANG['Project']    = '项目';
$_LANG['Module']     = '子项';
$_LANG['BugTitle']   = '任务标题';
$_LANG['ReproSteps'] = '重现步骤';
$_LANG['DefaultReproSteps']['Step'] = '[步骤]';
$_LANG['DefaultReproSteps']['StepInfo'] = <<<EOT
1.
2.

EOT;
$_LANG['DefaultReproSteps']['Result']       = '[结果]';
$_LANG['DefaultReproSteps']['ExpectResult'] = '[期望]';
$_LANG['DefaultReproSteps']['Note']         = '[备注]';

$_LANG['BugID'] = '任务 ID';

$_LANG['NoBugTitle']        = '任务标题不能为空';
$_LANG['NoBugRepro']        = '解决步骤不能为空';
$_LANG['NoOpenBuild']       = '创建 Build 不能为空';
$_LANG['NoBugSeverity']     = '严重程度不能为空';
$_LANG['NoBugType']         = '任务类型不能为空';
$_LANG['NoHowFound']        = '如何发现不能为空';
$_LANG['NoDuplicateID']     = '请输入重复 任务 的编号';
$_LANG['NoResolvedBuild']   = 'SVN 版本不能为空';
$_LANG['NoResolution']      = '解决方案不能为空';
$_LANG['AlreadyResolved']   = '任务已经处于解决的状态';
$_LANG['AlreadyClosed']     = '任务已经处于关闭的状态';
$_LANG['AlreadyActive']     = '任务已经处于激活的状态';
$_LANG['BugAlreadyChanged'] = '该任务已经发生变更，请重新打开编辑。';

$_LANG['OpenBugButton']     = '新建(N)';
$_LANG['EditBugButton']     = '编辑(E)';
$_LANG['CopyBugButton']     = '复制(C)';
$_LANG['ResolveBugButton']  = '解决(R)';
$_LANG['CloseBugButton']    = '关闭(L)';
$_LANG['ActiveBugButton']   = '激活(A)';
$_LANG['BugStatusInfo']     = '任务 状态';
$_LANG['BugOpenedInfo']     = '新建';
$_LANG['BugResolvedInfo']   = '解决';
$_LANG['BugClosedInfo']     = '关闭';
$_LANG['BugConditionInfo']  = '任务相关';
$_LANG['BugOtherInfo']      = '其他信息';
$_LANG['BugHistoryInfo']    = '历史';
$_LANG['BugReproInfo']      = '解决步骤';
$_LANG['BugFiles']          = '附件';
$_LANG['MoreInfo']          = '更多信息';

/*=====================================Setting for add, edit case====================*/
$_LANG['AddCase']           = '新建模块';
$_LANG['NoCaseTitle']       = '模块标题不能为空';
$_LANG['NoCaseStep']        = '模块步骤不能为空';
$_LANG['NoCasePriority']    = '模块优先级不能为空';
$_LANG['NoCaseType']        = '模块类型不能为空';
$_LANG['NoCaseMethod']      = '模块测试方法不能为空';
$_LANG['BadScriptedDate']   = '日期不合法或日期格式无效（例如：2008-08-08）';
$_LANG['CaseAlreadyChanged']= '该模块已经发生变更，请重新打开编辑。';

$_LANG['CaseStatusInfo']    = '模块状态';

$_LANG['CaseOpenedInfo']    = '新建';
$_LANG['CaseReviewInfo']    = '评审';

$_LANG['AddCaseButton']     = '新建模块(N)';
$_LANG['EditCaseButton']    = '编辑(E)';
$_LANG['CopyCaseButton']    = '复制(C)';
$_LANG['RunCaseButton']     = '测试例(R)';
$_LANG['OpenTest']   		= '新建测试例(T)';
$_LANG['OpenDevelop']     	= '新建任务(D)';
$_LANG['RunCaseLink']       = '运行模块';

$_LANG['CaseFiles']         = '附件';
$_LANG['CaseMainInfo']      = '主要信息';
$_LANG['CaseActionInfo']    = '操作信息';
$_LANG['CaseStepsInfo']     = '步骤';
$_LANG['CaseConditionInfo'] = '模块相关';
$_LANG['CaseOtherInfo']     = '其他信息';
$_LANG['CaseDefaultStepInfo'] = <<<EOT
[步骤]
1.
2.

[验证]

[备注]

EOT;

$_LANG['CaseAutomationInfo'] = '自动化脚本';
/*=====================================Setting for add, edit result====================*/
$_LANG['OpenResult']         = '新建 TEST';
$_LANG['ViewResult']		 = 'TEST';
$_LANG['RunCaseStep']        = '运行';
$_LANG['EditResult']         = '编辑测试点';
$_LANG['EditResultButton']   = '编辑(E)';
$_LANG['OpenBugButton']      = '新建任务(B)';
$_LANG['ResultActionInfo']   = '操作信息';
$_LANG['ResultFiles']        = '附件';

$_LANG['NoResultValue']      = '没有选择执行结果';
$_LANG['NoResultSteps']      = '没有 任何 测试 步骤';
$_LANG['NoResultBuild']      = '没有 输入 测试例 的运行 Build 版本';
$_LANG['ResultAlreadyChanged'] = '该测试例已经发生变更，请重新打开编辑。';
$_LANG['ResultStatusInfo']   = '任务状态';
$_LANG['ResultEnvInfo']      = '运行环境';
$_LANG['ResultOpenedInfo']   = '新建';
$_LANG['ResultMainInfo']     = '主要信息';
$_LANG['ResultActionInfo']   = '操作信息';
$_LANG['ResultStepsInfo']    = '步骤';
$_LANG['ResultConditionInfo'] = '测试例相关';
$_LANG['ResultOtherInfo']    = '其他信息';


$_LANG['NotSaveMod'] = '如果您离开本页面，您所做的更改将会丢失。';

/*=====================================Setting for pagination===========================*/
$_LANG['Pagination']['FirstPage']   = '首页';
$_LANG['Pagination']['EndPage']     = '尾页';
$_LANG['Pagination']['PrePage']     = '上一页';
$_LANG['Pagination']['NextPage']    = '下一页';
$_LANG['Pagination']['TotalRecord'] = '记录总数:';
$_LANG['Pagination']['NumPerPage']  = '项条页';
$_LANG['Pagination']['PageNum']     = '页码:';
$_LANG['Pagination']['NoResult']    = '无结果！';
$_LANG['Pagination']['Result']      = '结果';


/*=====================================Setting for list caption==========================*/
$_LANG['CustomDisplay']     = '自定义显示';
$_LANG['AllFieldsTitle']    = '可选字段';
$_LANG['FieldsToShowTitle'] = '要显示的字段';
$_LANG['FieldsToShowBTN']   = '确定(O)';
$_LANG['FieldsDefaultBTN']  = '默认字段(D)';
$_LANG['ExportHtmlTable']   = '全部导出';
$_LANG['ReportForms']       = '统计报表';
$_LANG['OpenedBugsInLastWeek'] = '上周新建的 Bug';
$_LANG['StaleBugsForOneWeek']  = '超过一周未处理的 Bug';

/*=====================================Setting for edit my info=========================*/
$_LANG['EditMyInfoButton'] = '提交(E)';


/*=====================================Setting for install===================*/
$_LANG['InstallBugFree']      = 'TaskTracer安装程序';
$_LANG['InstallChoose']       = '请选择';
$_LANG['InstallNextButton']   = '下一步';
$_LANG['InstallButton']       = '开始安装';
$_LANG['UpgradeButton']       = '开始升级';
$_LANG['InstallNewBugFree']   = '安装全新的 TaskTracer';
$_LANG['UpgradeBugFreeFrom1'] = '从现有的 TaskTracer 升级';
$_LANG['UpgradeBugFreeFrom2'] = '从现有的 TaskTracer 升级';
$_LANG['UpgradeBugFree']      = '升级 TaskTracer';
$_LANG['UpgradeNotice']       = '升级前请先备份 TaskTracer 的数据库、上传的附件和程序';
$_LANG['UpgradeLanguage']     = '所要升级的 TaskTracer 的版本语言';
$_LANG['InstallStep1']        = '1、设置存储Bug数据的数据库参数<br />TaskTracer自身有一个简单的用户验证表，您可以使用它来管理用户，这时您可以跳过第 2 项的设置。 如果您的应用环境中已经有了第三方的PHP应用程序，比如论坛等等， 您也可以选择使用它们的用户验证表进行验证，这时您需要对第 2 项进行配置。';
$_LANG['InstallStep2']        = '2、设置用户验证表所在的数据库';
$_LANG['InstallStep3']        = '3、TaskTracer邮件功能参数配置<br /> 如果你选定的发信方式为SMTP方式，需要设置smtp服务器的地址，如果smtp服务器需要验证，还需要设定用户名和密码';
$_LANG['InstallStep4']        = '4、其他配置';
$_LANG['InstallStep5']        = '5、成为管理员';
$_LANG['InstallDBHost']       = '数据库服务器地址';
$_LANG['InstallDBUser']       = '数据库用户名';
$_LANG['InstallDBPassword']   = '数据库密码';
$_LANG['InstallDBDatabase']   = 'TaskTracer 数据表所在的数据库名';
$_LANG['InstallTablePrefix']  = 'TaskTracer 数据表的前缀名称';
$_LANG['InstallCreateDB']     = '创建数据库';
$_LANG['InstallUserDBHost']   = '数据库服务器地址';
$_LANG['InstallUserDBUser']   = '数据库用户名';
$_LANG['InstallUserDBPassword'] = '数据库密码';
$_LANG['InstallUserDBDatabase'] = '用户数据表所在的数据库名';
$_LANG['InstallTableName']     = '用户验证表名';
$_LANG['InstallUserName']      = '用户名对应的字段名';
$_LANG['InstallRealName']      = '真实姓名对应的字段名';
$_LANG['InstallUserPassword']  = '密码对应的字段名';
$_LANG['InstallEmail']         = 'Email对应的字段名';
$_LANG['InstallEncryptType']   = '密码加密方式';
$_LANG["InstallFromAddress"]   = "TaskTracer 以哪个邮箱地址进行发信";
$_LANG["InstallFromName"]      = "TaskTracer 以什么称呼进行发信";
$_LANG["InstallSendMethod"]    = "TaskTracer 自动发信的方式";
$_LANG["InstallSmtpHost"]      = "SMTP 服务器地址";
$_LANG["InstallSmtpAuth"]      = "SMTP 服务器是否需要验证";
$_LANG["InstallSmtpUserName"]  = "SMTP 服务器用户名";
$_LANG["InstallSmtpPassword"]  = "SMTP 服务器密码";
$_LANG["InstallUploadDirectory"]   = "上传附件存放目录，此目录必须可读可写";
$_LANG["InstallMaxFileSize"]       = "上传附件最大允许尺寸，单位字节";
$_LANG["InstallAdminUserName"]     = "管理员用户名";
$_LANG["InstallAdminRealName"]     = "管理员称呼";
$_LANG["InstallAdminUserEmail"]    = "管理员邮箱";
$_LANG["InstallAdminUserPassword"] = "管理员密码";
$_LANG["SampleBugInfo"]["BugTitle"] = "Sample：欢迎使用TaskTracer！";
$_LANG["SampleBugInfo"]["ReproSteps"] = <<<EOT
这是一个Bug的例子。
请先访问后台管理页面[url]Admin[/url]：
1. 在用户管理页添加用户；
2. 在用户组管理页添加新的用户组,并指派添加的用户；
3. 在项目管理页添加新的项目，指定访问项目的用户组；

更多有关TaskTracer的使用帮助，请访问 [url]http://www.walzer.cn[/url]。
EOT;
$_LANG["InstallSuccessed"] = <<<EOT
安装成功！<br /><br />
默认管理员帐号：admin<br />
默认管理员密码：123456<br /><br />
请点击<a href="Login.php" target="_self"><b>这里</b></a>登录。登录后，请立即修改默认密码。<br />
EOT;
$_LANG["UpgradeSuccessed"] = '升级成功，请点击<a href="Login.php" target="_self"><b>这里</b></a>登录。';
$_LANG["InstallErrorNoMysqlModule"]   = "没有安装Mysql模块！";
$_LANG["InstallErrorBugDB"]           = "TaskTracer 数据库验证失败: ";
$_LANG["InstallErrorPrefixNeeded"]    = "为创建新表，需要在配置文件中指定数据表前缀。<br />例：\$_CFG['DB']['TablePrefix'] = 'bf_';";
$_LANG["InstallErrorCreateBugDB"]     = "TaskTracer 数据库创建失败: ";
$_LANG["InstallErrorUserDB"]          = "用户数据库验证失败: ";
$_LANG["InstallErrorUserTable"]       = "用户验证表: ";
$_LANG["InstallErrorWritable"]        = "必须可读可写可执行(o=rwx)";
$_LANG["InstallErrorSmtpAuth"]        = "Smtp服务器需要验证，必须指定用户名和密码";
$_LANG["InstallErrorAdminUserInfo"]   = "请指定管理员的用户名、Email、密码";
$_LANG["InstallErrorCreateAdminUser"] = "管理员创建失败";
$_LANG["InstallErrorDBExists"]        = "数据库已经存在";
$_LANG["InstallErrorAlreadyNewest"]   = "已经是最新版本的TaskTracer。<br />点击<a href='Login.php' target='_self'><b>这里</b></a>登录。";
$_LANG["CreateDBSuccessed"]           = '创建数据库成功！请点击<a href="install.php" target="_self"><b>继续安装</b></a>';

/*=====================================Setting for notice===============================*/
$_LANG['NoticeBugSubject'] = '目前为止指派给您的 Bug 数：';

/*=====================================Setting for report================================*/
$_LANG['ViewReportBTN'] = '查看统计';
$_LANG['SelectAllBTN']  = '全选';
$_LANG['SelectNoneBTN'] = '全不选';
$_LANG['Others']        = '其它';
$_LANG['TotalCount']    = '总计';
$_LANG['Blank']         = '[空白]';
$_LANG['ReportTimeRange'] = array
(
    'All'       => '所有时间',
    'LastWeek'  => '上周',
    'ThisMonth' => '本月',
    'ThisYear'  => '今年',
    'Custom'    => '自定义时间',
);

$_LANG['ReportBugCount'] = '任务 数量';
$_LANG["BugReportType"] =  array
(
	"ResolvedScoresPerUser" => "完成分值按人分布",
	"AssignedScoresPerUser" => "未完成分值按人分布",
	"ResolvedPercentPerUser" => "完成百分比按人分布",
	"ResolvedScoresPerDay"  => "完成分值按日分布",
	"ResolvedScoresQualityPerUser"  => "个人分值质量加权统计",
	"ResolvedScoresQualityPerDay"   => "分值质量加权按日分布",
	"AvgQualityPerUser"      => "个人平均质量",
    "BugsPerProject"     => "任务 项目分布",
    "BugsPerModule"      => "任务 子项分布",
    "BugsPerStatus"      => "任务 状态分布",
	"BugScorePerStatus"  => "任务 状态按分值分布",
    "BugsPerSeverity"    => "任务 严重程度分布",
    "BugsPerPriority"    => "任务 优先级分布",
    "BugsPerType"        => "任务 类型分布",
    "BugsPerHowFound"    => "任务 如何发现分布",
    "BugsPerOS"          => "任务 操作系统分布",
    "BugsPerBrowser"     => "任务 浏览器分布",
    "OpenedBugsPerUser"  => "任务 创建者分布",
    "OpenedBugsPerDay"   => "任务 创建日期分布（按日）",
    "OpenedBugsPerWeek"  => "任务 创建日期分布（按周）",
    "OpenedBugsPerMonth" => "任务 创建日期分布（按月）",
    "ResolvedBugsPerUser"=> "任务 解决者分布",
    "BugsPerResolution"  => "任务 解决方案分布",
    "ResolvedBugsPerDay" => "任务 解决日期分布（按日）",
    "ResolvedBugsPerWeek"=> "任务 解决日期分布（按周）",
    "ResolvedBugsPerMonth"=> "任务 解决日期分布（按月）",
    "ClosedBugsPerUser"  => "任务 关闭者分布",
    "ClosedBugsPerDay"   => "任务 关闭日期分布（按日）",
    "ClosedBugsPerWeek"  => "任务 关闭日期分布（按周）",
    "ClosedBugsPerMonth" => "任务 关闭日期分布（按月）",
    "BugLiveDays"        => "任务 处理时间分布",
    "AssignedBugsPerUser"=> "任务 被指派人分布",
    "BugHistorys"        => "任务 处理步骤分布",
);


$_LANG['ReportCaseCount'] = '模块 数量';
$_LANG["CaseReportType"] =  array
(
	"CasesByPercent"		=> "模块 完成百分比",
	"CasesByScoreTotal"   => "模块 总分列表",
	"CasesByScoreUnresolved" => "模块 未完成分值",
    "CasesPerProject"     => "模块 项目分布",
    "CasesPerModule"      => "模块 子项分布",
    "CasesPerStatus"      => "模块 状态分布",
    "CasesPerPriority"    => "模块 优先级分布",
    "CasesPerType"        => "模块 类型分布",
    "CasesPerMethod"      => "模块 测试方法分布",
    "CasesPerPlan"        => "模块 测试计划分布",
    "OpenedCasesPerUser"  => "模块 创建者分布",
    "OpenedCasesPerDay"   => "模块 创建日期分布（按日）",
    "OpenedCasesPerWeek"  => "模块 创建日期分布（按周）",
    "OpenedCasesPerMonth" => "模块 创建日期分布（按月）",
    "CasePerScriptStatus" => "模块 脚本状态分布",
    "CasesPerScriptedBy"  => "模块 脚本编写者分布",
    "ScriptedDatePerDay"  => "模块 脚本编写时间分布（按日）",
    "ScriptedDatePerWeek" => "模块 脚本编写时间分布（按周）",
    "ScriptedDatePerMonth" => "模块 脚本编写时间分布（按月）",
);

$_LANG['ReportResultCount'] = '测试例 数量';
$_LANG["ResultReportType"] =  array
(
    "ResultsPerProject"     => "测试例 项目分布",
    "ResultsPerModule"      => "测试例 子项分布",
    "ResultsPerValue"       => "测试例 执行结果分布",
    "ResultsPerStatus"      => "测试例 执行状态分布",
    "OpenedResultsPerUser"  => "测试例 创建者分布",
    "OpenedResultsPerDay"   => "测试例 创建日期分布（按日）",
    "OpenedResultsPerWeek"  => "测试例 创建日期分布（按周）",
    "OpenedResultsPerMonth" => "测试例 创建日期分布（按月）",
    "ResultsPerOS"          => "测试例 操作系统分布",
    "ResultsPerBrowser"     => "测试例 浏览器分布",
);


/* -------------------------- Walzer Add Affiar List here -------------------------*/
$_LANG["ModuleScore"]	= '模块分值';
$_LANG["Affair"] = array
(
	"title"			=> "事务-理想工时计算表",
	"scope" 	 	=> "规模度",
	"complexity" 	=> "复杂度",
	"unlisted_score" => "未列事务分值",
);

$_LANG["AdminAffair"] = array
(
	"title1"		=> "新增/编辑/删除事务",
	"title2"		=> "新增/编辑/删除 规模度-复杂度矩阵",
	"AffairID"		=> "事务ID",
	"ParentID"		=> "所属大类ID",
	"ModuleID"		=> "所属的任务模块",
	"AffairName"	=> "事务名称",
	"Manhour"		=> "理想工时",
	"ActionType"	=> "操作方式",
	"New"			=> "新增",
	"Edit"			=> "修改",
	"Delete"		=> "删除",
	"Submit"		=> "提交",
	"ShowMatrix"    => "设置规模度-复杂度矩阵",
	"HideMatrix"	=> "隐藏规模度-复杂度矩阵",
	"AddRow"		=> "追加一行",
	"AddCol"		=> "追加一列",
	"DelRow"		=> "删除一行",
	"DelCol"		=> "删除一列",
	"TableTitle"	=> "复杂度/规模度",
	"CopyMatrix"	=> "复用其他事务的矩阵",
	"CreateMatrix"	=> "新建矩阵",
	"EditMatrix"	=> "修改矩阵",
	"DeleteMatrix"	=> "删除矩阵",
	"MatrixDestAffairID" => "目标事务ID",
	"CopyFromAffairID" => "从哪个事务ID中复用",
	"Description"	=> "文字描述",
);

$_LANG["DeadlineStatus"] = array
(
	"1"		=> "进行中(>3d)",
	"2"		=> "即将到期(1~3d)",
	"3"		=> "已到期(-1~1d)",
	"4"		=> "延误(-3~-1d)",
	"5"		=> "严重延误(-3d~)",
);

$_LANG["TopNav"] = array
(
	"Bug"	=> "任务",
	"TestCase"	=>	"模块",
	"TestResult"	=> "测试例",
);

$_CFG['ZeroTime'] = '0000-00-00 00:00:00';
$_CFG['NotForceDownloadFileType'] = array('png','gif','jpg','bmp');

$_CFG['FCFSWFType'] = array
(
   'Area2D'     => 'FCF_Area2D.swf',
   'Bar2D'      => 'FCF_Bar2D.swf',
   'Column2D'   => 'FCF_Column2D.swf',
   'Column3D'   => 'FCF_Column3D.swf',
   'Doughnut2D' => 'FCF_Doughnut2D.swf',
   'Line'       => 'FCF_Line.swf',
   'Pie3D'      => 'FCF_Pie3D.swf',
);

$_CFG['ReportTypeSWF'] =  array
(
    "BugsPerProject"     => 'Pie3D',
    "BugsPerModule"      => 'Pie3D',
    "BugsPerStatus"      => 'Pie3D',
    "BugsPerSeverity"    => 'Pie3D',
    "BugsPerPriority"    => 'Pie3D',
    "BugsPerHowFound"    => 'Pie3D',
    "BugsPerOS"          => 'Pie3D',
    "BugsPerBrowser"     => 'Pie3D',
    "OpenedBugsPerUser"  => 'Bar2D',
    "OpenedBugsPerDay"   => 'Column2D',
    "OpenedBugsPerWeek"  => 'Column2D',
    "OpenedBugsPerMonth" => 'Column2D',
    "ResolvedBugsPerDay" => 'Line',
    "ResolvedBugsPerWeek"  => 'Line',
    "ResolvedBugsPerMonth" => 'Line',
    "ClosedBugsPerUser"   => 'Bar2D',
    "ClosedBugsPerDay"    => 'Column3D',
    "ClosedBugsPerWeek"   => 'Column3D',
    "ClosedBugsPerMonth"  => 'Column3D',
    "AssignedBugsPerUser" => 'Bar2D',
    "ResolvedBugsPerUser" => 'Bar2D',
    "BugsPerResolution"  => 'Bar2D',
    "BugsPerType"        => 'Bar2D',
    "BugLiveDays"        => 'Column2D',
    "BugHistorys"        => 'Column2D',
    "CasesPerProject"    => 'Pie3D',
    "CasesPerModule"     => 'Pie3D',
    "CasesPerStatus"     => "Pie3D",
    "CasesPerPriority"   => "Pie3D",
    "CasesPerType"       => "Pie3D",
    "CasesPerMethod"     => "Pie3D",
    "CasesPerPlan"       => "Pie3D",
    "OpenedCasesPerUser" => "Bar2D",
    "OpenedCasesPerDay"  => "Column2D",
    "OpenedCasesPerWeek" => "Column2D",
    "OpenedCasesPerMonth" => "Column2D",
    "CasePerScriptStatus" => "Bar2D",
    "CasesPerScriptedBy"  => "Bar2D",
    "ScriptedDatePerDay"  => "Column3D",
    "ScriptedDatePerWeek" => "Column3D",
    "ScriptedDatePerMonth" => "Column3D",
    "ResultsPerProject"    => "Pie3D",
    "ResultsPerModule"     => "Pie3D",
    "ResultsPerValue"      => "Pie3D",
    "ResultsPerStatus"     => "Pie3D",
    "OpenedResultsPerUser"  => "Bar2D",
    "OpenedResultsPerDay"   => "Column3D",
    "OpenedResultsPerWeek"  => "Column3D",
    "OpenedResultsPerMonth" => "Column3D",
    "ResultsPerOS"          => "Column2D",
    "ResultsPerBrowser"     => "Column2D",
	"ResolvedScoresPerUser" => "Bar2D",
	"ResolvedScoresPerDay"  => "Line",
	"ResolvedScoresQualityPerUser"  => "Bar2D",
	"ResolvedScoresQualityPerDay"   => "Column2D",
	"AvgQualityPerUser"      => "Bar2D",
	"AssignedScoresPerUser"  => "Bar2D",
	"ResolvedPercentPerUser" => "Bar2D",
	"CasesByPercent"			=> "Bar2D",
	"CasesByScoreTotal"   		=> "Bar2D",
	"CasesByScoreUnresolved" 	=> "Bar2D",
	"BugScorePerStatus"			=> "Pie3D",
);

$_CFG['ReportGraphOption']['Default'] = array
(
   'decimalPrecision'  => '0',
   'formatNumberScale' => '0',
   'rotateNames'       => '1',
   'basefontsize'      => '12',
   'formatNumber'      => '0',
);

$_CFG['ReportGraphOption']['Column2D'] = $_CFG['ReportGraphOption']['Default'] + array();
$_CFG['ReportGraphOption']['Column3D'] = $_CFG['ReportGraphOption']['Default'] + array();
$_CFG['ReportGraphOption']['Bar2D']    = $_CFG['ReportGraphOption']['Default'] + array();
$_CFG['ReportGraphOption']['Pie3D']    = $_CFG['ReportGraphOption']['Default'] + array
(
  'showPercentageValues'=>'0',
  'showNames'       =>'1',
  'showValues'      =>'0',
  'pieRadius'       =>"150",
  'pieYScale'       =>'60',
  'pieBorderAlpha'  =>'40',
  'pieFillAlpha'    =>'70',
  'pieSliceDepth'   =>'15',
  'chartLeftMargin' =>"0",
  'chartRightMargin'=>"0",
  'chartTopMargin'  =>"0"
);
$_CFG['ReportGraphOption']['Line'] = $_CFG['ReportGraphOption']['Default'] + array
(
  'showAlternateHGridColor' => '1',
  'AlternateHGridColor'     => 'ff5904',
  'divLineColor'            => 'ff5904',
  'divLineAlpha'            => '20',
  'alternateHGridAlpha'     => '5',
  'anchorRadius'            => '1',
);
$_CFG['ReportTypeGraphOption']['BugsPerProject'] = array
(
 'showNames' => 0,
);
$_CFG['ReportTypeGraphOption']['BugsPerModule'] = array
(
 'showNames' => 0,
);
$_CFG['ReportTypeGraphOption']['CasesPerProject'] = array
(
 'showNames' => 0,
);
$_CFG['ReportTypeGraphOption']['CasesPerModule'] = array
(
 'showNames' => 0,
);
$_CFG['ReportTypeGraphOption']['ResultsPerProject'] = array
(
 'showNames' => 0,
);
$_CFG['ReportTypeGraphOption']['ResultsPerModule'] = array
(
 'showNames' => 0,
);

/* Operators. */
$_CFG['FieldType'] = array
(
    'Project'=> 'ProjectName',
    'Number' => 'BugID,CaseID,ResultID,BugSeverity,BugPriority,CasePriority,DuplicateID,LinkID',
    'Date'   => 'LastEditedDate,OpenedDate,ResolvedDate,ClosedDate,ScriptedDate',
    'String' => 'BugTitle,OpenedBuild,ResolvedBuild,BugMachine,BugKeyword,CaseTitle,CaseKeyword,ResultTitle,ResultBuild,ResultMachine,ResultKeyword',
    'People' => 'AssignedTo,LastEditedBy,OpenedBy,ResolvedBy,ClosedBy,ScriptedBy',
    'MutilPeople' => 'MailTo,ModifiedBy',
    'Option' => 'BugStatus,BugSubStatus,BugType,HowFound,BugOS,BugBrowser,Resolution,CaseStatus,CaseType,CaseMethod,CasePlan,ScriptStatus,MarkForDeletion,ResultValue,ResultStatus,ResultOS,ResultBrowser',
    'Path'   => 'ModulePath'
);

$_CFG['FieldTypeOperation'] = array
(
    'Project'=> '=,!=,LIKE,NOT LIKE',
    'Number' => '=,!=,>,<,>=,<=',
    'Date'   => '=,!=,>,<,>=,<=',
    'String' => 'LIKE,NOT LIKE',
    'People' => '=,!=',
    'MutilPeople' => '=',
    'Option' => '=,!=',
    'Path'   => 'LIKE,NOT LIKE,UNDER'
);
?>
