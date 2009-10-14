<?php
/**
 * BugFree is free software under the terms of the FreeBSD License.
 *
 * Module functions library of BugFree system.
 *
 * @link        http://www.bugfree.org.cn
 * @package     BugFree
 */

//------------------------- BASE FUNCTIONS -----------------------------------//
/**
 * Get Current Mode: Bug, Case or Result
 *
 * @author                       Yupeng Lee<leeyupeng@gmail.com>
 */
function baseGetTestMode()
{
    if($_SESSION['TestMode'] == '')
    {
        $_SESSION['TestMode'] = 'Bug';
    }
    return $_SESSION['TestMode'];
}

/*================================User Function Start=================================*/

/**
 * Judge valid user.
 *
 * @author                        Chunsheng Wang <wwccss@263.net>
 * @global array                  the BugFree config array.
 * @global object                 the object of ADO class created in SetupBug.inc.php.
 * @param  string  $TestUserName  the user name used to login BugFree system.
 * @param  string  $TestUserPWD   the user password used to login BugFree system.
 */
function baseJudgeUser($TestUserName = '',$TestUserPWD = '', $Encrypt = true)
{
    global $_CFG;

    $DBName = !empty($_CFG['UserDB']) ? 'MyUserDB' : 'MyDB';

    global $$DBName;

    if($Encrypt)
    {
        $TestUserPWD = baseEncryptUserPWD($TestUserPWD, $TestUserName);
    }
    $Where = "{$_CFG[UserTable][UserName]} = '{$TestUserName}' AND {$_CFG[UserTable][UserPassword]} = '{$TestUserPWD}'";
    if($DBName == 'MyDB')
    {
        $Where .= " AND IsDroped = '0'";
    }
    $TestUserInfo = dbGetRow($_CFG[UserTable][TableName], "{$_CFG[UserTable][UserName]} AS UserName, {$_CFG[UserTable][RealName]} AS RealName, {$_CFG[UserTable][Email]} AS Email"
                                                        ,  $Where , $$DBName);
    return $TestUserInfo;
}

/**
 * Encrypt the password according to the EncryptType defined in Config.inc.php.
 *
 * @author                         Chunsheng Wang <wwccss@263.net>
 * @global  array                  config vars of BugFree system.
 * @param   string  $TestUserPWD   the password before encrypting.
 * @param   string  $TestUserName
 * @return  string                 encrypted password.
 */
function baseEncryptUserPWD($TestUserPWD, $TestUserName = '')
{
    global $_CFG;

    if($_CFG['UserTable']['EncryptType'] == 'md5')
    {
        $TestUserPWD = md5($TestUserPWD);
    }
    elseif($_CFG['UserTable']['EncryptType'] == 'mysqlpassword')
    {
        $TestUserPWD = "PASSWORD('{$TestUserPWD}')";
    }
    elseif($_CFG['UserTable']['EncryptType'] == 'discuzuc')
    {
        $DBName = !empty($_CFG['UserDB']) ? 'MyUserDB' : 'MyDB';
        global $$DBName;
        $TestUserInfo = dbGetRow($_CFG[UserTable][TableName], "{$_CFG[UserTable][UserName]} AS UserName,salt", "UserName = '{$TestUserName}'", $$DBName);
        $TestUserPWD= md5(md5($TestUserPWD).$TestUserInfo['salt']);
    }    
    else
    {
        $TestUserPWD = $TestUserPWD;
    }
    return $TestUserPWD;
}

/**
 * Judge User Login Status
 *
 * @author                         Yupeng Lee<leeyupeng@gmail.com>
 */
function baseJudgeUserLogin()
{
    global $_LANG, $_CFG;
    if($_SESSION['TestUserName'] == '')
    {
        if(isset($_COOKIE['BFUserName']) && $_COOKIE['BFUserName'] != '')
        {
            $TestUserName = strtolower($_COOKIE['BFUserName']);
            $TestUserPWD = $_COOKIE['BFUserPWD'];
            $TestUserInfo = baseJudgeUser($TestUserName, $TestUserPWD, false);
            if($TestUserInfo)
            {
                $_SESSION['TestUserACL'] = baseGetUserACL($TestUserName);
                $_SESSION['TestIsAdmin'] = baseJudgeIsAdmin($TestUserName);
                if(!$_SESSION['TestIsAdmin'])
                {
                    $_SESSION['TestIsProjectAdmin'] = baseJudgeIsAdmin($TestUserName, 'ProjectAdmin');
                }                
                if(empty($_SESSION['TestUserACL']) && !$_SESSION['TestIsAdmin'])
                {
                    jsAlert(str_replace('<br />', "", $_LANG['Message']['NoPriv']));
                    jsGoTo($_CFG["BaseURL"] . "/Login.php","top");
                    exit;
                }
                else
                {
                    @setcookie("BFUserName", $TestUserName, time()+1209600,BF_COOKIE_PATH);
                    @setcookie("BFUserPWD", $TestUserPWD, time()+1209600,BF_COOKIE_PATH);
                    @setcookie("BFRememberStatus", '1', time()+1209600,BF_COOKIE_PATH);
                    @setcookie("TestCurrentProjectID", $_COOKIE['TestCurrentProjectID'], time()+1209600,BF_COOKIE_PATH);

                    // register session
                    $_SESSION['TestUserName'] = $TestUserName;
                    $_SESSION['TestRealName'] = $TestUserInfo['RealName'];
                    $_SESSION['TestUserEmail'] = $TestUserInfo['Email'];

                    $_SESSION['TestUserACLSQL'] = 'ProjectID' . dbCreateIN(join(',', array_keys($_SESSION['TestUserACL'])));
                    $TestCurrentProjectID = $_COOKIE['TestCurrentProjectID'] != '' && isset($_SESSION['TestUserACL'][$_COOKIE['TestCurrentProjectID']]) ? $_COOKIE['TestCurrentProjectID'] : key($_SESSION['TestUserACL']);
                    testSetCurrentProject($TestCurrentProjectID);            
                }
            }
        }
    }
    else
    {
        return true;
    }
}

/**
 * Judge Adminuser Login Status
 *
 * @author                         Yupeng Lee<leeyupeng@gmail.com>
 *
 */
function baseJudgeAdminUserLogin($AdminLevel = 'SysAdmin,ProjectAdmin')
{
    global $_CFG;
    if(baseJudgeIsAdmin($_SESSION['TestUserName']) && preg_match('/SysAdmin/i', $AdminLevel))
    {
        return true;
    }
    elseif(baseJudgeIsAdmin($_SESSION['TestUserName'], 'ProjectAdmin') && preg_match('/ProjectAdmin/i', $AdminLevel))
    {
        return true;
    }
    else
    {
        sysErrorMsg();
    }
}

/**
 * Judge is admin
 *
 * @author                          Yupeng Lee<leeyupeng@gmail.com>
 * @param  string $UserName         UserName
 * @param  string $AdminType        Admin Type
 * @return bool                     Is admin or not
 *
 */
function baseJudgeIsAdmin($UserName, $AdminType = 'SysAdmin')
{
    global $_CFG;
    $UserName = strtolower($UserName);
    if($AdminType == 'SysAdmin')
    {
        if(in_array($UserName, $_CFG['AdminUser']))
        {
            return true;
        }
    }
    if($AdminType == 'ProjectAdmin')
    {
        $ProjectList = dbGetList('TestProject', '', "ProjectManagers LIKE '%,{$UserName},%'");
        if(count($ProjectList) > 0)
        {
            return true;
        }
        $GroupList = dbGetList('TestGroup', '', "AddedBy = '{$UserName}'");
        if(count($GroupList) > 0)
        {
            return true;
        }
        $GroupList = dbGetList('TestGroup', '', "GroupManagers LIKE '%,{$UserName},%'");
        if(count($GroupList) > 0)
        {
            return true;
        }
    }
    return false;
}


/**
 * Get the User's A C L.
 *
 * @author                         Yupeng Lee <leeyupeng@163.com>
 * @param   string  $TestUserName  UserName.
 * @return  string                 User's ACL.
 */
function baseGetUserACL($TestUserName)
{
    $ProjectList = testGetValidProjectList();
    $UserACL = array();
    $ProjectACL = array();
    $GroupIDList = array();
    $IsAdmin = false;
    if(baseJudgeIsAdmin($TestUserName))
    {
        $IsAdmin = true;
    }
    else
    {
        $GroupACL = testGetGroupList("GroupUser LIKE '%,{$TestUserName},%'");
        $GroupIDList = array('1' => '1');
        foreach($GroupACL as $GroupInfo)
        {
            $GroupIDList[$GroupInfo['GroupID']] = $GroupInfo['GroupID'];
        }
    }
    foreach($ProjectList as $ProjectID => $ProjectInfo)
    {
        if($IsAdmin)
        {
            $UserACL[$ProjectID] = 'All';
        }
        elseif(array_intersect($ProjectInfo['ProjectGroupList'], $GroupIDList))
        {
            $UserACL[$ProjectID] = 'All';
        }
    }
    return $UserACL;
}


/**
 * Get query string from group query post in SearchBug.php/SearchCase.php/SearchResult.php
 *
 * @author                          Yupeng Lee<leeyupeng@gmail.com>
 * @param  array   $PostQueryArray  Query array posted from BugSearchForm, CaseSearchForm or ResultSearchForm
 * @return string                   Query string for SQL
 */
function baseGetGroupQueryStr($PostQueryArray)
{
    global $_CFG;
    $FirstQueryGroup = array();
    $SecondQureyGroup = array();

    $FieldName = 'Field';
    $OperatorName = 'Operator';
    $ValueName = 'Value';
    $AndOrName = 'AndOr';

    for($I=0; $I<$_CFG['QueryFieldNumber']; $I++)
    {
        $WhereStr = ' ';

        if(ucfirst($PostQueryArray[$AndOrName.$I]) == 'And')
        {
            $WhereStr .= '1 AND ';
        }
        elseif(ucfirst($PostQueryArray[$AndOrName.$I]) == 'Or')
        {
            $WhereStr .= '1 OR ';
        }
        $TempQueryStr = baseGetFieldQueryStr($PostQueryArray[$FieldName.$I], $PostQueryArray[$OperatorName.$I], $PostQueryArray[$ValueName.$I]);

        if($TempQueryStr == '')
        {
            $WhereStr = '';
        }
        else
        {
            $WhereStr .= $TempQueryStr;
        }

        if($WhereStr != '')
        {
            $I%2 == 0 ? $FirstQueryGroup[] = $WhereStr : $SecondQureyGroup[] = $WhereStr;
        }
    }

    $FirstQueryStr = join('AND', $FirstQueryGroup);
    $SecondQueryStr = join('AND', $SecondQureyGroup);
    $FirstQueryStr == '' ? $FirstQueryStr = '1' : '';
    $SecondQueryStr == '' ? $SecondQueryStr = '1' : '';

    $QueryStr = '(' . $FirstQueryStr . ') ' . $PostQueryArray['AndOrGroup'] . ' (' . $SecondQueryStr . ')';

    return $QueryStr;
}

/**
 * Get query string with one field
 *
 * @author                          Yupeng Lee<leeyupeng@gmail.com>
 * @param  string  $FieldName       FieldName
 * @param  string  $OperatorName    =,<,>,<= eg.
 * @param  string  $FieldValue      FieldValue
 * @return string                   Query string for SQL
 */
function baseGetFieldQueryStr($FieldName, $OperatorName, $FieldValue)
{
    $QueryStr = '';
    if($FieldValue == '')
    {
        return $QueryStr;
    }

    if(ucfirst($FieldValue) == 'Null')
    {
        $FieldValue = '';
    }

    if($OperatorName == 'LIKE')
    {
        $QueryStr = "LIKE '%{$FieldValue}%' ";
    }
    elseif($OperatorName == 'NOT LIKE')
    {
        $QueryStr = "NOT LIKE '%{$FieldValue}%' ";
    }
    elseif($OperatorName == 'UNDER')
    {
        $QueryStr = "LIKE '{$FieldValue}%' ";
    }
    elseif($OperatorName == '!=')
    {
        if('ModifiedBy' == $FieldName)
        {
            $QueryStr = "NOT LIKE '%,{$FieldValue},%' ";
        }
        else
        {
            $QueryStr = "NOT " . dbCreateIN($FieldValue) . " ";
        }
    }
    elseif($OperatorName == '=')
    {
        if(preg_match('/date/i', $FieldName))
        {
            $QueryStr = sysStrToDateSql($FieldValue);
        }
        elseif('ModifiedBy' == $FieldName || 'MailTo' == $FieldName)
        {
            $QueryStr = "LIKE '%,{$FieldValue},%' ";
        }
        else
        {
            $QueryStr = $OperatorName . " '{$FieldValue}' ";
        }
    }
    else
    {
        $QueryStr = $OperatorName . " '{$FieldValue}' ";
    }

    $QueryStr = $FieldName . ' ' . $QueryStr;
    return $QueryStr;
}

/**
 * Compare the PreAppendName in array
 *
 * @author                          Yupeng Lee<leeyupeng@gmail.com>
 * @param  array   $a               array a
 * @param  array   $b               array b
 * @return bool                     equal or not
 */
function testCmpPreAppendName($a, $b)
{
    return strcmp($a["PreAppendName"], $b["PreAppendName"]);
}

/**
 * Get user list which are not droped
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 * @param  string  $Where    SQL condition
 * @param  string  $OrderyBy SQL order by
 * @param  string  $Limit    SQL limit: m, n
 * @param  string  $ListKey  the key of result array
 * @return array             UserList
 */
function testGetUserList($Where = '', $OrderBy = 'UserName ASC', $Limit = '', $ListKey = 'UserName')
{
    global $_CFG;

    $DBName = !empty($_CFG['UserDB']) ? 'MyUserDB' : 'MyDB';
    global $$DBName;

    global $_CFG;

    $Columns = "{$_CFG[UserTable][UserName]} AS UserName, {$_CFG[UserTable][RealName]} AS RealName, {$_CFG[UserTable][UserPassword]} AS UserPassword, {$_CFG[UserTable][Email]} AS Email";
    $Columns = !empty($_CFG['UserDB']) ? $Columns : 'UserID, ' . $Columns;
    if($Where != "")
    {
        $Where = !empty($_CFG['UserDB']) ? $Where : $Where . " AND IsDroped = '0'";
    }
    else
    {
        $Where = !empty($_CFG['UserDB']) ? $Where : "IsDroped = '0'";
    }

    $UserList = dbGetList($_CFG['UserTable']['TableName'], $Columns, $Where, '', $OrderBy, $Limit, 'UserName', $$DBName, $ListKey);
    foreach($UserList as $UserName => $UserInfo)
    {
        $LastRealName = $UserInfo['RealName'];
        $RealNamePrefix = sysSubStr($LastRealName, 1);
        $FirstLetter = $_CFG['PinYin'][$RealNamePrefix];
        if($FirstLetter == '')
        {
	        $FirstLetter = strtoupper(substr($LastRealName, 0, 1));
        }
        if(!preg_match('/[A-Z0-9]/',$FirstLetter))
        {
            $FirstLetter = strtoupper(substr($UserName, 0, 1));
        }
        if(!preg_match('/[A-Z0-9]/',$FirstLetter))
        {
            $FirstLetter = '*';
        }
        $UserList[$UserName]['PreAppendName'] = $FirstLetter . ' ' . $LastRealName;
    }
    uasort($UserList, 'testCmpPreAppendName');

    return $UserList;
}

/**
 * Get user list which are not droped
 *
 * @author                       Yupeng Lee<leeyupeng@gmail.com>
 * @param  string  $Where        SQL condition
 * @param  string  $OrderyBy     SQL order by
 * @param  string  $Limit        SQL limit: m, n
 * @param  string  $ListKey      the key of result array
 * @param  string  $ListValue    the value of result array
 * @return array   $TestUserList UserList
 */
function testGetOneDimUserList($Where = '', $OrderBy = 'RealName ASC', $Limit = '', $ListKey = 'UserName', $ListValue= 'RealName')
{
    $TempUserList = testGetUserList($Where, $OrderBy, $Limit, $ListKey);
    $TestUserList = array();
    foreach($TempUserList as $UserInfo)
    {
        $TestUserList[$UserInfo[$ListKey]] = $UserInfo[$ListValue];
    }
    return $TestUserList;
}

/**
 * Get user list which are not droped
 *
 * @author              Yupeng Lee<leeyupeng@gmail.com>
 * @param  string  $Where        SQL condition
 * @param  string  $OrderyBy     SQL order by
 * @param  string  $Limit        SQL limit: m, n
 * @return array   $TestUserList UserList
 */
function testGetAllUserList($Where = '', $OrderBy = '', $Limit = '')
{
    global $_CFG;

    $DBName = !empty($_CFG['UserDB']) ? 'MyUserDB' : 'MyDB';
    global $$DBName;

    $Columns = "{$_CFG[UserTable][UserName]} AS UserName, CONCAT(UPPER(LEFT({$_CFG[UserTable][UserName]},1)),': ',{$_CFG[UserTable][RealName]}) AS PreAppendName, {$_CFG[UserTable][RealName]} AS RealName, {$_CFG[UserTable][Email]} AS Email";
    $Columns = !empty($_CFG['UserDB']) ? $Columns : 'UserID, AddedBy, AddDate, LastEditedBy, LastDate, IsDroped, ' . $Columns;
    return dbGetList($_CFG['UserTable']['TableName'], $Columns, $Where, '', $OrderBy, $Limit, 'UserName', $$DBName);
}

/**
 * Get user info by username
 *
 * @author                    Yupeng Lee<leeyupeng@gmail.com>
 * @param    string $UserName
 * @return   array  $UserInfo
 */
function testGetUserInfoByName($UserName)
{
    global $_CFG;
    $UserInfo = array_pop(testGetUserList("{$_CFG[UserTable][UserName]}='{$UserName}'"));
    return $UserInfo;
}

/*======================================User Functions End====================================*/


/*=======================================Project Functions Start==============================*/
/**
 * Set current project
 *
 * @author              Yupeng Lee<leeyupeng@gmail.com>
 */
function testSetCurrentProject($ProjectID)
{
    $_SESSION['TestCurrentProjectID'] = $ProjectID;
    @setcookie('TestCurrentProjectID', $ProjectID, time()+1209600,BF_COOKIE_PATH);
}

/**
 * Get project list
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 * @param  string  $Where     SQL condition
 * @param  string  $OrderyBy  SQL ordery by
 * @param  string  $Limit     SQL limit
 * @return array              ProjectList
 */
function testGetProjectList($Where = '', $OrderBy = '', $Limit = '')
{
    $ProjectList = dbGetAllColumns('TestProject', $Where, '', $OrderBy, $Limit, 'ProjectID');
    foreach($ProjectList as $ProjectID => $ProjectInfo)
    {
        $ProjectList[$ProjectID]['ProjectManagerList'] = sysTrimExplode(',', $ProjectInfo['ProjectManagers']);
        $ProjectList[$ProjectID]['ProjectGroupList'] = sysTrimExplode(',', $ProjectInfo['ProjectGroupIDs']);
    }
    return $ProjectList;
}


/**
 * Get projects which aren't dropped
 *
 * @author              Yupeng Lee<leeyupeng@gmail.com>
 * @param   string  $Where  SQL condition
 * @return  array           Valid ProjectList
 */
function testGetValidProjectList($Where = '')
{
    $WhereIsNotDropped = "IsDroped = '0'";
    if($Where == '')
    {
        $Where = $WhereIsNotDropped;
    }
    else
    {
        $Where .= ' AND ' . $WhereIsNotDropped;
    }

    $OrderBy = 'DisplayOrder DESC';

    return testGetProjectList($Where, $OrderBy);
}

/**
 * Get project(not dropped) list's select html
 *
 * @author                             Yupeng Lee<leeyupeng@gmail.com>
 * @param   array  $ProjectListName
 * @param   int    $ProjectIDSelected  Slelected in select html
 * @param   string $Attrib             Html attrib
 * @return  string                     Html code
 */
function testGetValidProjectSelectList($ProjectListName, $ProjectIDSelected = '', $Attrib)
{
    if($ProjectIDSelected == '')
    {
        $ProjectID = $_SESSION['TestCurrentProjectID'];
    }
    $ProjectList = testGetValidProjectList($_SESSION['TestUserACLSQL']);
    $ProjectListSelect = htmlSelect($ProjectList, $ProjectListName, '', $ProjectIDSelected, $Attrib, 'ProjectID,ProjectName');
    return $ProjectListSelect;
}

/**
 * Get all project list's select html
 *
 * @author                             Yupeng Lee<leeyupeng@gmail.com>
 * @param   array  $ProjectListName
 * @param   int    $ProjectIDSelected  Slelected in select html
 * @param   string $Attrib             Html attrib
 * @return  string                     Html code
 */
function testGetAllProjectSelectList($ProjectListName, $ProjectIDSelected = '', $Attrib)
{
    if($ProjectIDSelected == '')
    {
        $ProjectID = $_SESSION['TestCurrentProjectID'];
    }
    $OrderBy = 'DisplayOrder DESC';
    $ProjectList = testGetProjectList('', $OrderBy);
    $ProjectListSelect = htmlSelect($ProjectList, $ProjectListName, '', $ProjectIDSelected, $Attrib, 'ProjectID,ProjectName');
    return $ProjectListSelect;
}

/**
 * Get project(not dropped) array, key:ProjectID value:ProjectName
 *
 * @author                             Yupeng Lee<leeyupeng@gmail.com>
 * @param   array  $Where              SQL condition
 * @return  array                      Project array
 */
function testGetValidSimpleProjectList($Where = '')
{
    $ProjectList = testGetValidProjectList($_SESSION['TestUserACLSQL']);
    $SimpleProjectList = array();
    foreach($ProjectList as $Key => $Value)
    {
        $SimpleProjectList[$Value['ProjectID']] = $Value['ProjectName'];
    }
    return $SimpleProjectList;
}

/**
 * Get UserList by ProjectID
 *
 * @author                           Yupeng Lee <leeyupeng@gmail.com>
 * @param   string  $ProjectIDs      ProjectID split by ,
 * @param   bool    $ContainNullUser Return array whith ''=>'' or not
 * @return  array
 */
function testGetProjectUserList($ProjectIDs, $ContainNullUser = false)
{
    $GroupList = testGetGroupList();
    $ProjectList = testGetProjectList('ProjectID ' . dbCreateIn($ProjectIDs));
    $ProjectUserList = array();
    
    foreach($ProjectList as $ProjectID => $ProjectInfo)
    {
        $GroupIDList = sysTrimExplode(',', $ProjectInfo['ProjectGroupIDs']);
        foreach($GroupIDList as $GroupID)
        {
            if(is_array($GroupList[$GroupID]['GroupUserList']))
            {
                $ProjectUserList += $GroupList[$GroupID]['GroupUserList'];
            }
        }
    }
    
    $UserCountList = @array_count_values($ProjectUserList);
    if(!is_array($UserCountList)) $UserCountList = array();
    foreach($ProjectUserList as $UserName => $RealName)
    {
        if($RealName == '')
        {
            $RealName = $UserName;
        }
        $LastRealName = $RealName;

        if($UserCountList[$RealName] > 1)
        {
            $LastRealName .= "[{$UserName}]";
        }
       $ProjectUserList[$UserName] = $LastRealName;
    }
    asort($ProjectUserList);
    if($ContainNullUser)
    {
        $ProjectUserList = array('Active'=>'Active') + $ProjectUserList;
    }

    return $ProjectUserList;
}

/**
 * Get UserNameList by ProjectID
 *
 * @author                           Yupeng Lee <leeyupeng@gmail.com>
 * @param   string  $ProjectIDs      ProjectID split by ,
 * @param   bool    $ContainNullUser Return array whith ''=>'' or not
 * @return  array
 */
function testGetProjectUserNameList($ProjectIDs, $ContainNullUser = false)
{
    $GroupList = testGetGroupList();
    $ProjectList = testGetProjectList('ProjectID ' . dbCreateIn($ProjectIDs));
    $ProjectUserList = array();
    
    foreach($ProjectList as $ProjectID => $ProjectInfo)
    {
        $GroupIDList = sysTrimExplode(',', $ProjectInfo['ProjectGroupIDs']);
        foreach($GroupIDList as $GroupID)
        {
            if(is_array($GroupList[$GroupID]['GroupUserNameList']))
            {
                $ProjectUserList += $GroupList[$GroupID]['GroupUserNameList'];
            }
        }
    }
    asort($ProjectUserList);
    if($ContainNullUser)
    {
        $ProjectUserList = array(''=>'') + $ProjectUserList;
    }

    return $ProjectUserList;
}

/**
 * Get current usernamelist who have the rights in $_SESSION['TestUserACL']
 *
 * @author                       Yupeng Lee <leeyupeng@gmail.com>
 * @param   bool  $PreAppend
 * @return  arary
 */
function testGetCurrentUserNameList($PreAppend = false)
{
    if($PreAppend)
    {
        return testGetProjectUserList(join(',',array_keys($_SESSION['TestUserACL'])));
    }
    return testGetProjectUserNameList(join(',',array_keys($_SESSION['TestUserACL'])));
}
/*=======================================Project Functions End==============================*/


/*=======================================Module Functions Start===============================*/

/**
 * Get module list
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ProjectID
 * @param   string $ModuleType Bug or Case
 * @return  array  $ModuleList
 */
function testGetModuleList($ProjectID, $ModuleType = 'Bug')
{
	// Walzer Add. 只取$ModuleType == BUG
	$ModuleType = 'Bug';
	
    $Where = "ProjectID = '{$ProjectID}' ";
    $Where .= "AND ModuleType = '{$ModuleType}' ";
    $OrderBy = "ModuleGrade DESC, DisplayOrder ASC, ModuleID DESC";

    $ModuleList = dbGetAllColumns('TestModule', $Where, '', $OrderBy, '', 'ModuleID');

    $TreeModuleList = array(0=>array());
    foreach($ModuleList as $ModuleID => $ModuleInfo)
    {
        $ParentID = $ModuleInfo['ParentID'];
        $ModuleInfo['ChildIDs'] =  $ModuleID;

        if(!isset($TreeModuleList[$ParentID]))
        {
            $TreeModuleList[$ParentID] = array();
        }
        if(!isset($TreeModuleList[$ModuleID]))
        {
            $TreeModuleList[$ParentID] = array($ModuleID => $ModuleInfo) + $TreeModuleList[$ParentID];
        }
        else
        {
            $ModuleInfo['ChildIDs'] .= ',' . join(',', array_keys($TreeModuleList[$ModuleID]));
            $TreeModuleList[$ParentID] = array($ModuleID => $ModuleInfo) + $TreeModuleList[$ModuleID] + $TreeModuleList[$ParentID];
            unset($TreeModuleList[$ModuleID]);
        }
    }

    return $TreeModuleList[0];
}


/**
 * Get project module List, including the root module '/'
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ProjectID
 * @param   string $ModuleType  Bug or Case
 * @return  array  $ModuleList
 */
function testGetProjectModuleList($ProjectID, $ModuleType = 'Bug')
{
    $ProjectInfo = array_pop(testGetProjectList("ProjectID = '{$ProjectID}'"));
    if($ModuleType == 'Result')
    {
        $ModuleType = 'Case';
    }

    $ParentID = '0';
    $LastModuleID = '0';
    $ModuleList = array('0' => array('ModuleID' => 0,
                        'ParentID' => '',
                        'ModuleName' => $ProjectInfo['ProjectName'],
                        'IDPathe' => '0',
                        'NamePath' => '/',
                        'IsLeaf' => true,
                        'IsLastLeaf' => true,
                        'ModuleGrade' => '0',
                        'ChildIDs' => '0'));

    $ProjectModuleList = testGetModuleList($ProjectID, $ModuleType);
    $ModuleList += $ProjectModuleList;
    foreach($ModuleList as $ModuleID => $ModuleInfo)
    {
        if($ModuleID == '0')
        {
            continue;
        }
        $ModuleList[$ModuleID]['IsLastLeaf'] = true;

        if($ParentID == $ModuleInfo['ParentID'])
        {
            $ModuleList[$LastModuleID]['IsLastLeaf'] = false;
        }

        $ParentID = $ModuleInfo['ParentID'];


        $ParentIDPath = $ModuleList[$ParentID]['IDPath'];
        $ParentNamePath = $ModuleList[$ParentID]['NamePath'];
        if($ParentIDPath == '')
        {
            $ParentIDPath = '0';
        }
        $ModuleList[$ModuleID]['IDPath'] = $ParentIDPath . ',' . $ModuleID;
        if($ParentNamePath == '/')
        {
            $ParentNamePath = '';
        }
        $ModuleList[$ModuleID]['NamePath'] = $ParentNamePath . '/' . $ModuleInfo['ModuleName'];
        $ModuleList[$ModuleID]['IsLeaf'] = true;
        if($ParentID != '')
        {
            $ModuleList[$ParentID]['IsLeaf'] = false;
            $ModuleList[$ParentID]['IsLastLeaf'] = false;
        }

        $ModuleList[$LastModuleID]['NextTreeModuleID'] = $ModuleID;
        $LastModuleID = $ModuleID;
    }

    return $ModuleList;
}

/**
 * Get project module tree menu html code
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ProjectID
 * @param   string $LinkUrl
 * @param   string $ModuleType  Bug or Case
 * @return  string
 */
function testGetTreeModuleList($ProjectID, $LinkUrl = '', $ModuleType = 'Bug')
{
    $ModuleList = testGetProjectModuleList($ProjectID, $ModuleType);

    $TreeMenuHtml = "<ul id='Tree_{$ProjectID}'>";
    foreach($ModuleList as $ModuleID => $ModuleInfo)
    {
        if($ModuleInfo['ModuleGrade'] - 0 < 1)
        {
            $TreeMenuHtml .= '<li class="OpenedNode">';
        }
        else
        {
            $TreeMenuHtml .= '<li>';
        }
        $TreeMenuHtml .=  "<a id='Child_{$ProjectID}_{$ModuleID}' href='{$LinkUrl}ProjectID={$ProjectID}&ModuleID={$ModuleID}&ChildModuleIDs={$ModuleInfo['ChildIDs']}' onclick='treeMenuChildAction(\"Tree_{$ProjectID}\",\"Child_{$ProjectID}_{$ModuleID}\");'>{$ModuleInfo[ModuleName]}</a>";
        if($ModuleInfo['IsLeaf'])
        {
            if($ModuleInfo['IsLastLeaf'])
            {
                $TreeMenuHtml .= str_repeat('</li></ul>', abs($ModuleInfo['ModuleGrade'] - $ModuleList[$ModuleInfo['NextTreeModuleID']]['ModuleGrade'] ));
            }
            else
            {
                $TreeMenuHtml .= '</li>';
            }
        }
        else
        {
            $TreeMenuHtml .= '<ul>';
        }
    }
    return $TreeMenuHtml;
}

/**
 * Get module list's select html
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ProjectID
 * @param   string $LinkUrl
 * @param   string $ModuleType  Bug or Case
 * @return  string
 */
function testGetSelectModuleList($ProjectID, $ModuleListName, $ModuleSelected = '', $Attrib ='', $ModuleType = 'Bug')
{
    $ModuleList = testGetProjectModuleList($ProjectID, $ModuleType);
    $ModuleIDSelected = '0';
    if(!empty($ModuleList[$ModuleSelected]))
    {
        $ModuleIDSelected = $ModuleSelected;
    }
    else
    {
        foreach($ModuleList as $ModuleID => $ModuleInfo)
        {
            if($ModuleInfo['ModuleName'] == $ModuleSelected)
            {
                $ModuleIDSelected = $ModuleID;
                break;
            }
        }
    }
    $ModuleSelectList = htmlSelect($ModuleList, $ModuleListName, '', $ModuleIDSelected, $Attrib, 'ModuleID,NamePath');
    return $ModuleSelectList;
}

/**
 * Get module info by moduleid
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param   int   $ModuleID
 * @return  array $ModuleInfo
 */
function testGetModuleInfo($ModuleID)
{
    $ModuleInfo = array_pop(dbGetAllColumns('TestModule', "ModuleID = '{$ModuleID}'"));

    if(empty($ModuleInfo))
    {
        $ModuleInfo['ModuleOwner'] = '';
    }
    return $ModuleInfo;
}

/**
 * Get module path by moduleid
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ModuleID
 * @return  string $ModulePath
 */
function testGetModulePath($ModuleID)
{
    if($ModuleID - 0 == 0)
    {
        return '/';
    }

    $ModuleInfo = testGetModuleInfo($ModuleID);
    $ModulePathArr[] = $ModuleInfo['ModuleName'];
    while($ModuleInfo['ModuleGrade']>1)
    {
        $ModuleInfo = testGetModuleInfo($ModuleInfo['ParentID']);
        $ModulePathArr[] = $ModuleInfo['ModuleName'];
    }
    return '/' . join('/', array_reverse($ModulePathArr));
}
/*=======================================Module Functions End===============================*/

/*=======================================Group Functions Start==============================*/

/**
 * Get group list
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 * @param   string $Where    SQL condition
 * @param   string $OrderyBy SQL order by
 * @param   string $Limit    SQL limit m,n
 * @return  array  $GroupList
 */
function testGetGroupList($Where = '', $OrderBy = '', $Limit = '')
{
    $GroupList = dbGetAllColumns('TestGroup', $Where, '', $OrderBy, $Limit, 'GroupID');
    $UserList = testGetUserList();
    
    foreach($GroupList as $Key => $GroupInfo)
    {
        $GroupUserList = explode(',', $GroupInfo['GroupUser']);
        $GroupList[$Key]['GroupUserList'] = array();
        $GroupList[$Key]['GroupUserNameList'] = array();
        
        $GroupManagerList = explode(',', $GroupInfo['GroupManagers']);
        $GroupList[$Key]['GroupManagerList'] = array();
        $GroupList[$Key]['GroupManagerNameList'] = array();
        if($GroupInfo['GroupID'] == '1')
        {
            foreach($UserList as $UserName => $UserInfo)
            {
                $LastRealName = $UserInfo['PreAppendName'];
                $GroupList[$Key]['GroupUserList'][$UserName] = $LastRealName;
                $GroupList[$Key]['GroupUserNameList'][$UserName] = $UserList[$UserName]['RealName'];
            }
        }
        else
        {
            foreach($GroupUserList as $UserName)
            {
                if($UserName != '')
                {
                    $LastRealName = $UserList[$UserName]['PreAppendName'];
                    $GroupList[$Key]['GroupUserList'][$UserName] = $LastRealName;
                    $GroupList[$Key]['GroupUserNameList'][$UserName] = $UserList[$UserName]['RealName'];
                }
            }
            foreach($GroupManagerList as $UserName)
            {
                if($UserName != '')
                {
                    $LastRealName = $UserList[$UserName]['PreAppendName'];
                    $GroupList[$Key]['GroupManagerList'][$UserName] = $LastRealName;
                    $GroupList[$Key]['GroupManagerNameList'][$UserName] = $UserList[$UserName]['RealName'];
                }
            }

        }
        $GroupList[$Key]['GroupUserSingleSelect'] = htmlSelect($GroupList[$Key]['GroupUserList'], 'GroupUserList','', '', 'class="FullSelect"');
        $GroupList[$Key]['GroupManagerSingleSelect'] = htmlSelect($GroupList[$Key]['GroupManagerList'], 'GroupManagerList','', '', 'class="FullSelect"');

        $GroupACLList = unserialize($GroupInfo['GroupACL']);
        if(!is_array($GroupACLList))
        {
            $GroupACLList = array();
        }
        $GroupList[$Key]['GroupProjectList'] = array();
        foreach($GroupACLList as $ProjectID => $ACLInfo)
        {
            $GroupList[$Key]['GroupProjectList'][$ProjectID] = $ProjectList[$ProjectID]['ProjectName'];
        }

        $GroupList[$Key]['GroupProjectSingleSelect'] = htmlSelect($GroupList[$Key]['GroupProjectList'], 'GroupProjectList', '', '', 'class="FullSelect"');
    }

    return $GroupList;
}

/*=======================================Group Functions End==============================*/

/*=======================================Bug Functions Start==============================*/

/**
 * Get BuildLisg
 *
 * @auhtor                  Yupeng Lee<leeyupeng@gmail.com>
 */
function testGetBugBuildList($ProjectID)
{
    $BuildList = dbGetList('BugInfo', 'OpenedBuild, COUNT(OpenedBuild) AS UsedTimes', "ProjectID = '{$ProjectID}'", 'OpenedBuild', 'OpenedBuild DESC, UsedTimes DESC', '10', 'OpenedBuild');
    $DayBuild = "1.0.".date("Ymd");
    $BuildList[$DayBuild] = array('OpenedBuild' => $DayBuild, 'UsedTimes' => '0');

    return $BuildList;
}

/**
 * Open Bug
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostBugInfo
 * @return array  $ActionMsg
 */
function testOpenBug($PostBugInfo)
{
    global $_LANG;

    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'BugID'=>'0');

    $ProjectID = $PostBugInfo['ProjectID'];
    $BugTitle = sysDbSubStr(htmlspecialchars($PostBugInfo['BugTitle']), 150);
    $BugMailTitle = sysDbSubStr($PostBugInfo['BugTitle'], 150);
    $ReproSteps = htmlspecialchars($PostBugInfo['ReproSteps']);
    $OpenedBuild = $PostBugInfo['OpenedBuildInput'] == "" ? $PostBugInfo['OpenedBuild'] : $PostBugInfo['OpenedBuildInput'];
    $OpenedBuild = htmlspecialchars(trim($OpenedBuild));

    if($BugTitle == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoBugTitle'];
    }
    if($PostBugInfo['BugSeverity'] == '')
    {
    	$PostBugInfo['BugSeverity'] = 1;
        // $ActionMsg['FailedMsg'][] = $_LANG['NoBugSeverity'];
    }
    if($PostBugInfo['BugType'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoBugType'];
    }
    if($PostBugInfo['HowFound'] == '')
    {
    	$PostBugInfo['HowFound'] = "Other";
        // $ActionMsg['FailedMsg'][] = $_LANG['NoHowFound'];
    }
    if($OpenedBuild == '')
    {
    	$OpenedBuild = "null";
        // $ActionMsg['FailedMsg'][] = $_LANG['NoOpenBuild'];
    }
    if($ReproSteps == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoBugRepro'];
    }
    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }
	
	// Walzer Add. 若相关CASE（模块）包含子项，则按照MS PROJECT的规则，不能在集成模块上建立任务	
	if ($PostBugInfo['CaseID'] > 0)
	{
		$Children = dbGetList('CaseInfo','CaseID',"ParentID = '{$PostBugInfo[CaseID]}'",'','CaseID DESC','1');
		
		if (!empty($Children))
		{
			$ActionMsg['FailedMsg'][] = $_LANG['CannotOpenDevelopFromIntegration'];
			return $ActionMsg;
		}
	}
		
    //Open Bug
    $ProjectInfo = dbGetRow('TestProject', '', "ProjectID='{$PostBugInfo[ProjectID]}'");
    $ProjectName = $ProjectInfo['ProjectName'];
    $ModulePath = testGetModulePath($PostBugInfo['ModuleID']);
    $OpenedBy = $PostBugInfo['TestUserName'];
    $LastEditedBy = $OpenedBy;
    $MailToList = testGetMailToList($PostBugInfo['MailTo'], $ProjectID);
    $MailTo = $MailToList['MailToStr'];
    $BugKeyWord = htmlspecialchars(trim($PostBugInfo['BugKeyword']));
    $PostBugInfo['DuplicateID'] = dbGetValidValueList($PostBugInfo['DuplicateID'], 'BugInfo','BugID');
    $PostBugInfo['LinkID'] = dbGetValidValueList($PostBugInfo['LinkID'], 'BugInfo','BugID');
    $PostBugInfo['CaseID'] = dbGetValidValueList($PostBugInfo['CaseID'], 'CaseInfo','CaseID');
    if($PostBugInfo['BugPriority'] == '')
    {
        $PostBugInfo['BugPriority'] = 'NULL';
    }

    if($PostBugInfo[AssignedTo] != '')
    {
        $AssignDate = 'now()';
    }
    else
    {
        $AssignDate = "''";
    }
    
    if($PostBugInfo[ResultID] == '')
    {
        $PostBugInfo[ResultID] = 'NULL';
    }
    
    if($PostBugInfo[CaseID] == '')
    {
    	$PostBugInfo[CaseID] = 'NULL';
    }

    $PostBugInfo['BugMachine'] = htmlspecialchars(trim($PostBugInfo['BugMachine']));
    $PostBugInfo['ReplyNote'] = htmlspecialchars(trim($PostBugInfo['ReplyNote']));
	
	// 公用字段
    $ValueSql .= "'{$ProjectID}','{$ProjectName}','{$PostBugInfo[ModuleID]}','{$ModulePath}',";
    $ValueSql .= "'{$BugTitle}','{$PostBugInfo[BugSeverity]}',{$PostBugInfo[BugPriority]},'{$PostBugInfo[BugType]}','{$PostBugInfo['BugOS']}',";
    $ValueSql .= "'{$PostBugInfo[BugBrowser]}','{$PostBugInfo[BugMachine]}','{$PostBugInfo[BugSubStatus]}','{$PostBugInfo[HowFound]}','{$ReproSteps}','Active',";
    $ValueSql .= "'{$PostBugInfo[LinkID]}',{$PostBugInfo[CaseID]},'{$PostBugInfo[DuplicateID]}',{$PostBugInfo[ResultID]},";
    $ValueSql .= "'{$MailTo}','{$OpenedBy}',now(),'{$OpenedBuild}','{$PostBugInfo[AssignedTo]}',{$AssignDate},'{$LastEditedBy}',now(),',{$OpenedBy},',";
    $ValueSql .= "'{$BugKeyWord}',";
    
    $FieldSql .= "ProjectID,ProjectName,ModuleID,ModulePath,BugTitle,BugSeverity,BugPriority,BugType,BugOS,BugBrowser,";
    $FieldSql .= "BugMachine,BugSubStatus,HowFound,ReproSteps,BugStatus,LinkID,CaseID,DuplicateID,ResultID,MailTo,OpenedBy,";
    $FieldSql .= "OpenedDate,OpenedBuild,AssignedTo,AssignedDate,LastEditedBy,LastEditedDate,ModifiedBy,BugKeyword,";


    // Walzer Add 计算BugScore和BugAffairStr
    {
		$BugScore = 0;
		$BugAffairStr = "";
			
		foreach ( $PostBugInfo['affair_checkbox'] as $affair_id )
		{
			$affair = dbGetRow('AffairList','manhour',"AffairID = '{$affair_id}'");

			if ($affair['manhour'] >= 0)
			{
				$BugScore = $BugScore + $affair['manhour'];
				$BugAffairStr .= $affair_id.",";
				// echo "Add ".$affair_id.":".$affair['manhour'].", score = ".$BugScore."<br>";
			}
			else
			{
				$scope = $PostBugInfo['scope'][$affair_id];
				$complexity = $PostBugInfo['complexity'][$affair_id];
				
				$manhour = dbGetRow('AffairManhour', 'manhour', 
									"scope = '{$scope}' AND complexity = '{$complexity}' AND (AffairID like '{$affair_id},%' OR AffairID like '%,{$affair_id},' OR AffairID like '%,{$affair_id},%,')");

				$BugScore = $BugScore + $manhour['manhour']; 
				$BugAffairStr .= $affair_id."(".$scope.$complexity."),";
				// echo "Add ".$affair_id.":".$manhour['manhour'].", score = ".$BugScore."<br>";
			}
		}
		
		// 加入未列出事务的分值
		$BugScore = $BugScore + $PostBugInfo['UnlistedScore'];
		
		// 质量默认值为1
		if ($PostBugInfo['FixQuality'] <= 0)
		{
			$PostBugInfo['FixQuality'] = 1.0;
		}
		
		// printaline("abc");
		// 计算BugScore和$BugAffairStr结束
		
    	$ValueSql .= "'{$BugScore}','{$PostBugInfo[FixQuality]}','{$BugAffairStr}','{$PostBugInfo[UnlistedScore]}',";
		$FieldSql .= "BugScore,FixQuality,BugAffairStr,UnlistedScore,";
	}
	
	// 加入Deadline和DeadlineStatus
	{
		$ValueSql .= "'{$PostBugInfo[Deadline]}','{$PostBugInfo[DeadlineStatus]}'";	
		$FieldSql .= "Deadline,DeadlineStatus";
	}
	
	// print_r($ValueSql);
	// echo '<br>';
	// print_r($FieldSql);
	// walzer();
		
    // insert bug
    //$BugID = dbInsertRow('BugInfo',$ValueSql,"ProjectID,ProjectName,ModuleID,ModulePath,BugTitle,BugSeverity,BugPriority,BugType,BugOS,BugBrowser,BugMachine,BugSubStatus,HowFound,ReproSteps,BugStatus,LinkID,CaseID,DuplicateID,ResultID,MailTo,OpenedBy,OpenedDate,OpenedBuild,AssignedTo,AssignedDate,LastEditedBy,LastEditedDate,ModifiedBy,BugKeyword,BugScore,FixQuality,BugAffairStr,UnlistedScore");
	$BugID = dbInsertRow('BugInfo', $ValueSql, $FieldSql);
        
	// Walzer Add, 加入对相关case的分值和完成百分比计算
    if($PostBugInfo['CaseID'] > 0)
    {
    	// 直接关联的CASE（模块）
        $RelativeCase = dbGetRow('CaseInfo', '*', "CaseID = '{$PostBugInfo[CaseID]}'");
    	$ScoreTotal = $RelativeCase['ScoreTotal'] + $BugScore;
    	$ScoreUnresolved = $RelativeCase['ScoreUnresolved'] + $BugScore;
    	
    	if ($ScoreUnresolved + $RelativeCase['ScoreResolved'] != $ScoreTotal)
    	{
    		print("RelativeCase: ScoreResolved + ScoreUnresolved != ScoreTotal");
    		walzer_error();
    	}
    	
    	$ResolvedPercent = $RelativeCase['ScoreResolved'] / $ScoreTotal * 100;
    	    	
        dbUpdateRow('CaseInfo',  'BugID',"CONCAT(BugID,'{$BugID}',',')", 
        						 'ScoreTotal', "{$ScoreTotal}",
        						 'ScoreUnresolved', "{$ScoreUnresolved}",
        						 'ResolvedPercent', "{$ResolvedPercent}",
        						 "CaseID={$PostBugInfo[CaseID]}");
        // 父级模块
        while ($RelativeCase['ParentID'])
        {
	        $RelativeCase = dbGetRow('CaseInfo', '*', "CaseID = '{$RelativeCase[ParentID]}'");
	    	$ScoreTotal = $RelativeCase['ScoreTotal'] + $BugScore;
	    	$ScoreUnresolved = $RelativeCase['ScoreUnresolved'] + $BugScore;
	    	
	    	if ($ScoreUnresolved + $RelativeCase['ScoreResolved'] != $ScoreTotal)
	    	{
	    		print("RelativeCase: ScoreResolved + ScoreUnresolved != ScoreTotal");
	    		walzer_error();
	    	}
	    	
	    	$ResolvedPercent = $RelativeCase['ScoreResolved'] / $ScoreTotal * 100;
	    	    	
	        dbUpdateRow('CaseInfo',  'ScoreTotal', "{$ScoreTotal}",
	        						 'ScoreUnresolved', "{$ScoreUnresolved}",
	        						 'ResolvedPercent', "{$ResolvedPercent}",
	        						 "CaseID={$RelativeCase[CaseID]}");
        }
    }

    // insert action
    $ActionID = testAddAction('Bug',$BugID,$OpenedBy,'Opened','',$PostBugInfo['ReplyNote']);

    $ActionMsg['Bingle'] = true;
    $ActionMsg['BugID'] = $BugID;
    $ActionMsg['ActionID'] = $ActionID;

    $AssignedToUserInfo = testGetUserInfoByName($PostBugInfo['AssignedTo']);
    if($PostBugInfo['AssignedTo'] != $PostBugInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostBugInfo['AssignedTo'] : '';
    }
    $MainMessage = testCreateMailMessage($BugID,'Opened',$PostBugInfo['TestRealName'],$AssignedToUserInfo['RealName'],$ReproSteps ."\n". $PostBugInfo['ReplyNote'],'Bug');
    sysMail($AssignedToMail, $MailToList['Email'], 'Bug #' . $BugID . ' ' . $BugMailTitle, $MainMessage);

    return $ActionMsg;
}

function WalzerUpdateCaseScore($id, $ResolvedChange, $UnresolvedChange)
{	
	$case = dbGetRow('CaseInfo','ScoreResolved,ScoreUnresolved,ParentID',
								"CaseID = '{$id}'");
	
	$ScoreResolved = $case['ScoreResolved'] + $ResolvedChange;
	$ScoreUnresolved = $case['ScoreUnresolved'] + $UnresolvedChange;
	$ScoreTotal = $ScoreResolved + $ScoreUnresolved;
	if ($ScoreTotal != 0)
	{
		$ResolvedPercent = $ScoreResolved / $ScoreTotal * 100;
	}
	else
	{
		$ResolvedPercent = 0;
	}
	
	dbUpdateRow('CaseInfo',  'ScoreTotal', "{$ScoreTotal}",
							 'ScoreResolved', "{$ScoreResolved}",
							 'ScoreUnresolved', "{$ScoreUnresolved}",
							 'ResolvedPercent', "{$ResolvedPercent}",
							 "CaseID={$id}");
							 
	if ($case['ParentID'] > 0)
	{
		WalzerUpdateCaseScore($case['ParentID'], $ResolvedChange, $UnresolvedChange);
	}
}

/**
 * Edit Bug
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostBugInfo
 * @param  bool   $UploadFile  Upload file or not
 * @return array  $ActionMsg
 */
function testEditBug($PostBugInfo, $UploadFile = false)
{
    global $_LANG;
	
	// Walzer Add 计算BugScore和BugAffairStr
	{
		$BugScore = 0;
		$BugAffairStr = "";
		
		// echo "#####################################";
		// print_r($PostBugInfo['affair_checkbox']);
		
		foreach ( $PostBugInfo['affair_checkbox'] as $affair_id )
		{
			$affair = dbGetRow('AffairList','manhour',"AffairID = '{$affair_id}'");
			
			if ($affair['manhour'] >= 0)
			{
				$BugScore = $BugScore + $affair['manhour'];
				$BugAffairStr .= $affair_id.",";
				// echo "Add ".$affair_id.":".$affair['manhour'].", score = ".$BugScore."<br>";
			}
			else
			{
				$scope = $PostBugInfo['scope'][$affair_id];
				$complexity = $PostBugInfo['complexity'][$affair_id];
				
				$manhour = dbGetRow('AffairManhour', 'manhour', 
									"scope = '{$scope}' AND complexity = '{$complexity}' AND (AffairID like '{$affair_id},%' OR AffairID like '%,{$affair_id},' OR AffairID like '%,{$affair_id},%,')");
				
				// echo 'affair_id: '.$affair_id.'<br>';
				// echo 'scope: '.$scope.' complexity: '.$complexity.' manhour: '.$manhour;
				// walzer();
			
				$BugScore = $BugScore + $manhour['manhour']; 
				$BugAffairStr .= $affair_id."(".$scope.$complexity."),";
				// echo "Add ".$affair_id.":".$manhour['manhour'].", score = ".$BugScore."<br>";
			}
		}
		
		$PostBugInfo['BugScore'] = $BugScore + $PostBugInfo['UnlistedScore'];
		$PostBugInfo['BugAffairStr'] = $BugAffairStr;
	
		// 质量默认值为1
		if ($PostBugInfo['FixQuality'] <= 0)
		{
			$PostBugInfo['FixQuality'] = 1.0;
		}

		// echo "BugScore from ".$PostBugInfo['BugScore']." to ".$BugScore."<br>";
		// echo "BugAffairStr ".$BugAffairStr."<br>";
		// printaline("abc");
		// 计算BugScore和$BugAffairStr结束	
	}
				
    $BugID = $PostBugInfo['BugID'];
    $ProjectID = $PostBugInfo['ProjectID'];
    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'BugID'=>$PostBugInfo['BugID'], 'ActionID' => '0');
    foreach($PostBugInfo as $Key => $Value)
    {
        if($_LANG['BugFields'][$Key])
        {
            // for call eval function
            switch($Key)
            {
                case 'BugTitle':
                $PostBugInfo['BugTitle'] = sysDbSubStr(htmlspecialchars($PostBugInfo['BugTitle']),150);
                $BugMailTitle = sysDbSubStr($PostBugInfo['BugTitle'],150);
                if($PostBugInfo['BugTitle'] == '')
                {
                    $ActionMsg['FailedMsg'][] = $_LANG['NoBugTitle'];
                }
                break;

                case 'ReproSteps':
                $PostBugInfo['ReproSteps'] = htmlspecialchars($PostBugInfo['ReproSteps']);
                if($PostBugInfo['ReproSteps'] == '')
                {
                    $ActionMsg['FailedMsg'][] = $_LANG['NoBugRepro'];
                }
                break;

                case 'BugSeverity':
                if($PostBugInfo['BugSeverity'] == '')
                {
                	$PostBugInfo['BugSeverity'] = 1;
                    // $ActionMsg['FailedMsg'][] = $_LANG['NoBugSeverity'];
                }
                break;

                case 'BugType':
                if($PostBugInfo['BugType'] == '')
                {
                    $ActionMsg['FailedMsg'][] = $_LANG['NoBugType'];
                }
                break;

			    case 'HowFound':
                if($PostBugInfo['HowFound'] == '')
                {
                	$PostBugInfo['HowFound'] = "other";
                    // $ActionMsg['FailedMsg'][] = $_LANG['NoHowFound'];
                }
                break;
				
                case 'MailTo':
                $MailToList = testGetMailToList($PostBugInfo['MailTo'], $ProjectID);
                $PostBugInfo['MailTo'] = $MailToList['MailToStr'];
                break;

                case 'BugKeyword':
                $PostBugInfo['BugKeyword'] = htmlspecialchars(trim($PostBugInfo['BugKeyword']));
                break;

                case 'BugMachine':
                $PostBugInfo['BugMachine'] = htmlspecialchars(trim($PostBugInfo['BugMachine']));
                break;

                case 'DuplicateID':
                $PostBugInfo['DuplicateID'] = dbGetValidValueList($PostBugInfo['DuplicateID'], 'BugInfo','BugID');
                break;

                case 'LinkID':
                $PostBugInfo['LinkID'] = dbGetValidValueList($PostBugInfo['LinkID'], 'BugInfo','BugID');
                break;
				
                case 'CaseID':
                $PostBugInfo['CaseID'] = dbGetValidValueList($PostBugInfo['CaseID'], 'CaseInfo','CaseID');
                break;
            }
        }
    }

    $PostBugInfo['OpenedBuild'] = $PostBugInfo['OpenedBuildInput'] == "" ? $PostBugInfo['OpenedBuild'] : $PostBugInfo['OpenedBuildInput'];
    $PostBugInfo['OpenedBuild'] = htmlspecialchars(trim($PostBugInfo['OpenedBuild']));

    if($PostBugInfo['OpenedBuild'] == '')
    {
    	$PostBugInfo['OpenedBuild'] = "null";
        //$ActionMsg['FailedMsg'][] = $_LANG['NoOpenBuild'];
    }
    if(isset($PostBugInfo['ResolvedBuildInput']))
    {
        $PostBugInfo['ResolvedBuild'] = htmlspecialchars(trim($PostBugInfo['ResolvedBuildInput']));
    }
    if(isset($PostBugInfo['ResolvedBuild']) && $PostBugInfo['ResolvedBuild'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResolvedBuild'];
    }
    if(isset($PostBugInfo['Resolution']) && $PostBugInfo['Resolution'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResolution'];
    }
    if($PostBugInfo['Resolution'] == 'Duplicate' && $PostBugInfo['DuplicateID'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoDuplicateID'];
    }

    $ProjectInfo = dbGetRow('TestProject', '', "ProjectID='{$PostBugInfo[ProjectID]}'");
    $PostBugInfo['ProjectName'] = $ProjectInfo['ProjectName'];
    $ModulePath = testGetModulePath($PostBugInfo['ModuleID']);
    $PostBugInfo['ModulePath'] = $ModulePath;

    $Columns = '*';
    $RawBugInfo = dbGetRow('BugInfo',$Columns,"BugID = '{$PostBugInfo[BugID]}'");
    $TempPostBugInfo = sysStripSlash($PostBugInfo);

    $BugDiffInfo = array();
    $DiffAssoc = 'ProjectID,ModuleID,BugTitle,BugType,BugOS,BugSeverity,BugPriority,BugBrowser,BugMachine,BugSubStatus,';
    $DiffAssoc .= 'HowFound,BugKeyword,AssignedTo,MailTo,OpenedBuild,ResolvedBuild,';
    $DiffAssoc .= 'Resolution,LinkID,CaseID,DuplicateID,';

	// 为理想工时法加入的字段
	$DiffAssoc .= 'BugScore,FixQuality,BugAffairStr,UnlistedScore,';
	// 为Deadline和状态加入的字段
	$DiffAssoc .= 'Deadline,DeadlineStatus';
	
	// 比较后得出不同的字段	
    $BugDiffInfo += sysArrayDiffAssoc($TempPostBugInfo, $RawBugInfo, $DiffAssoc);
		
    if(stripslashes(($PostBugInfo['ReproSteps'])) != $RawBugInfo['ReproSteps'])
    {
        $BugDiffInfo['ReproSteps'] = $PostBugInfo['ReproSteps'];
    }
    if(isset($BugDiffInfo['ProjectID']))
    {
        $BugDiffInfo['ProjectName'] = $ProjectInfo['ProjectName'];
    }
    if(isset($BugDiffInfo['ModuleID']))
    {
        $BugDiffInfo['ModulePath'] = $ModulePath;
    }

    $EditPostArray = array();
    $BugDiffInfo = sysAddSlash($BugDiffInfo);
    $EditPostArray += $BugDiffInfo;
			
    if(isset($BugDiffInfo['AssignedTo']))
    {
        $EditPostArray['AssignedDate'] = date('Y-m-d H:i:s');
        $PostBugInfo['AssignedDate'] = $EditPostArray['AssignedDate'];
    }
    else
    {
        $PostBugInfo['AssignedDate'] = $RawBugInfo['AssignedDate'];
    }

    $ResolvedBy = $RawBugInfo['ResolvedBy'];
    $ResolvedBuild = $PostBugInfo['ResolvedBuild'];
    $Resolution = $PostBugInfo['Resolution'];
    $ResolvedDate = $RawBugInfo['ResolvedDate'];
    $ClosedBy = $RawBugInfo['ClosedBy'];
    $ClosedDate = $RawBugInfo['ClosedDate'];
    $BugStatus = $RawBugInfo['BugStatus'];
    $ActionType = 'Edited';
    $AssignedTo = $PostBugInfo['AssignedTo'];
    if($PostBugInfo['ActionType'] == 'Resolved')
    {
        if($RawBugInfo['BugStatus'] == 'Resolved')
        {
            $ActionMsg['FailedMsg'][] = $_LANG['AlreadyResolved'];
        }
        else
        {
            $ResolvedBy = $PostBugInfo['TestUserName'];
            $ResolvedDate = date('Y-m-d H:i:s');
            $BugStatus = 'Resolved';
            $ActionType = 'Resolved';
        }
        
        if($Resolution == 'Duplicate')
        {
            $ChangeNote = "Bug #[bug]{$BugID}[/bug] is resolved as duplicate to this bug.";
            $LastEditedBy = $PostBugInfo['TestUserName'];
            $DuplicateList = explode(',', $PostBugInfo['DuplicateID']);
            foreach($DuplicateList as $DupID)
            {
                $ActionID = testAddAction('Bug', $DupID, $LastEditedBy, 'Edited', '', $ChangeNote);
            }
        }
    }
    elseif($PostBugInfo['ActionType'] == 'Closed')
    {
        if($RawBugInfo['BugStatus'] == 'Closed')
        {
            $ActionMsg['FailedMsg'][] = $_LANG['AlreadyClosed'];
        }
        else
        {
            $ClosedBy = $PostBugInfo['TestUserName'];
            $ClosedDate = date('Y-m-d H:i:s');
            $BugStatus = 'Closed';
            $ActionType = 'Closed';
        }
    }
    elseif($PostBugInfo['ActionType'] == 'Activated')
    {
        if($RawBugInfo['BugStatus'] == 'Active')
        {
            $ActionMsg['FailedMsg'][] = $_LANG['AlreadyActive'];
        }
        else
        {
            $BugStatus = 'Active';
            $ActionType = 'Activated';
            $ResolvedBy = '';
            $ResolvedDate = '0000-00-00 00:00:00';
            $ResolvedBuild = '';
            $Resolution = '';
            $ClosedBy = '';
            $ClosedDate = '0000-00-00 00:00:00';
        }
    }
	
    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }
    else
    {
        $LastActionID = testGetLastActionID('Bug',$BugID);
        if($PostBugInfo['LastActionID'] != $LastActionID)
        {
            $ActionMsg['FailedMsg'][] = $_LANG['BugAlreadyChanged'];
            return $ActionMsg;
        }
    }

    if($PostBugInfo['ReplyNote'] != '' || $UploadFile || $PostBugInfo['ActionType'] == 'Closed' || $PostBugInfo['ActionType'] == 'Activated')
    {
        $EditPostArray['ReplyNote'] = htmlspecialchars($PostBugInfo['ReplyNote']);
    }

    if(empty($EditPostArray))
    {
        $ActionMsg['Bingle'] = true;
        return $ActionMsg;
    }

    if(empty($PostBugInfo['BugPriority']))
    {
        $PostBugInfo['BugPriority'] = 'NULL';
    }

	if (empty($PostBugInfo['CaseID']))
	{
		$PostBugInfo['CaseID'] = 'NULL';
	}
	
    $LastEditedBy = $PostBugInfo['TestUserName'];
	
    // update buginfo
    dbUpdateRow('BugInfo','ProjectID',"'{$PostBugInfo[ProjectID]}'",'ModuleID',"'{$PostBugInfo[ModuleID]}'",
                          'ProjectName',"'{$PostBugInfo[ProjectName]}'",'ModulePath',"'{$PostBugInfo[ModulePath]}'",
                          'BugTitle',"'{$PostBugInfo[BugTitle]}'",
						  'BugStatus',"'{$BugStatus}'",
    					  'BugType',"'{$PostBugInfo[BugType]}'",'BugOS',"'{$PostBugInfo[BugOS]}'",
                          'BugSeverity',"'{$PostBugInfo[BugSeverity]}'",'BugPriority',"{$PostBugInfo[BugPriority]}",
                          'BugBrowser',"'{$PostBugInfo[BugBrowser]}'",'BugMachine',"'{$PostBugInfo[BugMachine]}'",
                          'BugSubStatus',"'{$PostBugInfo[BugSubStatus]}'",
                          'HowFound',"'{$PostBugInfo[HowFound]}'",'BugKeyword',"'{$PostBugInfo[BugKeyword]}'",
                          'ReproSteps',"'{$PostBugInfo[ReproSteps]}'",
                          'AssignedTo',"'{$AssignedTo}'",'MailTo',"'{$PostBugInfo[MailTo]}'",
                          'OpenedBuild',"'{$PostBugInfo[OpenedBuild]}'",
                          'ResolvedBy',"'{$ResolvedBy}'",'ResolvedDate',"'{$ResolvedDate}'",'ResolvedBuild',"'{$ResolvedBuild}'",'Resolution',"'{$Resolution}'",
                          'ClosedBy',"'{$ClosedBy}'",'ClosedDate',"'{$ClosedDate}'",
                          'LinkID',"'{$PostBugInfo[LinkID]}'",'DuplicateID',"'{$PostBugInfo[DuplicateID]}'",
                          'CaseID',"{$PostBugInfo[CaseID]}",
                          'AssignedDate', "'{$PostBugInfo[AssignedDate]}'",
                          'LastEditedBy',"'{$LastEditedBy}'",'LastEditedDate','now()',
                          'ModifiedBy', "CONCAT(ModifiedBy,'{$LastEditedBy}',',')",
                          // 为理想工时法加入的字段
                          'BugScore',"'{$PostBugInfo['BugScore']}'",
						  'FixQuality',"'{$PostBugInfo[FixQuality]}'",
						  'BugAffairStr',"'{$BugAffairStr}'",
						  'UnlistedScore',"'{$PostBugInfo['UnlistedScore']}'",
						  // 为Deadline和状态加入的字段
						  'Deadline',"'{$PostBugInfo[Deadline]}'",
						  'DeadlineStatus',"'{$PostBugInfo[DeadlineStatus]}'",
						  
                          "BugID='{$BugID}'");

	// Walzer Add, 会影响CASE SCORE变更的情况
	// 一共三个地方，相关CaseID变，BugScore变，BugStatus变。3*2*3一共是18种情况。
	{
		// echo 'Post case id: '.$PostBugInfo['CaseID'].'<br>';
		// echo 'old case id: '.$RawBugInfo['CaseID'].'<br>';
	
		$OldResolvedChange = 0;
		$OldUnresolvedChange = 0;
		$NewResolvedChange = 0;
		$NewUnresolvedChange = 0;
		
		// 当CASE ID变时，先计算旧分数的变更
		if ($PostBugInfo['CaseID'] != $RawBugInfo['CaseID'])
		{		
			if ($RawBugInfo['BugStatus'] == 'Active')
			{
				$OldResolvedChange = 0;
				$OldUnresolvedChange = 0 - $RawBugInfo['BugScore'];
				
				$NewResolvedChange = 0;
				$NewUnresolvedChange = $RawBugInfo['BugScore'];
			}
			else
			{
				// Closed, Resolved
				$OldResolvedChange = 0 - $RawBugInfo['BugScore'];
				$OldUnresolvedChange = 0;
				
				$NewResolvedChange = $RawBugInfo['BugScore'];
				$NewUnresolvedChange = 0;
			}
		}
		
		// Walzer Add: 若分数变更或任务完成/重新激活，则更新对应的Case里的 总分值 和 完成百分比
		if ($PostBugInfo['CaseID'] > 0 &&
			($PostBugInfo['BugScore'] != $RawBugInfo['BugScore'] || 
			$ActionType == 'Resolved' || $ActionType == 'Activated'))
		{        
			// 对相关case的分值和完成百分比计算
	    	if ($ActionType == 'Resolved')
			{
				// 把任务改成已解决
				if ($PostBugInfo['BugScore'] == $RawBugInfo['BugScore'])
				{
					$ResolvedChange = $PostBugInfo['BugScore'];
					$UnresolvedChange = 0 - $PostBugInfo['BugScore'];
				}
				else
				{
					$ResolvedChange = $PostBugInfo['BugScore'];
					$UnresolvedChange = 0 - $RawBugInfo['BugScore'];
				}
			}
			else if ($ActionType == 'Activated')
			{
				// 把已解决或已关闭的任务激活
				if ($PostBugInfo['BugScore'] == $RawBugInfo['BugScore'])
				{
					$ResolvedChange = 0 - $PostBugInfo['BugScore'];
					$UnresolvedChange = $PostBugInfo['BugScore'];
				}
				else
				{
					$ResolvedChange = 0 - $RawBugInfo['BugScore'];
					$UnresolvedChange = $PostBugInfo['BugScore'];
				}
			}
			else
			{
				// 只是修改分数，不修改状态；或者把状态改为已关闭
				if ($BugStatus == 'Active')
				{
					$ResolvedChange = 0; 
					$UnresolvedChange = $PostBugInfo['BugScore'] - $RawBugInfo['BugScore'];
				}
				else
				{
					$ResolvedChange = $PostBugInfo['BugScore'] - $RawBugInfo['BugScore'];
					$UnresolvedChange = 0;
				}
			}
		}
	
		// update old
		if ($RawBugInfo['CaseID'] > 0)
		{
			// echo 'Old CaseID:',$RawBugInfo['CaseID'],' ResovledChange:', $OldResolvedChange,' UnresolvedChange:',$OldUnresolvedChange.'<br>';
			WalzerUpdateCaseScore($RawBugInfo['CaseID'], $OldResolvedChange, $OldUnresolvedChange);
		}
	
		// update new
		if ($PostBugInfo['CaseID'] > 0)
		{
			// echo 'New CaseID:',$PostBugInfo['CaseID'],' ResovledChange:',$ResolvedChange + $NewResolvedChange,' UnresolvedChange:',$UnresolvedChange + $NewUnresolvedChange.'<br>';
			WalzerUpdateCaseScore($PostBugInfo['CaseID'], $ResolvedChange + $NewResolvedChange, $UnresolvedChange + $NewUnresolvedChange);
		}
	}
						
    // insert action
    $ActionID = testAddAction('Bug',$BugID,$LastEditedBy,$ActionType,'',$EditPostArray['ReplyNote']);
    $ActionMsg['ActionID'] = $ActionID;
    $ActionMsg['Bingle'] = true;

    // insert histroy
    $ChangeNote = '';
    foreach($BugDiffInfo as $Key => $Value)
    {
        $OldValue = sysAddSlash($RawBugInfo[$Key]);
        $NewValue = $BugDiffInfo[$Key];
        testAddHistory($ActionID, $Key, $OldValue, $NewValue);
        if($Key == 'ReproSteps')
        {
            $OldValue = '...';
            $NewValue = '...';
        }
        elseif($Key == 'Resolution' && $ActionType == 'Resolved')
        {
            $ActionDesc = " as {$NewValue}";
            continue;
        }
 
        $ChangeNote .= "Changed [b]{$Key}[/b] from [b]\"{$OldValue}\"[/b] to[b]\"{$NewValue}\"[/b]\n";
    }

    $AssignedToUserInfo = testGetUserInfoByName($PostBugInfo['AssignedTo']);
    if($PostBugInfo['AssignedTo'] != $PostBugInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostBugInfo['AssignedTo'] : '';
    }
    if($RawBugInfo['OpenedBy'] != $PostBugInfo['TestUserName'] &&  $RawBugInfo['OpenedBy'] != $PostBugInfo['AssignedTo'])
    {
        $OpenedByUserInfo = testGetUserInfoByName($RawBugInfo['OpenedBy']);
        $MailToList['Email'][] = $OpenedByUserInfo['Email'];
    }
    if($PostBugInfo['ActionType'] == 'Closed' && $RawBugInfo['ResolvedBy'] != $PostBugInfo['TestUserName'])
    {
        $ResolvedByUserInfo = testGetUserInfoByName($RawBugInfo['ResolvedBy']);
        $MailToList['Email'][] = $ResolvedByUserInfo['Email'];
    }
    $ActionUser = $PostBugInfo['TestRealName'];
    if($ActionUser == '') $ActionUser = $PostBugInfo['TestUserName'];
    $MainMessage = testCreateMailMessage($BugID, $ActionType ,$ActionUser,$AssignedToUserInfo['RealName'],$ChangeNote . "\n" . $EditPostArray['ReplyNote'],'Bug', $ActionDesc);
    sysMail($AssignedToMail, $MailToList['Email'], 'Bug #' . $BugID . ' ' . $BugMailTitle, $MainMessage);

    return $ActionMsg;
}

/**
 * Edit user
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostUserInfo
 * @return array  $ActionMsg
 */
function testEditUser($PostUserInfo)
{
    global $_LANG, $_CFG;

    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array());

    $PostUserInfo['UserName'] = htmlspecialchars(trim($PostUserInfo['UserName']));
    $PostUserInfo['RealName'] = htmlspecialchars(trim($PostUserInfo['RealName']));
    $PostUserInfo['Email'] = trim($PostUserInfo['Email']);

    $RawUserInfo = array_pop(dbGetList($_CFG['UserTable']['TableName'], $Columns, "UserName = '{$PostUserInfo[UserName]}'"));

    if(empty($PostUserInfo['RealName']))
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoRealName'];
    }
    if(empty($PostUserInfo['Email']))
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoEmail'];
    }
    elseif(!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/i", $PostUserInfo['Email']))
    {
        $ActionMsg['FailedMsg'][] = $_LANG['InvalidEmail'];
    }
    if($PostUserInfo['RawUserPassword'] != '')
    {
        if(baseEncryptUserPWD($PostUserInfo['RawUserPassword']) != $RawUserInfo['UserPassword'])
        {
            $ActionMsg['FailedMsg'][] = $_LANG['WrongRawUserPassword'];
        }
    }
    if($PostUserInfo['UserPassword'] != $PostUserInfo['RepeatUserPassword'])
    {
        $ActionMsg['FailedMsg'][] = $_LANG['PasswordNotEqual'];
    }

    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }

    if($PostUserInfo['UserPassword'] == '')
    {
        dbUpdateRow($_CFG['UserTable']['TableName'], 'RealName', "'{$PostUserInfo[RealName]}'"
                                                   , 'Email', "'{$PostUserInfo[Email]}'"
                                                   , 'LastEditedBy', "'{$_SESSION[TestUserName]}'"
                                                   , 'LastDate', 'now()'
                                                   , "UserName = '{$PostUserInfo[UserName]}'");
    }
    else
    {
        $PostUserInfo['UserPassword'] = baseEncryptUserPWD($PostUserInfo['UserPassword']);
        dbUpdateRow($_CFG['UserTable']['TableName'], 'RealName', "'{$PostUserInfo[RealName]}'"
                                                   , 'UserPassword', "'{$PostUserInfo[UserPassword]}'"
                                                   , 'Email', "'{$PostUserInfo[Email]}'"
                                                   , 'LastEditedBy', "'{$_SESSION[TestUserName]}'"
                                                   , 'LastDate', 'now()'
                                                   , "UserName = '{$PostUserInfo[UserName]}'");
    }

    $_SESSION['TestRealName'] = $PostUserInfo['RealName'];
    $ActionMsg['Bingle'] = true;

    return $ActionMsg;

}

/**
 * Open Result
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostResultInfo
 * @return array  $ActionMsg
 */
function testOpenResult($PostResultInfo)
{
    global $_LANG;

    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'ResultID'=>'0');

    $ProjectID = $PostResultInfo['ProjectID'];
    $ProjectInfo = dbGetRow('TestProject', '', "ProjectID='{$PostResultInfo[ProjectID]}'");
    $ProjectName = $ProjectInfo['ProjectName'];
    $ModulePath = testGetModulePath($PostResultInfo['ModuleID']);
    $OpenedBy = $PostResultInfo['TestUserName'];
    $LastEditedBy = $OpenedBy;
    $MailToList = testGetMailToList($PostResultInfo['MailTo'], $ProjectID);
    $PostResultInfo['MailTo'] = $MailToList['MailToStr'];
    $PostResultInfo['ResultKeyword'] = htmlspecialchars(trim($PostResultInfo['ResultKeyword']));
    $PostResultInfo['ResultBuild'] = htmlspecialchars(trim($PostResultInfo['ResultBuild']));
    $PostResultInfo['ResultMachine'] = htmlspecialchars(trim($PostResultInfo['ResultMachine']));
    $PostResultInfo['ResultSteps'] = htmlspecialchars($PostResultInfo['ResultSteps']);
    $PostResultInfo['ReplyNote'] = htmlspecialchars(trim($PostResultInfo['ReplyNote']));
    $PostResultInfo['ResultTitle'] = htmlspecialchars(trim($PostResultInfo['ResultTitle']));

    if($PostResultInfo['ResultValue'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultValue'];
    }
    if($PostResultInfo['ResultBuild'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultBuild'];
    }
    if($PostResultInfo['ResultSteps'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultSteps'];
    }

    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }


    if($PostResultInfo[AssignedTo] != '')
    {
        $AssignDate = 'now()';
    }
    else
    {
        $AssignDate = "''";
    }


    $ValueSql .= "'{$ProjectID}','{$ProjectName}','{$PostResultInfo[ModuleID]}','{$ModulePath}','{$PostResultInfo[CaseID]}','{$PostResultInfo[ResultTitle]}','{$PostResultInfo[ResultValue]}','{$PostResultInfo[ResultStatus]}',";
    $ValueSql .= "'{$PostResultInfo[ResultOS]}','{$PostResultInfo[ResultBrowser]}','{$PostResultInfo[ResultMachine]}','{$PostResultInfo[ResultKeyword]}','{$PostResultInfo[MailTo]}','{$PostResultInfo[ResultSteps]}',";
    $ValueSql .= "'{$OpenedBy}',now(),'{$PostResultInfo[ResultBuild]}','{$PostResultInfo[AssignedTo]}',{$AssignDate},'{$LastEditedBy}',now(),',{$OpenedBy},'";

    // insert Result
    $ResultID = dbInsertRow('ResultInfo',$ValueSql,"ProjectID,ProjectName,ModuleID,ModulePath,CaseID,ResultTitle,ResultValue,ResultStatus,
                                                    ResultOS,ResultBrowser,ResultMachine,ResultKeyword,MailTo,ResultSteps,
                                                    OpenedBy,OpenedDate,ResultBuild,AssignedTo,AssignedDate,LastEditedBy,LastEditedDate,ModifiedBy");

    $ActionID = testAddAction('Result',$ResultID,$OpenedBy,'Opened','',$PostResultInfo['ReplyNote']);

    if($PostResultInfo[CaseID]> 0)
    {
        dbUpdateRow('CaseInfo','ResultID',"CONCAT(ResultID,'{$ResultID}',',')", "CaseID={$PostResultInfo[CaseID]}");
    }

    $AssignedToUserInfo = testGetUserInfoByName($PostResultInfo['AssignedTo']);
    if($PostResultInfo['AssignedTo'] != $PostResultInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostResultInfo['AssignedTo'] : '';
    }
    $MainMessage = testCreateMailMessage($ResultID,'Opened',$PostResultInfo['TestRealName'],$AssignedToUserInfo['RealName'], $PostResultInfo['ResultSteps'] ."\n". $PostResultInfo['ReplyNote'],'Result');
    sysMail($AssignedToMail, $MailToList['Email'], 'Result #' . $ResultID . ' ' . $PostResultInfo['ResultTitle'], $MainMessage);

    $ActionMsg['Bingle'] = true;
    $ActionMsg['ResultID'] = $ResultID;
    $ActionMsg['ActionID'] = $ActionID;

    return $ActionMsg;

}

/**
 * Edit Result
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostResultInfo
 * @param  bool   $UploadFile  Upload file or not
 * @return array  $ActionMsg
 */
function testEditResult($PostResultInfo,$UploadFile = false)
{
    global $_LANG;

    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'ResultID'=>'0');

    $ResultID = $PostResultInfo['ResultID'];
    $ProjectID = $PostResultInfo['ProjectID'];
    $LastEditedBy = $PostResultInfo['TestUserName'];
    $MailToList = testGetMailToList($PostResultInfo['MailTo'], $ProjectID);
    $PostResultInfo['MailTo'] = $MailToList['MailToStr'];
    $PostResultInfo['ResultBuild'] = htmlspecialchars(trim($PostResultInfo['ResultBuild']));
    $PostResultInfo['ResultMachine'] = htmlspecialchars(trim($PostResultInfo['ResultMachine']));
    $PostResultInfo['ResultSteps'] = htmlspecialchars($PostResultInfo['ResultSteps']);
    $PostResultInfo['ReplyNote'] = htmlspecialchars(trim($PostResultInfo['ReplyNote']));
    $PostResultInfo['ResultKeyword'] = htmlspecialchars(trim($PostResultInfo['ResultKeyword']));

    if($PostResultInfo['ResultValue'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultValue'];
    }
    if($PostResultInfo['ResultBuild'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultBuild'];
    }
    if($PostResultInfo['ResultSteps'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoResultSteps'];
    }

    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }
	
    if($PostResultInfo[AssignedTo] != '')
    {
        $AssignDate = 'now()';
    }
    else
    {
        $AssignDate = "''";
    }

    $Columns = '*';
    $RawResultInfo = dbGetRow('ResultInfo',$Columns,"ResultID = '{$PostResultInfo[ResultID]}'");
    $TempPostResultInfo = sysStripSlash($PostResultInfo);

    $ResultDiffInfo = array();
    $DiffAssoc = 'ResultTitle,ResultValue,ResultStatus,MailTo,ResultSteps,AssignedTo,ResultOS,ResultBrowser,ResultMachine,ResultBuild,ResultKeyword';
    $ResultDiffInfo += sysArrayDiffAssoc($TempPostResultInfo, $RawResultInfo, $DiffAssoc);
	
    if(isset($ResultDiffInfo['AssignedTo']))
    {
        $EditPostArray['AssignedDate'] = date('Y-m-d H:i:s');
        $PostResultInfo['AssignedDate'] = $EditPostArray['AssignedDate'];
    }
    else
    {
        $PostResultInfo['AssignedDate'] = $RawResultInfo['AssignedDate'];
    }

    if(stripslashes(($PostResultInfo['ResultSteps'])) != $RawResultInfo['ResultSteps'])
    {
        $ResultDiffInfo['ResultSteps'] = $PostResultInfo['ResultSteps'];
    }

    $EditPostArray = array();
    $ResultDiffInfo = sysAddSlash($ResultDiffInfo);
    $EditPostArray += $ResultDiffInfo;
    if($PostResultInfo['ReplyNote'] != '' || $UploadFile)
    {
        $EditPostArray['ReplyNote'] = htmlspecialchars(trim($PostResultInfo['ReplyNote']));
    }
	
    // check Result step
    if(empty($EditPostArray))
    {
        $ActionMsg['Bingle'] = true;
        $ActionMsg['ResultID'] = $ResultID;
        return $ActionMsg;
    }
    else
    {
        $LastActionID = testGetLastActionID('Result',$ResultID);
        if($PostResultInfo['LastActionID'] != $LastActionID)
        {
            $ActionMsg['FailedMsg'][] = $_LANG['ResultAlreadyChanged'];
            return $ActionMsg;
        }
    }

    // update Resultinfo
    dbUpdateRow('ResultInfo','ResultTitle',"'{$PostResultInfo[ResultTitle]}'",
    						 'ResultValue',"'{$PostResultInfo[ResultValue]}'",
                             'ResultStatus',"'{$PostResultInfo[ResultStatus]}'",
                             'MailTo',"'{$PostResultInfo[MailTo]}'",
                             'ResultSteps',"'{$PostResultInfo[ResultSteps]}'",
                             'AssignedTo',"'{$PostResultInfo[AssignedTo]}'",
                             'AssignedDate', "'{$PostResultInfo[AssignedDate]}'",
                             'ResultOS', "'{$PostResultInfo[ResultOS]}'",
                             'ResultBrowser', "'{$PostResultInfo[ResultBrowser]}'",
                             'ResultMachine',"'{$PostResultInfo[ResultMachine]}'",
                             'ResultBuild',"'{$PostResultInfo[ResultBuild]}'",
                             'ResultKeyword',"'{$PostResultInfo[ResultKeyword]}'",
                             'LastEditedBy',"'{$LastEditedBy}'",'LastEditedDate','now()',
                             'ModifiedBy', "CONCAT(ModifiedBy,'{$LastEditedBy}',',')",
                             "ResultID='{$ResultID}'");

    // insert action
    $ActionID = testAddAction('Result',$ResultID,$LastEditedBy,'Edited','',$EditPostArray['ReplyNote']);

    // insert histroy
    $ChangeNote = '';
    foreach($ResultDiffInfo as $Key => $Value)
    {
        $OldValue = sysAddSlash($RawResultInfo[$Key]);
        $NewValue = $ResultDiffInfo[$Key];
        testAddHistory($ActionID, $Key, $OldValue, $NewValue);
        if($Key == 'ResultSteps')
        {
            $OldValue = '...';
            $NewValue = '...';
        }
        $ChangeNote .= "Changed [b]{$Key}[/b] from [b]\"{$OldValue}\"[/b] to[b]\"{$NewValue}\"[/b]\n";
    }

    $AssignedToUserInfo = testGetUserInfoByName($PostResultInfo['AssignedTo']);
    if($PostResultInfo['AssignedTo'] != $PostResultInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostResultInfo['AssignedTo'] : '';
    }
    $MainMessage = testCreateMailMessage($ResultID,$ActionType,$PostResultInfo['TestRealName'],$AssignedToUserInfo['RealName'], $ChangeNote ."\n". $PostResultInfo['ReplyNote'],'Result', $ActionNote);
    sysMail($AssignedToMail, $MailToList['Email'], 'Result #' . $ResultID . ' ' . $PostResultInfo['ResultTitle'], $MainMessage);

    $ActionMsg['Bingle'] = true;
    $ActionMsg['ResultID'] = $ResultID;
    $ActionMsg['ActionID'] = $ActionID;

    return $ActionMsg;
}

/**
 * Open Case
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $PostCaseInfo
 * @return array  $ActionMsg
 */
function testOpenCase($PostCaseInfo)
{
    global $_LANG;

    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'CaseID'=>'0', 'ActionID' => '0');

    $ProjectID = $PostCaseInfo['ProjectID'];
    $PostCaseInfo['CaseTitle'] = sysDbSubStr(htmlspecialchars($PostCaseInfo['CaseTitle']), 150);
    $CaseMailTitle = sysDbSubStr($PostCaseInfo['CaseTitle'], 150);
    $PostCaseInfo['CaseSteps'] = htmlspecialchars($PostCaseInfo['CaseSteps']);

    if($PostCaseInfo['CaseTitle'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoCaseTitle'];
    }

    if($PostCaseInfo['CaseSteps'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoCaseStep'];
    }
	
	if($PostCaseInfo['CasePriority'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCasePriority'];
	}
	
	if($PostCaseInfo['CaseType'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCaseType'];
	}
	
	if($PostCaseInfo['CaseMethod'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCaseMethod'];
	}
	
    $PostCaseInfo['ScriptedDate'] = trim($PostCaseInfo['ScriptedDate']);
    if($PostCaseInfo['ScriptedDate'] != '' && !sysCheckDateFormat($PostCaseInfo['ScriptedDate']))
    {
        $ActionMsg['FailedMsg'][] = $_LANG['BadScriptedDate'];
    }

    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }

    $ProjectInfo = dbGetRow('TestProject', '', "ProjectID='{$PostCaseInfo[ProjectID]}'");
    $ProjectName = $ProjectInfo['ProjectName'];
    $ModulePath = testGetModulePath($PostCaseInfo['ModuleID']);
    $OpenedBy = $PostCaseInfo['TestUserName'];
    $LastEditedBy = $OpenedBy;
    $MailToList = testGetMailToList($PostCaseInfo['MailTo'], $ProjectID);
    $MailTo = $MailToList['MailToStr'];
    $PostCaseInfo['CaseKeyword'] = htmlspecialchars(trim($PostCaseInfo['CaseKeyword']));
    $PostCaseInfo['LinkID'] = dbGetValidValueList($PostCaseInfo['LinkID'], 'CaseInfo','CaseID');
    $PostCaseInfo['BugID' ] = dbGetValidValueList($PostCaseInfo['BugID'], 'BugInfo','BugID');
    if($PostCaseInfo['CasePriority'] == '')
    {
        $PostCaseInfo['CasePriority'] = 'NULL';
    }

    if($PostCaseInfo[AssignedTo] != '')
    {
        $AssignedDate = 'now()';
    }
    else
    {
        $AssignedDate = "''";
    }


    $PostCaseInfo['ReplyNote'] = htmlspecialchars(trim($PostCaseInfo['ReplyNote']));

    $ValueSql .= "'{$PostCaseInfo[ProjectID]}','{$ProjectName}','{$PostCaseInfo[ModuleID]}','{$ModulePath}',";
    $ValueSql .= "'{$PostCaseInfo[CaseTitle]}','{$PostCaseInfo[CaseType]}','Active',{$PostCaseInfo[CasePriority]},'{$PostCaseInfo[CaseMethod]}','{$PostCaseInfo[CasePlan]}','{$PostCaseInfo[CaseSteps]}',";
    $ValueSql .= "'{$OpenedBy}',now(),'{$PostCaseInfo[AssignedTo]}',{$AssignedDate},'{$LastEditedBy}',now(),',{$OpenedBy},',";
    $ValueSql .= "'{$MailTo}','{$PostCaseInfo[ScriptedBy]}','{$PostCaseInfo[ScriptedDate]}','{$PostCaseInfo[ScriptStatus]}','{$PostCaseInfo[ScriptLocation]}',";
    $ValueSql .= "'{$PostCaseInfo[LinkID]}','{$PostCaseInfo[BugID]}','{$PostCaseInfo[CaseKeyword]}','','{$PostCaseInfo[ParentID]}'";

    $CaseID = dbInsertRow('CaseInfo', $ValueSql, "ProjectID,ProjectName,ModuleID,ModulePath,
                                                  CaseTitle,CaseType,CaseStatus,CasePriority,CaseMethod,CasePlan,CaseSteps,
                                                  OpenedBy,OpenedDate,AssignedTo,AssignedDate,LastEditedBy,LastEditedDate,ModifiedBy,
                                                  MailTo,ScriptedBy,ScriptedDate,ScriptStatus,ScriptLocation,
                                                  LinkID,BugID,CaseKeyword,ResultID,ParentID");

    $ActionID = testAddAction('Case',$CaseID,$OpenedBy,'Opened','',$PostCaseInfo['ReplyNote']);

    $AssignedToUserInfo = testGetUserInfoByName($PostCaseInfo['AssignedTo']);
    if($PostCaseInfo['AssignedTo'] != $PostCaseInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostCaseInfo['AssignedTo'] : '';
    }
    $MainMessage = testCreateMailMessage($CaseID,'Opened', $PostCaseInfo['TestRealName'],$AssignedToUserInfo['RealName'], $PostCaseInfo['CaseSteps']."\n". $PostCaseInfo['ReplyNote'],'Case');
    sysMail($AssignedToMail, $MailToList['Email'], 'Case #' . $CaseID . ' ' . $CaseMailTitle, $MainMessage);

    $ActionMsg['Bingle'] = true;
    $ActionMsg['CaseID'] = $CaseID;
    $ActionMsg['ActionID'] = $ActionID;

    return $ActionMsg;
}

/**
 * Edit Case
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param  array  $EditCaseInfo
 * @param  bool   $UploadFile   Upload file or not
 * @return array  $ActionMsg
 */
function testEditCase($PostCaseInfo, $UploadFile = false)
{
    global $_LANG;

    $CaseID = $PostCaseInfo['CaseID'];
    $ProjectID = $PostCaseInfo['ProjectID'];
    $ActionMsg = array('Bingle'=>false,'BingleMsg'=>array(), 'FailedMsg'=>array(), 'CaseID'=>$PostCaseInfo['CaseID'], 'ActionID' => '0');

    $ProjectID = $PostCaseInfo['ProjectID'];
    $PostCaseInfo['CaseTitle'] = sysDbSubStr(htmlspecialchars($PostCaseInfo['CaseTitle']), 150);
    $CaseMailTitle = sysDbSubStr($PostCaseInfo['CaseTitle'], 150);
    $PostCaseInfo['CaseSteps'] = htmlspecialchars($PostCaseInfo['CaseSteps']);

    if($PostCaseInfo['CaseTitle'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoCaseTitle'];
    }
    if($PostCaseInfo['CaseSteps'] == '')
    {
        $ActionMsg['FailedMsg'][] = $_LANG['NoCaseStep'];
    }

	if($PostCaseInfo['CasePriority'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCasePriority'];
	}
	
	if($PostCaseInfo['CaseType'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCaseType'];
	}
	
	if($PostCaseInfo['CaseMethod'] == '')
	{
		$ActionMsg['FailedMsg'][] = $_LANG['NoCaseMethod'];
	}

    $PostCaseInfo['ScriptedDate'] = trim($PostCaseInfo['ScriptedDate']);
    if($PostCaseInfo['ScriptedDate'] != '' && !sysCheckDateFormat($PostCaseInfo['ScriptedDate']))
    {
        $ActionMsg['FailedMsg'][] = $_LANG['BadScriptedDate'];
    }

    if(!empty($ActionMsg['FailedMsg']))
    {
        return $ActionMsg;
    }

    $ProjectInfo = dbGetRow('TestProject', '', "ProjectID='{$PostCaseInfo[ProjectID]}'");
    $ProjectName = $ProjectInfo['ProjectName'];
    $PostCaseInfo['ProjectName'] = $ProjectName;
    $ModulePath = testGetModulePath($PostCaseInfo['ModuleID']);
    $PostCaseInfo['ModulePath'] = $ModulePath;

    $LastEditedBy = $PostCaseInfo['TestUserName'];
    $MailToList = testGetMailToList($PostCaseInfo['MailTo'], $ProjectID);
    $PostCaseInfo['MailTo'] = $MailToList['MailToStr'];
    $PostCaseInfo['CaseKeyword'] = htmlspecialchars(trim($PostCaseInfo['CaseKeyword']));
    $PostCaseInfo['LinkID'] = dbGetValidValueList($PostCaseInfo['LinkID'], 'CaseInfo','CaseID');
    $PostCaseInfo['BugID' ] = dbGetValidValueList($PostCaseInfo['BugID'], 'BugInfo','BugID');

    $PostCaseInfo['ReplyNote'] = htmlspecialchars(trim($PostCaseInfo['ReplyNote']));

    $Columns = '*';
    $RawCaseInfo = dbGetRow('CaseInfo',$Columns,"CaseID = '{$PostCaseInfo[CaseID]}'");
    $TempPostCaseInfo = sysStripSlash($PostCaseInfo);

    $CaseDiffInfo = array();
    $DiffAssoc = 'ProjectID,ModuleID,CaseTitle,CaseType,CasePriority,CaseType,CaseStatus,';
    $DiffAssoc .= 'CaseMethod,CasePlan,AssignedTo,ScriptedBy,ScriptedDate,ScriptStatus,ScriptLocation,';
    $DiffAssoc .= 'MailTo,MarkForDeletion,LinkID,BugID,CaseKeyword,ParentID';
    $CaseDiffInfo += sysArrayDiffAssoc($TempPostCaseInfo, $RawCaseInfo, $DiffAssoc);

    if(isset($CaseDiffInfo['AssignedTo']))
    {
        $EditPostArray['AssignedDate'] = date('Y-m-d H:i:s');
        $PostCaseInfo['AssignedDate'] = $EditPostArray['AssignedDate'];
    }
    else
    {
        $PostCaseInfo['AssignedDate'] = $RawCaseInfo['AssignedDate'];
    }

    if(stripslashes(($PostCaseInfo['CaseSteps'])) != $RawCaseInfo['CaseSteps'])
    {
        $CaseDiffInfo['CaseSteps'] = $PostCaseInfo['CaseSteps'];
    }
    if(isset($CaseDiffInfo['ProjectID']))
    {
        $CaseDiffInfo['ProjectName'] = $ProjectInfo['ProjectName'];
    }
    if(isset($CaseDiffInfo['ModuleID']))
    {
        $CaseDiffInfo['ModulePath'] = $ModulePath;
    }

    $EditPostArray = array();
    $CaseDiffInfo = sysAddSlash($CaseDiffInfo);
    $EditPostArray += $CaseDiffInfo;
    if($PostCaseInfo['ReplyNote'] != '' || $UploadFile)
    {
        $EditPostArray['ReplyNote'] = htmlspecialchars(trim($PostCaseInfo['ReplyNote']));
    }

    // check case step
    if(empty($EditPostArray))
    {
        $ActionMsg['Bingle'] = true;
        return $ActionMsg;
    }
    else
    {
        $LastActionID = testGetLastActionID('Case',$CaseID);
        if($PostCaseInfo['LastActionID'] != $LastActionID)
        {
            $ActionMsg['FailedMsg'][] = $_LANG['CaseAlreadyChanged'];
            return $ActionMsg;
        }
    }

    if($PostCaseInfo['CasePriority'] == '')
    {
        $PostCaseInfo['CasePriority'] = 'NULL';
    }

    $LastEditedBy = $PostCaseInfo['TestUserName'];

    // update Caseinfo
    dbUpdateRow('CaseInfo','ProjectID',"'{$PostCaseInfo[ProjectID]}'",'ModuleID',"'{$PostCaseInfo[ModuleID]}'",
                           'ProjectName',"'{$PostCaseInfo[ProjectName]}'",'ModulePath',"'{$PostCaseInfo[ModulePath]}'",
                           'CaseTitle',"'{$PostCaseInfo[CaseTitle]}'",
                           'CasePriority',"{$PostCaseInfo[CasePriority]}",'CaseType',"'{$PostCaseInfo[CaseType]}'",
                           'CaseStatus',"'{$PostCaseInfo[CaseStatus]}'",
                           'CaseMethod',"'{$PostCaseInfo[CaseMethod]}'",
                           'CasePlan',"'{$PostCaseInfo[CasePlan]}'",
                           'CaseSteps',"'{$PostCaseInfo[CaseSteps]}'",
                           'AssignedTo',"'{$PostCaseInfo[AssignedTo]}'",
                           'AssignedDate', "'{$PostCaseInfo[AssignedDate]}'",
                           'ScriptedBy', "'{$PostCaseInfo[ScriptedBy]}'",
                           'ScriptedDate', "'{$PostCaseInfo[ScriptedDate]}'",
                           'ScriptStatus', "'{$PostCaseInfo[ScriptStatus]}'",
                           'ScriptLocation', "'{$PostCaseInfo[ScriptLocation]}'",
                           'MailTo',"'{$PostCaseInfo[MailTo]}'",
                           'MarkForDeletion',"'{$PostCaseInfo[MarkForDeletion]}'",
                           'LinkID',"'{$PostCaseInfo[LinkID]}'",
                           'BugID',"'{$PostCaseInfo[BugID]}'",
                           'CaseKeyword',"'{$PostCaseInfo[CaseKeyword]}'",
                           'LastEditedBy',"'{$LastEditedBy}'",'LastEditedDate','now()',
                           'ModifiedBy', "CONCAT(ModifiedBy,'{$LastEditedBy}',',')",
                           'ParentID', "'{$PostCaseInfo[ParentID]}'",
                           "CaseID='{$CaseID}'");

    // insert action
    $ActionID = testAddAction('Case',$CaseID,$LastEditedBy,'Edited','',$EditPostArray['ReplyNote']);

    // insert histroy
    $ChangeNote = '';
    foreach($CaseDiffInfo as $Key => $Value)
    {
        $OldValue = sysAddSlash($RawCaseInfo[$Key]);
        $NewValue = $CaseDiffInfo[$Key];
        testAddHistory($ActionID, $Key, $OldValue, $NewValue);
        if($Key == 'CaseSteps')
        {
            $OldValue = '...';
            $NewValue = '...';
        }
        $ChangeNote .= "Changed [b]{$Key}[/b] from [b]\"{$OldValue}\"[/b] to[b]\"{$NewValue}\"[/b]\n";
    }

    $AssignedToUserInfo = testGetUserInfoByName($PostCaseInfo['AssignedTo']);
    if($PostCaseInfo['AssignedTo'] != $PostCaseInfo['TestUserName'])
    {
        $AssignedToMail = $AssignedToUserInfo['Email'];
        $AssignedToUserInfo['RealName'] == '' ? $AssignedToUserInfo['RealName'] = $PostCaseInfo['AssignedTo'] : '';
    }
    $MainMessage = testCreateMailMessage($CaseID,'Edited',$PostCaseInfo['TestRealName'],$AssignedToUserInfo['RealName'], $ChangeNote."\n". $EditPostArray['ReplyNote'],'Case');
    sysMail($AssignedToMail, $MailToList['Email'], 'Case #' . $CaseID . ' ' . $CaseMailTitle, $MainMessage);

    $ActionMsg['Bingle'] = true;
    $ActionMsg['CaseID'] = $CaseID;
    $ActionMsg['ActionID'] = $ActionID;

    return $ActionMsg;
}

/**
 * Get mail template
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param   string  $IdList
 * @param   string  $Action
 * @param   string  $ActionUser
 * @param   string  $AssignedTo
 * @param   string  $Notes
 * @param   string  $ObjType   Bug,Case or Result
 */
function testCreateMailMessage($IdList, $Action, $ActionUser, $AssignedTo, $Notes, $ObjType, $ActionDesc = '')
{
    global $_CFG;
    global $TPL;

    if(empty($IdList))
    {
        return false;;
    }

    // Get css style.
    $CssStyle = join("",file($_CFG['RealRootPath'] . "/Css/Mail.css"));
    $TPL->assign("CssStyle",$CssStyle);

    // Bug Info.
    $ObjList = explode(',', $IdList);
    $TPL->assign("ObjList",$ObjList);

    // Change info.
    $TPL->assign("ActionUser", $ActionUser);
    $TPL->assign("AssignedTo", $AssignedTo);
    $TPL->assign("ObjType", $ObjType);
    $TPL->assign("ActionInfo", date("Y-m-d H:i") . " " . $Action . $ActionDesc . " By ".$ActionUser);
    $TPL->assign("Notes",sysStripSlash($Notes));    // Because the Notes are $_POST vars and has formatted  by addslashes(), so strip the slahes here.

    // Get change info in html.
    return ($TPL->fetch("MailChange.tpl"));
}

/**
 * Set multi info about buglist
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $BugList
 * @param    array  $UserNameList
 * @return   array  $BugList
 */
function testSetBugListMultiInfo($BugList, $UserNameList = '')
{
      foreach($BugList as $Key => $BugInfo)
      {
            $BugList[$Key] = testSetBugMultiInfo($BugInfo, $UserNameList);
      }
      return $BugList;
}

/**
 * Set multi info about bug
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $BugInfo
 * @param    array  $UserNameList
 * @return   array  $BugList
 */
function testSetBugMultiInfo($BugInfo, $UserNameList = '')
{
        if($BugInfo['OpenedBy'] != '')
        {
            $BugInfo['OpenedByName'] = $UserNameList[$BugInfo['OpenedBy']] == '' ? $BugInfo['OpenedBy'] : $UserNameList[$BugInfo['OpenedBy']];
        }
        if($BugInfo['AssignedTo'] != '')
        {
            $BugInfo['AssignedToName'] = $UserNameList[$BugInfo['AssignedTo']] == '' ? $BugInfo['AssignedTo'] : $UserNameList[$BugInfo['AssignedTo']];
        }
        if($BugInfo['ResolvedBy'] != '')
        {
            $BugInfo['ResolvedByName'] = $UserNameList[$BugInfo['ResolvedBy']] == '' ? $BugInfo['ResolvedBy'] : $UserNameList[$BugInfo['ResolvedBy']];
        }
        if($BugInfo['ClosedBy'] != '')
        {
            $BugInfo['ClosedByName'] = $UserNameList[$BugInfo['ClosedBy']] == '' ? $BugInfo['ClosedBy'] : $UserNameList[$BugInfo['ClosedBy']];
        }
        if($BugInfo['LastEditedBy'] != '')
        {
            $BugInfo['LastEditedByName'] = $UserNameList[$BugInfo['LastEditedBy']] == '' ? $BugInfo['LastEditedBy'] : $UserNameList[$BugInfo['LastEditedBy']];
        }

        if($BugInfo['MailTo'] != '')
        {
              $BugInfo['MailToName'] = testGetMailToList($BugInfo['MailTo']);
              $BugInfo['MailToName'] = @join(',',$BugInfo['MailToName']['RealName']);
        }
        else
        {
            $BugInfo['MailToName'] = '';
        }

        if($BugInfo['BugTitle'] != '')
        {
            $UCTitleLength = 24-strlen($BugInfo['BugID']);
            $ListTitleLength = 50;
            $BugInfo['UCTitle'] = sysSubStr($BugInfo['BugTitle'], $UCTitleLength, true);
            $BugInfo['ListTitle'] = sysSubStr($BugInfo['BugTitle'], $ListTitleLength, true);
        }
        if($BugInfo['BugProjectName'] != '')
        {
            $ListTitleLength = 50;
            $BugInfo['BugProjectNameName'] = sysSubStr($BugInfo['BugProjectName'], $ListTitleLength, true);
        }

        $BugInfo = testSetBugInfoLangName($BugInfo);

        return $BugInfo;
}

/**
 * Get buginfo name from $_LANG
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 */
function testSetBugInfoLangName($BugInfo)
{
    global $_LANG;

    if(isset($BugInfo['BugSeverity']))
    {
        $BugInfo['BugSeverityName'] = $_LANG['BugSeveritys'][$BugInfo['BugSeverity']];
    }
    if(isset($BugInfo['BugPriority']))
    {
        $BugInfo['BugPriorityName'] = $_LANG['BugPriorities'][$BugInfo['BugPriority']];
    }
    if(isset($BugInfo['BugType']))
    {
        $BugInfo['BugTypeName'] = $_LANG['BugTypes'][$BugInfo['BugType']];
    }
    if(isset($BugInfo['BugOS']))
    {
        $BugInfo['BugOSName'] = $_LANG['BugOS'][$BugInfo['BugOS']];
    }
    if(isset($BugInfo['BugBrowser']))
    {
        $BugInfo['BugBrowserName'] = $_LANG['BugBrowser'][$BugInfo['BugBrowser']];
    }
    if(isset($BugInfo['BugMachine']))
    {
        $BugInfo['BugMachineName'] = $_LANG['BugMachine'][$BugInfo['BugMachine']];
    }
    if(isset($BugInfo['BugSubStatus']))
    {
        $BugInfo['BugSubStatusName'] = $_LANG['BugSubStatus'][$BugInfo['BugSubStatus']];
    }    
    if(isset($BugInfo['HowFound']))
    {
        $BugInfo['HowFoundName'] = $_LANG['BugHowFound'][$BugInfo['HowFound']];
    }
    if(isset($BugInfo['BugStatus']))
    {
        $BugInfo['BugStatusName'] = $_LANG['BugStatus'][$BugInfo['BugStatus']];
        $BugInfo['BugStatusColor'] = $_LANG['BugStatusColors'][$BugInfo['BugStatus']];
    }
    if(isset($BugInfo['Resolution']))
    {
        $BugInfo['ResolutionName'] = $_LANG['BugResolutions'][$BugInfo['Resolution']];
    }

    return $BugInfo;
}

/*=======================================Bug Functions End==============================*/


/*=======================================Case Funtions Start=========================================*/

/**
 * Set multi info about case
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array $CaseInfo
 * @param  array $UserNameList
 * @return array $CaseInfo
 */
function testSetCaseMultiInfo($CaseInfo, $UserNameList = '')
{
        if($CaseInfo['OpenedBy'] != '')
        {
            $CaseInfo['OpenedByName'] = $UserNameList[$CaseInfo['OpenedBy']] == '' ? $CaseInfo['OpenedBy'] : $UserNameList[$CaseInfo['OpenedBy']];
        }
        if($CaseInfo['AssignedTo'] != '')
        {
            $CaseInfo['AssignedToName'] = $UserNameList[$CaseInfo['AssignedTo']] == '' ? $CaseInfo['AssignedTo'] : $UserNameList[$CaseInfo['AssignedTo']];
        }
        if($CaseInfo['LastEditedBy'] != '')
        {
            $CaseInfo['LastEditedByName'] = $UserNameList[$CaseInfo['LastEditedBy']] == '' ? $CaseInfo['LastEditedBy'] : $UserNameList[$CaseInfo['LastEditedBy']];
        }
        if($CaseInfo['ScriptedBy'] != '')
        {
            $CaseInfo['ScriptedByName'] = $UserNameList[$CaseInfo['ScriptedBy']] == '' ? $CaseInfo['ScriptedBy'] : $UserNameList[$CaseInfo['ScriptedBy']];
        }

		if($CaseInfo['MailTo'] != '')
		{
			  $CaseInfo['MailToName'] = testGetMailToList($CaseInfo['MailTo']);
		      $CaseInfo['MailToName'] = @join(',',$CaseInfo['MailToName']['RealName']);
		}
		else
		{
			$CaseInfo['MailToName'] = '';
		}
		
		if($CaseInfo['CaseTitle'] != '')
		{
            $UCTitleLength = 24-strlen($CaseInfo['CaseID']);
            $ListTitleLength = 50;
	    	$CaseInfo['UCTitle'] = sysSubStr($CaseInfo['CaseTitle'], $UCTitleLength, true);
			$CaseInfo['ListTitle'] = sysSubStr($CaseInfo['CaseTitle'], $ListTitleLength, true);
		}
        if($CaseInfo['ResultID'] != '')
        {
            $CaseInfo['ResultIDName'] = preg_replace("/,$/","", $CaseInfo['ResultID']);
            $ResultList = explode(',', $CaseInfo['ResultID']);
            foreach($ResultList as $ResultID)
            {
                if($ResultID != '')
                {
                    $CaseInfo['ResultIDList'][$ResultID] = $ResultID;
                }
            }
        }

        $CaseInfo = testSetCaseInfoLangName($CaseInfo);

        return $CaseInfo;
}

/**
 * Get caseinfo name from $_LANG
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 */
function testSetCaseInfoLangName($CaseInfo)
{
    global $_LANG;

    if(isset($CaseInfo['CasePriority']))
    {
        $CaseInfo['CasePriorityName'] = $_LANG['CasePriorities'][$CaseInfo['CasePriority']];
    }
    if(isset($CaseInfo['CaseType']))
    {
        $CaseInfo['CaseTypeName'] = $_LANG['CaseTypes'][$CaseInfo['CaseType']];
    }
    if(isset($CaseInfo['CaseStatus']))
    {
        $CaseInfo['CaseStatusName'] = $_LANG['CaseStatuses'][$CaseInfo['CaseStatus']];
    }
    if(isset($CaseInfo['CaseMethod']))
    {
        $CaseInfo['CaseMethodName'] = $_LANG['CaseMethods'][$CaseInfo['CaseMethod']];
    }
    if(isset($CaseInfo['CasePlan']))
    {
        $CaseInfo['CasePlanName'] = $_LANG['CasePlans'][$CaseInfo['CasePlan']];
    }
    if(isset($CaseInfo['MarkForDeletion']))
    {
        $CaseInfo['MarkForDeletionName'] = $_LANG['MarkForDeletions'][$CaseInfo['MarkForDeletion']];
    }
    if(isset($CaseInfo['ScriptStatus']))
    {
        $CaseInfo['ScriptStatusName'] = $_LANG['ScriptStatuses'][$CaseInfo['ScriptStatus']];
    }
    return $CaseInfo;
}

/**
 * Set multi info about case list
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array $CaseList
 * @param  array $UserNameList
 * @return array $CaseList
 */
function testSetCaseListMultiInfo($CaseList, $UserNameList = '')
{
      foreach($CaseList as $Key => $CaseInfo)
      {
            $CaseList[$Key] = testSetCaseMultiInfo($CaseInfo, $UserNameList);
      }
      return $CaseList;
}
/*=======================================Case Funtions End=========================================*/

/*=======================================Result Functions Start====================================*/
/**
 * Set multi info about result list
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array $ResultList
 * @param  array $UserNameList
 * @return array $ResultList
 */
function testSetResultListMultiInfo($ResultList, $UserNameList = '')
{
      foreach($ResultList as $Key => $ResultInfo)
      {
            $ResultList[$Key] = testSetResultMultiInfo($ResultInfo, $UserNameList);
      }
      return $ResultList;
}

/**
 * Set multi info about result
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param  array $ResultInfo
 * @param  array $UserNameList
 * @return array $ResultInfo
 */
function testSetResultMultiInfo($ResultInfo, $UserNameList = '')
{
        if($ResultInfo['OpenedBy'] != '')
        {
            $ResultInfo['OpenedByName'] = $UserNameList[$ResultInfo['OpenedBy']] == '' ? $ResultInfo['OpenedBy'] : $UserNameList[$ResultInfo['OpenedBy']];
        }
        if($ResultInfo['AssignedTo'] != '')
        {
            $ResultInfo['AssignedToName'] = $UserNameList[$ResultInfo['AssignedTo']] == '' ? $ResultInfo['AssignedTo'] : $UserNameList[$ResultInfo['AssignedTo']];
        }
        if($ResultInfo['LastEditedBy'] != '')
        {
            $ResultInfo['LastEditedByName'] = $UserNameList[$ResultInfo['LastEditedBy']] == '' ? $ResultInfo['LastEditedBy'] : $UserNameList[$ResultInfo['LastEditedBy']];
        }
        if($ResultInfo['ResultTitle'] != '')
        {
            $UCTitleLength = 24-strlen($BugInfo['BugID']);
            $ListTitleLength = 50;
            $ResultInfo['UCTitle'] = sysSubStr($ResultInfo['ResultTitle'], $UCTitleLength, true);
            $ResultInfo['ListTitle'] = sysSubStr($ResultInfo['ResultTitle'], $ListTitleLength, true);
        }

		if($ResultInfo['MailTo'] != '')
		{
			  $ResultInfo['MailToName'] = testGetMailToList($ResultInfo['MailTo']);
		      $ResultInfo['MailToName'] = @join(',',$ResultInfo['MailToName']['RealName']);
		}
		else
		{
			$ResultInfo['MailToName'] = '';
		}
        if($ResultInfo['BugID'] != '')
        {
            $ResultInfo['BugIDName'] = preg_replace("/,$/","", $ResultInfo['BugID']);
            $BugList = explode(',', $ResultInfo['BugID']);
            foreach($BugList as $BugID)
            {
                if($BugID != '')
                {
                    $ResultInfo['BugIDList'][$BugID] = $BugID;
                }
            }
        }

        $ResultInfo = testSetResultInfoLangName($ResultInfo);

        return $ResultInfo;
}

/**
 * Get Resultinfo name from $_LANG
 *
 * @author                   Yupeng Lee<leeyupeng@gmail.com>
 */
function testSetResultInfoLangName($ResultInfo)
{
    global $_LANG;

    if(isset($ResultInfo['ResultValue']))
    {
        $ResultInfo['ResultValueName'] = $_LANG['ResultValues'][$ResultInfo['ResultValue']];
        $ResultInfo['ResultValueColor'] = $_LANG['ResultValueColors'][$ResultInfo['ResultValue']];
    }
    if(isset($ResultInfo['ResultOS']))
    {
        $ResultInfo['ResultOSName'] = $_LANG['ResultOS'][$ResultInfo['ResultOS']];
    }
    if(isset($ResultInfo['ResultBrowser']))
    {
        $ResultInfo['ResultBrowserName'] = $_LANG['ResultBrowser'][$ResultInfo['ResultBrowser']];
    }
    if(isset($ResultInfo['ResultStatus']))
    {
        $ResultInfo['ResultStatusName'] = $_LANG['ResultStatuses'][$ResultInfo['ResultStatus']];
    }

    return $ResultInfo;
}

/*=======================================Result Function End======================================*/

/*=======================================Action & History Functions Start=========================*/

/**
 * Add action
 *
 * @author                           Yupeng Lee<leeyupeng@gmail.com>
 * @param     string  $ActionTarget   Bug Case Result
 * @param     string  $IdValuActionNote
 * @param     string  $ActionUser
 * @param     string  $ActionType    Opened, Edited, Resolved, Closed, Activated
 */
function testAddAction($ActionTarget,$IdValue,$ActionUser,$ActionType,$ActionDate='',$ActionNote='')
{
    $ActionID = dbInsertRow('TestAction', "'{$ActionTarget}','{$IdValue}','{$ActionUser}','{$ActionType}',now(),'{$ActionNote}'",
                            'ActionTarget,IdValue,ActionUser,ActionType,ActionDate,ActionNote');
    return $ActionID;
}

/**
 * Add history
 *
 * @author                            Yupeng Lee<leeyupeng@gmail.com>
 * @param     string   $ActionID
 * @param     string   $ActionField
 * @param     string   $OldValue
 * @param     string   $NewValue
 */
function testAddHistory($ActionID, $ActionField, $OldValue, $NewValue)
{
    $HistoryID = dbInsertRow('TestHistory', "'{$ActionID}','{$ActionField}','{$OldValue}','{$NewValue}'",
                                            'ActionID,ActionField,OldValue,NewValue');
    return $HistoryID;
}


/**
 * Get action
 *
 * @author                            Yupeng Lee<leeyupeng@gmail.com>
 * @param     string  $ActionTarget    Bug Case or Result
 * @param     string  $IdValue         BugID CaseID or ResultID
 * @param     string  $ActionOrder     'DESC' or 'ASC' by ActionID
 */
function testGetActionAndFileList($ActionTarget, $IdValue, $ActionOrder = 'DESC')
{
      $ActionList = dbGetList('TestAction', '', "ActionTarget = '{$ActionTarget}' AND IdValue IN ('{$IdValue}')",'', 'ActionID '. $ActionOrder);
      $ReturnActionList = array();
      $ActionCount = count($ActionList);
      $UserList = testGetUserList();

      if($ActionOrder == 'DESC')
      {
          $StartKey = $ActionCount;
          $StepRange = -1;
      }
      elseif($ActionOrder == 'ASC')
      {
          $StartKey = 0;
          $StepRange = 1;
      }
      foreach($ActionList as $Key => $ActionInfo)
      {

          $ActionID = $ActionInfo['ActionID'];

          $ReturnActionList['ActionList'][$StartKey] = $ActionInfo;
          $UserRealName = $UserList[$ActionInfo['ActionUser']]['RealName'];
          $ReturnActionList['ActionList'][$StartKey]['ActionUserName'] = $UserRealName == '' ? $ActionInfo['ActionUser'] : $UserRealName;

          $TmpHistoryList = testGetHistoryList($ActionID);
          foreach($TmpHistoryList as $Key => $HistoryInfo)
          {
              $TmpHistoryList[$Key]['OldValue'] = sysStripSlash($HistoryInfo['OldValue']);
              $TmpHistoryList[$Key]['NewValue'] = sysStripSlash($HistoryInfo['NewValue']);
              if($HistoryInfo['ActionField'] == 'Resolution')
              {
                  $ReturnActionList['ActionList'][$StartKey]['Resolution'] = $HistoryInfo['NewValue'];
                  //break;
              }
          }
          $ReturnActionList['ActionList'][$StartKey]['HistoryList'] = $TmpHistoryList;

          $TmpActionFileList = testGetActionFileList($ActionID);
          $ReturnActionList['FileList'][$ActionID] = $TmpActionFileList;
          $ReturnActionList['ActionList'][$StartKey]['FileList'] = $ReturnActionList['FileList'][$ActionID];

          $ReturnActionList['ActionList'][$StartKey]['AFCount'] = count($TmpHistoryList)+count($TmpActionFileList);

          $StartKey = $StartKey + $StepRange;
      }

      return $ReturnActionList;
}

/**
 * Get action history list
 *
 * @author                   Yupeng Lee <leeyupeng@gmail.com>
 * @param  int   $ActionID
 * @return array $HistoryList
 */
function testGetHistoryList($ActionID)
{
      $HistoryList = dbGetList('TestHistory', '', "ActionID={$ActionID}");
      return $HistoryList;
}

/**
 * Get last actionid
 *
 * @author                   Yupeng Lee <leeyupeng@gmail.com>
 * @param  string $ActionTarget Bug,Case or Result
 * @param  int    $IdValue
 * @return int    $LastActionID
 */
function testGetLastActionID($ActionTarget, $IdValue)
{
    $LastActionInfo = dbGetList('TestAction', 'ActionID', "ActionTarget='{$ActionTarget}' AND IdValue={$IdValue}",'','ActionID DESC','1');
    $LastActionID = $LastActionInfo[0]['ActionID'] >0 ? $LastActionInfo[0]['ActionID'] : 0;
    return $LastActionID;
}

/*=======================================Aciton Functions End===========================*/

/*======================================File Functions Start============================*/

/**
 * Get action uploaded file list
 *
 * @author                         Yupeng Lee <leeyupeng@gmail.com>
 * @param   int   $ActionID
 * @return  array $FileList
 */
function testGetActionFileList($ActionID)
{
    global $_CFG;
    $FileList = dbGetList('TestFile', '', "ActionID='{$ActionID}'");
    foreach($FileList as $Key => $FileInfo)
    {
        $FileList[$Key]['ForceDownload'] = !@in_array($FileInfo['FileType'], $_CFG['NotForceDownloadFileType']) ? '1' : '0';
    }
    return $FileList;
}

/*======================================File Functions End============================*/

/*=======================================Mail Functions Start==============================*/

/**
 * Get maillist
 *
 * @author                     Yupeng Lee<leeyupeng@gmail.com>
 * @param    string $MailToStr
 * @param    int    $ProjectID
 * @return   array  $ReturnMailList
 */
function testGetMailToList($MailToStr, $ProjectID = 0)
{
    $MailToStr = trim($MailToStr);
    $MailToList = explode(',', $MailToStr);
    $ReturnMailList = array();
    $ReturnMailList['MailToArray'] = array();
    if($ProjectID > 0)
    {
        $ProjectUserList = testGetProjectUserList($ProjectID);
    }
    foreach($MailToList as $Key=>$Value)
    {
        $Value = trim($Value);
        if(!empty($Value) && !@in_array($Value,$ReturnMailList['Email']) && !@in_array($Value,$ReturnMailList['MailToArray']))
        {
            if(sysCheckEmailFormat($Value))
            {
                $ReturnMailList['UserName'][$Key] = $Value;
                $ReturnMailList['RealName'][$Key] = $Value;
                $ReturnMailList['Email'][$Key] = $Value;
                $ReturnMailList['MailToArray'][$Key] = $Value;
            }
            else
            {
                $UserInfo = testGetUserInfoByName($Value);
                if(!empty($UserInfo) && ($ProjectID == 0 || ($ProjectID > 0  && $ProjectUserList[$UserInfo['UserName']] != '' )))
                {
                    $ReturnMailList['UserName'][$Key] = $Value;
                    $ReturnMailList['RealName'][$Key] = $UserInfo['RealName'];
                    $ReturnMailList['Email'][$Key] = $UserInfo['Email'];
                    $ReturnMailList['MailToArray'][$Key] = $Value;
                }
            }
        }
    }
    $ReturnMailList['MailToStr'] = join(',', $ReturnMailList['MailToArray']);
    return $ReturnMailList;
}

/*=======================================Mail Functions End==============================*/


/*=======================================Admin Functions Start===========================*/

/**
 * Get project module tree menu html code for admin
 *
 * @author                      Yupeng Lee<leeyupeng@gmail.com>
 * @param   int    $ProjectID
 * @param   string $LinkUrl
 * @param   string $ModuleType  Bug or Case
 * @return  string
 */
function testGetAdminTreeModuleList($ProjectID, $LinkUrl = '', $ModuleType = 'Bug')
{
    $ModuleList = testGetProjectModuleList($ProjectID, $ModuleType);

    $TreeMenuHtml = "<ul>";
    foreach($ModuleList as $ModuleID => $ModuleInfo)
    {
        if($ModuleInfo['IsLeaf'])
        {
            $TreeMenuHtml .= '<li class="ChildNode">';
        }
        else
        {
            $TreeMenuHtml .= '<li class="OpenedNode">';
        }
        if($ModuleInfo['ModuleGrade'] - 0 == 0)
        {
            $TreeMenuHtml .=  "<a href='{$LinkUrl}ProjectID={$ProjectID}&ModuleType={$ModuleType}'><strong>{$ModuleInfo[ModuleName]}</strong></a>";
        }
        else
        {
            $TreeMenuHtml .=  "<a href='{$LinkUrl}ProjectID={$ProjectID}&ModuleType={$ModuleType}&ModuleID={$ModuleID}'>{$ModuleInfo[ModuleName]}</a>";
        }
        if($ModuleInfo['IsLeaf'])
        {
            if($ModuleInfo['IsLastLeaf'])
            {
                $TreeMenuHtml .= str_repeat('</li></ul>', abs($ModuleInfo['ModuleGrade'] - $ModuleList[$ModuleInfo['NextTreeModuleID']]['ModuleGrade'] ));
            }
            else
            {
                $TreeMenuHtml .= '</li>';
            }
        }
        else
        {
            $TreeMenuHtml .= '<ul>';
        }
    }
    return $TreeMenuHtml;
}
/*=======================================Admin Functions End===========================*/


/*===================================xajax function start =============================*/

/**
 * assign Message in ActionMessage.tpl
 *
 * @author                  Yupeng Lee<leeyupeng@gmail.com>
 * @param   object $ResponseObj   the xajaxResponse object
 * @param   string $Message       the message to play
 * @param   string $ClassName     the style of message
 */
function xAssignActionMessage(&$ResponseObj, $Message = "", $ClassName = "")
{
    if($Message == "")
    {
        return;
    }
    $ResponseObj->addAssign("ActionMessage", "style.display" , "block");
    $ResponseObj->addAssign("ActionMessage", "innerHTML", $Message);

    if($ClassName != "")
    {
        $ResponseObj->addAssign("ActionMessage", "className",  $ClassName);
    }

    $ResponseObj->addScript("shadow('ActionMessage', '{$ClassName}')");
}

/**
 * Set slave module value
 *
 * @author                 Yupeng Lee<leeyupeng@gmail.com>
 * @param   int $ProjectID
 * @return  object $objResponse  the xajaxResponse object
 */
function xSetCurrentProject($ProjectID)
{
    $objResponse = new xajaxResponse();
    testSetCurrentProject($ProjectID);
    return $objResponse;
}

/**
 * check user login
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param   array $UserLoginForm  user login info from login form
 * @return  object $objResponse   the xajaxResponse object
 */
function xCheckUserLogin($UserLoginForm)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();
    $TestUserName = strtolower($UserLoginForm['TestUserName']);
    $TestUserPWD  = $UserLoginForm['TestUserPWD'];
    $TestUserInfo = baseJudgeUser($TestUserName, $TestUserPWD);
    if(!$TestUserInfo)
    {
        xAssignActionMessage($objResponse, $_LANG['Message']['ErrorLogin'], "Error");
        $objResponse->addAssign("TestUserPWD", "value", "");
    }
    else
    {
        $_SESSION['TestUserACL'] = baseGetUserACL($TestUserName);
        $_SESSION['TestIsAdmin'] = baseJudgeIsAdmin($TestUserName);
        if(!$_SESSION['TestIsAdmin'])
        {
            $_SESSION['TestIsProjectAdmin'] = baseJudgeIsAdmin($TestUserName, 'ProjectAdmin');
        }
        if(empty($_SESSION['TestUserACL']) && !$_SESSION['TestIsAdmin'])
        {
            xAssignActionMessage($objResponse, $_LANG['Message']['NoPriv'], "Warning");
        }
        else
        {
            if($UserLoginForm['RememberLoginStatus'])
            {
                @setcookie("BFUserName", $TestUserName, time()+1209600,BF_COOKIE_PATH);
                @setcookie("BFUserPWD", baseEncryptUserPWD($TestUserPWD, $TestUserName), time()+1209600,BF_COOKIE_PATH);
                @setcookie("BFRememberStatus", '1', time()+1209600,BF_COOKIE_PATH);
            }
            else
            {
                @setcookie("BFRememberStatus", '', time()-3600,BF_COOKIE_PATH);
            }

            // register session
            $_SESSION['TestUserName'] = $TestUserName;
            $_SESSION['TestRealName'] = $TestUserInfo['RealName'];
            $_SESSION['TestUserEmail'] = $TestUserInfo['Email'];

            $_SESSION['TestUserACLSQL'] = 'ProjectID' . dbCreateIN(join(',', array_keys($_SESSION['TestUserACL'])));
            
            $TestCurrentProjectID = $_COOKIE['TestCurrentProjectID'] != '' && isset($_SESSION['TestUserACL'][$_COOKIE['TestCurrentProjectID']]) ? $_COOKIE['TestCurrentProjectID'] : key($_SESSION['TestUserACL']);
            testSetCurrentProject($TestCurrentProjectID);
            usleep(100);
            xAssignActionMessage($objResponse, $_LANG['Message']['SucceedLogin'], "GoodNews");

            // add user login log
            dbInsertRow('TestUserLog', "'{$TestUserName}','{$_SERVER[REMOTE_ADDR]}',now()", 'UserName,LoginIP,LoginTime');

            if($_SESSION['LoginJumpURI'] == '')
            {
                $_SESSION['LoginJumpURI'] = $_CFG["BaseURL"] . "/index.php";
            }
            $objResponse->addRedirect($_SESSION['LoginJumpURI']);
        }
    }

    return $objResponse;
}

/**
 * select user's language
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param   string $Language
 * @return  object $objResponse   the xajaxResponse object
 */
function xSelectLanguage($Language)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();
    $LangCommon = $_CFG['RealRootPath'] . '/Lang/' . $Language . '/_COMMON.php';
    if(file_exists($LangCommon))
    {
        unset($_LANG);
        require($LangCommon);
        $objResponse->addAssign('ForTestUserName', 'innerHTML', $_LANG['TestUserName']);
        $objResponse->addAssign('ForTestUserPWD', 'innerHTML', $_LANG['TestUserPWD']);
        $objResponse->addAssign('ForLanguage', 'innerHTML', $_LANG['SelectLanguage']);
        $objResponse->addAssign('ForRememberMe', 'innerHTML', $_LANG['RememberMe']);
        $objResponse->addAssign('SubmitLoginBTN', 'value', $_LANG['ButtonLogin']);
        @setcookie("BFUserLang", $Language, time()+1209600,BF_COOKIE_PATH);
    }
    return $objResponse;
}

/**
 * Add project info
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $ProjectForm       the info of form about add project.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminAddProject($ProjectForm)
{
    global $_LANG;

    $objResponse = new xajaxResponse();

    $ProjectForm['ProjectName'] = trim($ProjectForm['ProjectName']);
    /* check validity of post */
    if(empty($ProjectForm['ProjectName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoProjectName'], 'Error');
    }
    /* check wether the project's exist */
    elseif(dbGetRow('TestProject', '', "ProjectName = '{$ProjectForm[ProjectName]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['ProjectExist'], 'Warning');
    }
    /* add project */
    else
    {
        $ProjectManagers = $ProjectForm['ProjectManagers'];
        $ProjectGroupIDs = $ProjectForm['ProjectGroupIDs'];
        if($ProjectManagers != '') $ProjectManagers = ',' . $ProjectForm['ProjectManagers'] . ',';
        if($ProjectGroupIDs != '') $ProjectGroupIDs = ',' . $ProjectForm['ProjectGroupIDs'] . ',';


        $ProjectID = dbInsertRow('TestProject', "'{$ProjectForm[ProjectName]}','{$ProjectForm['DisplayOrder']}', 
                                                 '{$ProjectManagers}','{$ProjectGroupIDs}', 
                                                 '{$ProjectForm[ProjectDoc]}','{$ProjectForm[ProjectPlan]}', 
                                                 '{$_SESSION[TestUserName]}', now(), '{$_SESSION[TestUserName]}', now()"
                                              , "ProjectName, DisplayOrder, ProjectManagers, ProjectGroupIDs, ProjectDoc, ProjectPlan, AddedBy, AddDate, LastEditedBy, LastDate");
        $_SESSION['TestUserACL'][$ProjectID] = 'All';
        $_SESSION['TestUserACLSQL'] = 'ProjectID' . dbCreateIN(join(',', array_keys($_SESSION['TestUserACL'])));
        $SuccessInfo = $ProjectForm['ProjectName'] . $_LANG['SucceedAddPro'] . htmlLink($_LANG['EditProject'], 'AdminProject.php?ActionType=EditProject&ProjectID=' . $ProjectID) . "|" . htmlLink($_LANG['GoOnAddPro'], 'AdminProject.php?ActionType=AddProject') . '|' . htmlLink($_LANG['BackToProjectList'], 'AdminProjectList.php');
        $objResponse->addAppend('ProjectForm', 'style.display', 'none');
        xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
        $objResponse->addRedirect("AdminProjectList.php", 3);
    }
    return $objResponse;
}

/**
 * update project info
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $ProjectForm       the info of form about add project.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminEditProject($ProjectForm)
{
    global $_LANG;

    $objResponse = new xajaxResponse();

    $ProjectForm['ProjectName'] = trim($ProjectForm['ProjectName']);
    /* check validity of post */
    if(empty($ProjectForm['ProjectName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoProjectName'], 'Error');
    }

    /* check wether the project's exist */
    elseif(dbGetRow('TestProject', '', "ProjectName = '{$ProjectForm[ProjectName]}' AND ProjectID <> '{$ProjectForm[ProjectID]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['ProjectExist'], 'Error');
    }

    /* edit project */
    else
    {
        $ProjectManagers = $ProjectForm['ProjectManagers'];
        $ProjectGroupIDs = $ProjectForm['ProjectGroupIDs'];
        if($ProjectManagers != '') $ProjectManagers = ',' . $ProjectForm['ProjectManagers'] . ',';
        if($ProjectGroupIDs != '') $ProjectGroupIDs = ',' . $ProjectForm['ProjectGroupIDs'] . ',';

        $ProjectInfo = dbGetRow('TestProject', '', "ProjectID = '{$ProjectForm[ProjectID]}'");
        dbUpdateRow('TestProject', 'ProjectName', "'{$ProjectForm[ProjectName]}'"
                                 , 'DisplayOrder', "'{$ProjectForm[DisplayOrder]}'"
                                 , 'ProjectManagers', "'{$ProjectManagers}'"
                                 , 'ProjectGroupIDs', "'{$ProjectGroupIDs}'"
                                 , 'ProjectDoc', "'{$ProjectForm[ProjectDoc]}'"
                                 , 'ProjectPlan', "'{$ProjectForm[ProjectPlan]}'"
                                 , 'LastEditedBy', "'{$_SESSION[TestUserName]}'"
                                 , 'LastDate', 'now()'
                                 , "ProjectID = '{$ProjectForm[ProjectID]}'");

        /* Update bug,case and result */
        dbUpdateRow('BugInfo', 'ProjectName', "'{$ProjectForm[ProjectName]}'",  "ProjectID = '{$ProjectForm[ProjectID]}'");
        dbUpdateRow('CaseInfo', 'ProjectName', "'{$ProjectForm[ProjectName]}'",  "ProjectID = '{$ProjectForm[ProjectID]}'");
        dbUpdateRow('ResultInfo', 'ProjectName', "'{$ProjectForm[ProjectName]}'",  "ProjectID = '{$ProjectForm[ProjectID]}'");

        $objResponse->addAppend('ProjectForm', 'style.display', 'none');
        xAssignActionMessage($objResponse, $_LANG['SucceedEditPro'], 'GoodNews');
        $objResponse->addRedirect("AdminProjectList.php", 3);
    }

    return $objResponse;
}

/**
 * Add Module
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $AMForm       the info of form about add module.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminAddModule($AMForm)
{
    global $_LANG;

    $objResponse = new xajaxResponse();

    $AMForm['ModuleName'] = trim($AMForm['ModuleName']);
    if(empty($AMForm['DisplayOrder']))
    {
        $AMForm['DisplayOrder'] = '0';
    }
    /* check validity of post */
    if(empty($AMForm['ModuleName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoModuleName'], 'Error');
    }
    /* check wether the module's exist */
    elseif(dbGetRow('TestModule', '', "ModuleName = '{$AMForm[ModuleName]}' AND ParentID = '{$AMForm[ParentModuleID]}' AND ModuleType='{$AMForm[ModuleType]}' AND ProjectID = '{$AMForm[ProjectID]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['ModuleExist'], 'Warning');
    }
    /* add module */
    else
    {
        if($AMForm['ParentID'] == '0')
        {
            $ModuleGrade = '1';
        }
        else
        {
            $ParentModuleInfo = testGetModuleInfo($AMForm['ParentModuleID']);
            $ModuleGrade = $ParentModuleInfo['ModuleGrade'] + 1;
        }
        $ModuleID = dbInsertRow('TestModule', "'{$AMForm[ModuleType]}','{$AMForm[ProjectID]}','{$AMForm[ModuleName]}','{$ModuleGrade}','{$AMForm[ParentModuleID]}','{$AMForm[AddModuleOwner]}', '{$AMForm[DisplayOrder]}', now(),now()"
                                              , "ModuleType,ProjectID, ModuleName, ModuleGrade, ParentID, ModuleOwner, DisplayOrder, AddDate, LastDate");
        dbUpdateRow('TestProject', 'LastEditedBy', "'{$_SESSION[TestUserName]}'", 'LastDate', 'now()', "ProjectID = '{$AMForm[ProjectID]}'");
        $SuccessInfo = $_LANG['SucceedAddModule'];
        $objResponse->addScript("setTimeout('window.location.reload()',2000);");
        xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
    }
    return $objResponse;
}

/**
 * Edit Module
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $EMForm       the info of form about edit module.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminEditModule($EMForm)
{
    global $_LANG;

    $objResponse = new xajaxResponse();

    $objResponse->addScript("xajax.$('SaveModuleSubmit').disabled='';");
    $EMForm['ModuleName'] = trim($EMForm['ModuleName']);
    if(empty($EMForm['DisplayOrder']))
    {
        $EMForm['DisplayOrder'] = '0';
    }
    /* check validity of post */
    if(empty($EMForm['ModuleName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoModuleName'], 'Error');
    }
    /* check wether the module's exist */
    elseif(dbGetRow('TestModule', '', "ModuleName = '{$EMForm[ModuleName]}' AND ParentID = '{$EMForm[ParentModuleID]}' AND ModuleType='{$EMForm[ModuleType]}' AND ModuleID <> '{$EMForm[ModuleID]}' AND ProjectID = '{$EMForm[ProjectID]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['ModuleExist'], 'Warning');
    }
    elseif($EMForm['ParentModuleID'] == $EMForm['ModuleID'])
    {
        xAssignActionMessage($objResponse, $_LANG['ModuleNotBeRecursive'], 'Warning');
    }
    else
    {
        if($EMForm['DeleteModule'] == '1')
        {
            $ParentModulePath = testGetModulePath($EMForm[ParentModuleID]);
            if(dbGetRow('TestModule','', "ParentID = $EMForm[ModuleID]"))
            {
                xAssignActionMessage($objResponse, $_LANG['ModuleHasChildModule'], 'Warning');
            }
            else
            {
                if($EMForm[ModuleType] == 'Bug')
                {
                    dbUpdateRow('BugInfo','ModuleID', $EMForm['ParentModuleID'], 'ModulePath', "'{$ParentModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");
                }
                else
                {
                    dbUpdateRow('CaseInfo','ModuleID', $EMForm['ParentModuleID'], 'ModulePath', "'{$ParentModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");
                    dbUpdateRow('ResultInfo','ModuleID', $EMForm['ParentModuleID'],'ModulePath', "'{$ParentModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");
                }
                dbDeleteRow('TestModule',"ModuleID = {$EMForm[ModuleID]}");
                $SuccessInfo = $_LANG['SucceedDeleteModule'];
                $objResponse->addRedirect("AdminModuleList.php?ProjectID={$EMForm[ProjectID]}&ModuleType={$EMForm[ModuleType]}&ModuleID={$EMForm[ParentModuleID]}", 2);
                xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
            }
        }
        else
        {
            if(empty($EMForm[ModuleID]))
            {
                if($EMForm['ProjectID'] == $EMForm['RawProjectID'])
                {
                    xAssignActionMessage($objResponse, $_LANG['MergeSelfDenied'], 'Error');
                }
                else
                {
                    if($EMForm['ParentModuleID'] == '0')
                    {
                        $ModuleGrade = '1';
                    }
                    else
                    {
                        $ParentModuleInfo = testGetModuleInfo($EMForm['ParentModuleID']);
                        $ModuleGrade = $ParentModuleInfo['ModuleGrade'] + 1;
                    }

                    // add a new module
                    $ModuleID = dbInsertRow('TestModule', "'{$EMForm[ModuleType]}','{$EMForm[ProjectID]}','{$EMForm[ModuleName]}','{$ModuleGrade}','{$EMForm[ParentModuleID]}','{$EMForm[EditModuleOwner]}', '{$EMForm[DisplayOrder]}', now(),now()"
                                                  , "ModuleType,ProjectID, ModuleName, ModuleGrade, ParentID, ModuleOwner, DisplayOrder, AddDate, LastDate");

                    $ProjectModuleList = testGetModuleList($EMForm['RawProjectID'], $EMForm[ModuleType]);
                    $ChildModuleIDList = array_keys($ProjectModuleList);

                    
                    /* Update bug,case and result */
                    $ModulePath = testGetModulePath($ModuleID);
                    $ProjectInfo = testGetProjectList("ProjectID = '{$EMForm[ProjectID]}'");
                    $ProjectName = $ProjectInfo[$EMForm['ProjectID']]['ProjectName'];
                    dbUpdateRow('BugInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID", $ModuleID, "ProjectID = '{$EMForm[RawProjectID]}' AND ModuleID = 0");
                    dbUpdateRow('CaseInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID", $ModuleID, "ProjectID = '{$EMForm[RawProjectID]}' AND ModuleID = 0");
                    dbUpdateRow('ResultInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID", $ModuleID, "ProjectID = '{$EMForm[RawProjectID]}' AND ModuleID = 0");

                    /* Update Child's ModuleGrade */
                    dbUpdateRow('TestModule', 'ParentID',$ModuleID, "ModuleType = '{$EMForm[ModuleType]}' AND ParentID = 0 AND ProjectID = {$EMForm['RawProjectID']}");
                    dbUpdateRow('TestModule', 'ModuleGrade',"ModuleGrade + {$ModuleGrade} + 1", 'ProjectID', "'{$EMForm[ProjectID]}'",  "ModuleType = '{$EMForm[ModuleType]}' AND ProjectID = {$EMForm['RawProjectID']}");
                    foreach($ChildModuleIDList as $ChildModuleID)
                    {
                        $ModulePath = testGetModulePath($ChildModuleID);
                        dbUpdateRow('BugInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                        dbUpdateRow('CaseInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                        dbUpdateRow('ResultInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                    }

                    $SuccessInfo = $_LANG['SucceedMergeProject'];
                    $objResponse->addScript("setTimeout('window.location.reload()',2000);");
                    xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
                }

            }
            else
            {
                $RawModuleInfo = testGetModuleInfo($EMForm[ModuleID]);
                $RawProjectID = $RawModuleInfo['ProjectID'];
                $ProjectModuleList = testGetProjectModuleList($RawProjectID, $EMForm[ModuleType]);
                $ChildModuleIDList = explode(',', $ProjectModuleList[$EMForm[ModuleID]]['ChildIDs']);
                if(in_array($EMForm['ParentModuleID'], $ChildModuleIDList))
                {
                    xAssignActionMessage($objResponse, $_LANG['ModuleNotBeRecursive'], 'Warning');
                }
                else
                {
                    if($EMForm['ParentModuleID'] == '0')
                    {
                        $ModuleGrade = '1';
                    }
                    else
                    {
                        $ParentModuleInfo = testGetModuleInfo($EMForm['ParentModuleID']);
                        $ModuleGrade = $ParentModuleInfo['ModuleGrade'] + 1;
                    }
                    dbUpdateRow('TestModule', 'ModuleType', "'{$EMForm[ModuleType]}'", 'ProjectID', "'{$EMForm[ProjectID]}'", 'ModuleName', "'{$EMForm[ModuleName]}'",
                                              'ModuleGrade', "'{$ModuleGrade}'", 'ParentID', "'{$EMForm[ParentModuleID]}'",
                                              'ModuleOwner', "'{$EMForm[EditModuleOwner]}'", 'LastDate', "now()",
                                              'DisplayOrder', "'{$EMForm['DisplayOrder']}'",
                                              "ModuleID = '{$EMForm[ModuleID]}'");

                    /* Update bug,case and result */
                    $ModulePath = testGetModulePath($EMForm[ModuleID]);
                    $ProjectInfo = testGetProjectList("ProjectID = '{$EMForm[ProjectID]}'");
                    $ProjectName = $ProjectInfo[$EMForm['ProjectID']]['ProjectName'];
                    dbUpdateRow('BugInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");
                    dbUpdateRow('CaseInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");
                    dbUpdateRow('ResultInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$EMForm[ModuleID]}'");

                    /* Update Child's ModuleGrade */
                    $ParentID = $EMForm[ModuleID];
                    $ProjectModuleList[$EMForm[ModuleID]]['ModuleGrade'] = $ModuleGrade;
                    array_shift($ChildModuleIDList);
                    foreach($ChildModuleIDList as $ChildModuleID)
                    {
                        $ParentID = $ProjectModuleList[$ChildModuleID]['ParentID'];
                        $ChildModuleGrade = $ProjectModuleList[$ParentID]['ModuleGrade']+1;
                        $ProjectModuleList[$ChildModuleID]['ModuleGrade'] = $ChildModuleGrade;
                        dbUpdateRow('TestModule', 'ModuleGrade',$ChildModuleGrade, 'ProjectID', "'{$EMForm[ProjectID]}'",  "ModuleID = {$ChildModuleID}");
                        $ModulePath = testGetModulePath($ChildModuleID);
                        dbUpdateRow('BugInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                        dbUpdateRow('CaseInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                        dbUpdateRow('ResultInfo', 'ProjectID', "'{$EMForm[ProjectID]}'", 'ProjectName', "'{$ProjectName}'", 'ModulePath', "'{$ModulePath}'", "ModuleID = '{$ChildModuleID}'");
                    }

                    $SuccessInfo = $_LANG['SucceedEditModule'];
                    $objResponse->addScript("setTimeout('window.location.reload()',2000);");
                    xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
                }
            }
        }
        dbUpdateRow('TestProject', 'LastEditedBy', "'{$_SESSION[TestUserName]}'", 'LastDate', 'now()', "ProjectID = '{$EMForm[ProjectID]}'");
    }
    return $objResponse;
}


/**
 * Add user info
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $UserForm       the info of form about add user.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminAddUser($UserForm)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();

    $UserForm['UserName'] = strtolower(htmlspecialchars(trim($UserForm['UserName'])));
    $UserForm['RealName'] = htmlspecialchars(trim($UserForm['RealName']));
    $UserForm['Email'] = trim($UserForm['Email']);

    /* check validity of post */
    $ErrorMsg = array();
    if(empty($UserForm['UserName']))
    {
        $ErrorMsg[] = $_LANG['NoUserName'];
    }
    if(empty($UserForm['RealName']))
    {
        $ErrorMsg[] = $_LANG['NoRealName'];
    }
    if($UserForm['UserPassword'] == '')
    {
        $ErrorMsg[] = $_LANG['NoPassword'];
    }
    else
    {
        $UserForm['UserPassword'] = baseEncryptUserPWD($UserForm['UserPassword']);
    }
    if(empty($UserForm['Email']))
    {
        $ErrorMsg[] = $_LANG['NoEmail'];
    }
    elseif(!sysCheckEmailFormat($UserForm['Email']))
    {
        $ErrorMsg[] = $_LANG['InvalidEmail'];
    }
    if(!empty($ErrorMsg))
    {
        xAssignActionMessage($objResponse, join('<br />', $ErrorMsg), 'Error');
    }
    /* check wether the user's exist */
    elseif(dbGetRow($_CFG['UserTable']['TableName'], '', "UserName = '{$UserForm[UserName]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['UserExist'], 'Warning');
    }
    /* add user */
    else
    {
        $UserID = dbInsertRow($_CFG['UserTable']['TableName'], "'{$UserForm[UserName]}','{$UserForm[RealName]}','{$UserForm[UserPassword]}', '{$UserForm[Email]}', '{$_SESSION[TestUserName]}', now(), '{$_SESSION[TestUserName]}', now(), '0'"
            , "UserName, RealName, UserPassword, Email, AddedBy, AddDate, LastEditedBy, LastDate, IsDroped");

        $SuccessInfo = $UserForm['UserName'] . ' ' . $_LANG['SucceedAddUser'] . htmlLink($_LANG['EditUser'], 'AdminUser.php?ActionType=EditUser&UserID=' . $UserID) . "|" . htmlLink($_LANG['GoOnAddUser'], 'AdminUser.php?ActionType=AddUser') . '|' . htmlLink($_LANG['BackToUserList'], 'AdminUserList.php');
        $objResponse->addAppend('UserForm', 'style.display', 'none');
        xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
        $objResponse->addRedirect("AdminUserList.php", 3);
    }
    return $objResponse;
}

/**
 * Edit user info
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $UserForm       the info of form about edit user.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminEditUser($UserForm)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();

    $UserForm['UserName'] = strtolower(htmlspecialchars(trim($UserForm['UserName'])));
    $UserForm['RealName'] = htmlspecialchars(trim($UserForm['RealName']));
    $UserForm['Email'] = trim($UserForm['Email']);

    /* check validity of post */
    $ErrorMsg = array();
    if(empty($UserForm['RealName']))
    {
        $ErrorMsg[] = $_LANG['NoRealName'];
    }
    if(empty($UserForm['Email']))
    {
        $ErrorMsg[] = $_LANG['NoEmail'];
    }
    elseif(!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/i", $UserForm['Email']))
    {
        $ErrorMsg[] = $_LANG['InvalidEmail'];
    }
    if(!empty($ErrorMsg))
    {
        xAssignActionMessage($objResponse, join('<br />', $ErrorMsg), 'Error');
    }
    /* check wether the user's exist */
    elseif(dbGetRow($_CFG['UserTable']['TableName'], '', "UserName = '{$UserForm[UserName]}' AND UserID <> '{$UserForm[UserID]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['UserExist'], 'Warning');
    }
    /* add user */
    else
    {
        $UserInfo = dbGetRow($_CFG['UserTable']['TableName'], '', "UserID = '{$UserForm[UserID]}'");
        $DiffArray = sysArrayDiffAssoc($UserInfo, $UserForm, 'UserID,RealName,Email');
 
        if(empty($DiffArray) && $UserForm['UserPassword'] == '')
        {
            xAssignActionMessage($objResponse, $_LANG['NoEditUser'], 'Warning');
        }
        else
        {
            if($UserForm['UserPassword'] == '')
            {
                $UserForm['UserPassword'] = $UserInfo['UserPassword'];
            }
            else
            {
                $UserForm['UserPassword'] = baseEncryptUserPWD($UserForm['UserPassword']);
            }
           dbUpdateRow($_CFG['UserTable']['TableName'], 'RealName', "'{$UserForm[RealName]}'"
                                                       , 'UserPassword', "'{$UserForm[UserPassword]}'"
                                                       , 'Email', "'{$UserForm[Email]}'"
                                                       , 'LastEditedBy', "'{$_SESSION[TestUserName]}'"
                                                       , 'LastDate', 'now()'
                                                       , "UserID = '{$UserForm[UserID]}'");
            $objResponse->addAppend('UserForm', 'style.display', 'none');
            xAssignActionMessage($objResponse, $_LANG['SucceedEditUser'], 'GoodNews');
            $objResponse->addRedirect("AdminUserList.php", 3);
        }
    }
    return $objResponse;
}

/**
 * Add Group
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $GroupForm       the info of form about add group.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminAddGroup($GroupForm)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();

    $GroupForm['GroupName'] = trim($GroupForm['GroupName']);
    /* check validity of post */
    if(empty($GroupForm['GroupName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoGroupName'], 'Error');
    }
    /* check wether the group's exist */
    elseif(dbGetRow('TestGroup', '', "GroupName = '{$GroupForm[GroupName]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['GroupNameExist'], 'Warning');
    }
    /* add group */
    else
    {
        $GroupUser = $GroupForm['GroupUserNames'];
        $GroupACL = NULL;
        if($GroupUser != '')
        {
            $GroupUserList = testGetUserList($_CFG['UserTable']['UserName'] . dbCreateIN($GroupUser));
            uasort($GroupUserList, 'testCmpPreAppendName');
            $GroupUser = join(',',array_keys($GroupUserList));

            /* Add "," at both end of the variable finally like this: ,wwccss,admin, */
            $GroupUser = ',' . $GroupUser . ',';
        }

        $GroupManager = $GroupForm['GroupManagerNames'];
        if($GroupManager != '')
        {
            $GroupManagerList = testGetUserList($_CFG['UserTable']['UserName'] . dbCreateIN($GroupManager));
            uasort($GroupManagerList, 'testCmpPreAppendName');
            $GroupManager = join(',',array_keys($GroupManagerList));

            /* Add "," at both end of the variable finally like this: ,wwccss,admin, */
            $GroupManager = ',' . $GroupManager . ',';
        }

        $GroupID = dbInsertRow('TestGroup', "'{$GroupForm[GroupName]}','{$GroupManager}','{$GroupUser}', '{$_SESSION[TestUserName]}', now(), '{$_SESSION[TestUserName]}', now()"
                                              , "GroupName, GroupManagers, GroupUser, AddedBy, AddDate, LastEditedBy, LastDate");
        $SuccessInfo = $GroupForm['GroupName'] . $_LANG['SucceedAddGroup'] . htmlLink($_LANG['EditGroup'], 'AdminGroup.php?ActionType=EditGroup&GroupID=' . $GroupID) . "|" . htmlLink($_LANG['GoOnAddGroup'], 'AdminGroup.php?ActionType=AddGroup') . '|' . htmlLink($_LANG['BackToGroupList'], 'AdminGroupList.php');
        $objResponse->addAppend('GroupForm', 'style.display', 'none');
        xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
        $objResponse->addRedirect("AdminGroupList.php", 3);
    }
    return $objResponse;
}

/**
 * Edit Group
 *
 * @author                        Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $GroupForm       the info of form about Edit group.
 * @return   object $objResponse  the xajaxResponse object
 */
function xAdminEditGroup($GroupForm)
{
    global $_LANG, $_CFG;

    $objResponse = new xajaxResponse();

    $GroupForm['GroupName'] = trim($GroupForm['GroupName']);
    /* check validity of post */
    if(empty($GroupForm['GroupName']))
    {
        xAssignActionMessage($objResponse, $_LANG['NoGroupName'], 'Error');
    }
    /* check wether the group's exist */
    elseif(dbGetRow('TestGroup', '', "GroupName = '{$GroupForm[GroupName]}' AND GroupID <> '{$GroupForm[GroupID]}'"))
    {
        xAssignActionMessage($objResponse, $_LANG['GroupNameExist'], 'Warning');
    }
    /* edit group */
    else
    {
        $GroupUser = $GroupForm['GroupUserNames'];
        $GroupACL = NULL;
        if($GroupUser != '')
        {
            $GroupUserList = testGetUserList($_CFG['UserTable']['UserName'] .  dbCreateIN($GroupUser));
            uasort($GroupUserList, 'testCmpPreAppendName');
            $GroupUser = join(',',array_keys($GroupUserList));

            /* Edit "," at both end of the variable finally like this: ,wwccss,admin, */
            $GroupUser = ',' . $GroupUser . ',';
        }
        $GroupManager = $GroupForm['GroupManagerNames'];
        if($GroupManager != '')
        {
            $GroupManagerList = testGetUserList($_CFG['UserTable']['UserName'] . dbCreateIN($GroupManager));
            uasort($GroupManagerList, 'testCmpPreAppendName');
            $GroupManager = join(',',array_keys($GroupManagerList));

            /* Add "," at both end of the variable finally like this: ,wwccss,admin, */
            $GroupManager = ',' . $GroupManager . ',';
        }
        dbUpdateRow('TestGroup', 'GroupName', "'{$GroupForm[GroupName]}'", 'GroupManagers', "'{$GroupManager}'", 'GroupUser', "'{$GroupUser}'"
                                   , 'LastEditedBy', "'{$_SESSION[TestUserName]}'", 'LastDate', 'now()'
                                   , "GroupID ='{$GroupForm[GroupID]}'");
        $SuccessInfo = $GroupForm['GroupName'] . $_LANG['SucceedEditGroup'];
        $objResponse->addAppend('GroupForm', 'style.display', 'none');
        xAssignActionMessage($objResponse, $SuccessInfo, 'GoodNews');
        $objResponse->addRedirect("AdminGroupList.php", 3);
    }
    return $objResponse;
}

/**
 * Set slave module value
 *
 * @author                            Yupeng Lee<leeyupeng@gmail.com>
 * @param    int    $ProjectID
 * @param    string $ModuleListSpanID
 * @param    string $ModuleListName
 * @param    string $ModuleType        Bug or Case
 * @return   object $objResponse       the xajaxResponse object
 */
function xProjectSetSlaveModule($ProjectID, $ModuleListSpanID, $ModuleListName = 'MoudleID', $ModuleType = 'Bug')
{
    $objResponse = new xajaxResponse();

    $OnChangeStr = 'onchange="';
    $OnChangeStr .= 'xajax_xSetModuleOwner(this.value);"';
    $OnChangeStr .= ' class="MyInput RequiredField"';
    $ModuleSelectList = testGetSelectModuleList($ProjectID, $ModuleListName, $_SESSION['TestCurrentModuleID'], $OnChangeStr, $ModuleType);

    $objResponse->addAssign($ModuleListSpanID, 'innerHTML', $ModuleSelectList);

    return $objResponse;
}

/**
 * Set slave module value
 *
 * @author                                Yupeng Lee<leeyupeng@gmail.com>
 * @param    int    $ProjectID
 * @param    string $UserListSpanID
 * @param    string $AssignedUserListName
 * @return   object $objResponse          the xajaxResponse object
 */
function xProjectSetAssignedUser($ProjectID, $UserListSpanID='AssignedToUserList', $AssignedUserListName = 'AssignedTo')
{
    $objResponse = new xajaxResponse();
    $ProjectUserList = testGetProjectUserList($ProjectID, true);
    $ProjectUserList += array('Closed' => 'Closed');
    $SelectAssignUserList = htmlSelect($ProjectUserList, $AssignedUserListName, '','', 'class="NormalSelect MyInput RequiredField"');
    $objResponse->addAssign($UserListSpanID, 'innerHTML', $SelectAssignUserList);
    $objResponse->addAssign('CurrentProjectID', 'value', $ProjectID);
    $objResponse->addClear('MailTo', 'selectDiv');
    return $objResponse;
}

/**
 * Set slave module value
 *
 * @author                                Yupeng Lee<leeyupeng@gmail.com>
 * @param    int    $ProjectID
 * @param    string $UserListSpanID
 * @param    string $AssignedUserListName
 * @return   object $objResponse          the xajaxResponse object
 */
function xProjectSetScriptedBy($ProjectID, $UserListSpanID='ScriptedByList', $AssignedUserListName = 'ScriptedBy')
{
    $objResponse = new xajaxResponse();
    $ProjectUserList = testGetProjectUserList($ProjectID, true);
    unset($ProjectUserList['Active']);
    $ProjectUserList = array(''=>'')+$ProjectUserList;
    $SelectAssignUserList = htmlSelect($ProjectUserList, $AssignedUserListName, '','', 'class="NormalSelect MyInput"');
    $objResponse->addAssign($UserListSpanID, 'innerHTML', $SelectAssignUserList);
    return $objResponse;
}

/**
 * Set module owner to assigned user
 *
 * @author                       Yupeng Lee<leeyupeng@gmail.com>
 * @param    int    $ModuleID
 * @param    string $AssignedTo
 * @return   object $objResponse the xajaxResponse object
 */
function xSetModuleOwner($ModuleID, $AssignedToID = 'AssignedTo')
{
    $objResponse = new xajaxResponse();

    $ModuleInfo = testGetModuleInfo($ModuleID);
    $ModuleOwner = $ModuleInfo['ModuleOwner'];
    if($ModuleOwner == '')
    {
        $ModuleOwner = 'Active';
    }

    $objResponse->addScript("selectOneItem(xajax.$('{$AssignedToID}'), '{$ModuleOwner}');");

    return $objResponse;
}

/**
 * Delete file
 *
 * @author                       Yupeng Lee<leeyupeng@gmail.com>
 * @param     string   $FileID
 * @return   object $objResponse the xajaxResponse object
 */
function xDeleteTestFile($FileID)
{
    $objResponse = new xajaxResponse();

    dbUpdateRow('TestFile', 'IsDroped', "'1'" , "FileID = '{$FileID}'");
    $objResponse->addRemove('TestFile' . $FileID);
    return $objResponse;
}

/**
 * Create user select div(used in mailto input)
 *
 * @author                                 Yupeng Lee<leeyupeng@gmail.com>
 * @param     string   $InputElementID
 * @param     string   $Value
 * @param     string   $SelectListFunction
 * @return    object   $objResponse        the xajaxResponse object
 */
function xCreateSelectDiv($InputElementID, $Value, $SelectListFunction)
{
    $objResponse = new xajaxResponse();

    // Get args number.
    $ArgCount = func_num_args();
    $Value = sysAddSlash($Value);
    $SelectListFuntionParam = '"' . $Value . '"';

    // Get keys to sort by and put them to SortRule array.
    for($I = 3;$I < $ArgCount;$I ++)
    {
        $Param = func_get_arg($I);
        $Param = sysAddSlash($Param);
        $SelectListFuntionParam .= ',"' . $Param . '"';
    }
    eval('$SelectDiv = $SelectListFunction(' . $SelectListFuntionParam . ');');

    $TempKeyStr = $SelectDiv['Key'];
    $TempValueStr = $SelectDiv['Value'];

    $objResponse->addScript("var tempKeyArray = new Array({$TempKeyStr});var tempValueArray = new Array({$TempValueStr});");
    $objResponse->addScript("xajax.$('{$InputElementID}').selectDiv.optionKeyList = tempKeyArray;");
    $objResponse->addScript("xajax.$('{$InputElementID}').selectDiv.optionValueList = tempValueArray;");
    $objResponse->addScript("xajax.$('{$InputElementID}').selectDiv.createOptionDiv(tempKeyArray,tempValueArray);");

    return $objResponse;
}

/**
 * update project info
 *
 * @author                              Yupeng Lee<leeyupeng@gmail.com>
 * @param    array  $EditProjectForm    the info of form about edit project.
 * @return   object   $objResponse      the xajaxResponse object
 */
function xModle($EditProjectForm)
{
    global $_LANG;

    $objResponse = new xajaxResponse();
    xAssignActionMessage($objResponse, $_LANG['Message']['SucceedLogin'], "GoodNews");

    return $objResponse;
}

/*===================================xajax function end =============================*/

/**
 *
 * @author                 Yupeng Lee <leeyupeng@gmail.com>
 * @param   string $Module Bug,Case or Result
 */
function testSetCustomFields($Mode)
{
    global $_LANG;
    global $TPL;

    /* Create custom fields selected list */
    $DefaultFields = $_LANG['Default' . $Mode . 'QueryFields'];
    if(!empty($_POST['FieldsToShow']))
    {
        @setcookie($Mode . "CustomFields", $_POST['FieldsToShow'], time()+1209600, BF_COOKIE_PATH);
        jsGoTo($_SERVER['REQUEST_URI']);
        exit;
    }
    if(!empty($_COOKIE[$Mode . "CustomFields"]))
    {
        $CustomFields = explode(",", $_COOKIE[$Mode . "CustomFields"]);
        foreach($CustomFields as $FieldName)
        {
            $FieldsToShow[$FieldName] = $_LANG[$Mode  . "Fields"][$FieldName];
        }
    }
    else
    {
        $FieldsToShow = $DefaultFields;
    }

    $ACLAttrib = 'multiple="multiple" size="10" class="MultiSelect" style="width:150px"';
    $FieldsToSelectList = htmlSelect($_LANG[$Mode . "QueryField"], 'FieldsToSelectList', '', '', $ACLAttrib);
    $FieldsToShowList = htmlSelect($FieldsToShow, 'FieldsToShowList', '','', $ACLAttrib);
    $TPL->assign("FieldsToShow", $FieldsToShow);
    $TPL->assign("FieldsToSelectList", $FieldsToSelectList);
    $TPL->assign("FieldsToShowCount", count($FieldsToSelectList));
    $TPL->assign("FieldsToShowList", $FieldsToShowList);
    $TPL->assign("DefaultFieldsText",  '"' . join('","', $DefaultFields) . '"');
    $TPL->assign("DefaultFieldsValue", '"' . join('","', @array_keys($DefaultFields)) . '"');

    return $FieldsToShow;
}

/**
 *
 * @author                         Yupeng Lee <leeyupeng@gmail.com>
 * @param  string  $SearchUserName
 * @param  int     $ProjectID
 * @return string  $SelectDiv
 */
function selectDivProjectUserList($SearchUserName, $ProjectID)
{
    $SearchUserName = trim($SearchUserName);
    $SearchUserName = str_replace('\\', '\\\\', $SearchUserName);
    $SearchUserName = str_replace('.', '\\.', $SearchUserName);
    $SearchUserName = str_replace('[', '\\[', $SearchUserName);
    $SearchUserName = str_replace('{', '\\{', $SearchUserName);
    $SearchUserName = str_replace('+', '\\+', $SearchUserName);
    $SearchUserName = str_replace('(', '\\(', $SearchUserName);
    $SearchUserName = str_replace('|', '\\|', $SearchUserName);
    $SearchUserName = str_replace('?', '\\?', $SearchUserName);
    $SearchUserName = str_replace('*', '\\*', $SearchUserName);
    $SearchUserName = str_replace('"', '\\"', $SearchUserName);
    $SearchUserName = str_replace('$', '\\$', $SearchUserName);
    $SearchUserName = str_replace('^', '\\^', $SearchUserName);
    $SearchUserName = htmlspecialchars($SearchUserName);

    $ProjectUserList = testGetProjectUserList($ProjectID);
    $TempKeyArray = array();
    $TempValueArray = array();

    if($SearchUserName != '')
    {
        asort($ProjectUserList);
        foreach($ProjectUserList as $UserName => $RealName)
        {
            if(preg_match("/".$SearchUserName."/", $UserName) || preg_match("/".$SearchUserName."/", $RealName))
            {
                $TempKeyArray[] = "'{$UserName}'";
                if(!preg_match("/\[$UserName\]/i", $RealName))
                {
                    $TempValueArray[] = "'{$RealName}[{$UserName}]'";
                }
                else
                {
                    $TempValueArray[] = "'{$RealName}'";
                }
            }
        }
    }

    $SelectDiv = array('Key'=>join(',',$TempKeyArray), 'Value'=>join(',',$TempValueArray));
    return $SelectDiv;
}
?>
