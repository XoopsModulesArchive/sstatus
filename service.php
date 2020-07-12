<?php
require_once('header.php');
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php'); 

// Always set main template before including the header
$xoopsOption['template_main'] = 'sstatus_service.html';
include(XOOPS_ROOT_PATH . '/header.php');
    
// Get the id of the service
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
}

$start = $limit = 0;
if(isset($_GET['limit'])){
    $limit = intval($_GET['limit']);
}
if(isset($_GET['start'])){
    $start = intval($_GET['start']);
}

// Make sure start is greater than 0
$start = max($start, 0);

// Make sure limit is set
$limit = max($limit, $sstatus_max);
$limit = min($limit, 100);

// Create service and memo objects
$hService       =& xoops_getmodulehandler('service', 'sstatus');
$serviceInfo    =& $hService->get($id);
$member_handler =& xoops_gethandler('member');
$memoList       = $serviceInfo->getMemos($limit, $start);

// Set values for pagveNav object
$total          = $serviceInfo->getMemoCount();
$pageNav        = new XoopsPageNav($total, $limit, $start, "id=$id&start");

foreach($memoList as $memo){
    $user =& $member_handler->getUser($memo->getVar('uid'));    // Create user object
    $aMemos[] = array('id'=>$memo->getVar('id'),
                   'serviceid'=>$memo->getVar('serviceid'),
                   'uid'=>$memo->getVar('uid'),
                   'memo'=>$memo->getVar('memo'),
                   'status'=>$memo->getVar('status'),
                   'posted'=>$memo->posted(),
                   'uname'=>$user->getVar('uname'),
                   'userinfo'=>XOOPS_URL . '/userinfo.php?uid=' . $memo->getVar('uid'));    //Link to view user info page
}

if(!$lastRec  =& $serviceInfo->getLastMemo()) {     // If there are no memos
    $xoopsTpl->assign('sstatus_userInfo', '');      // Fill variables with nothing
    $xoopsTpl->assign('sstatus_lastUser', '');
} else {
    $lastUser =& $member_handler->getUser($lastRec->getVar('uid'));
    $xoopsTpl->assign('sstatus_userInfo', XOOPS_URL . '/userinfo.php?uid=' . $lastRec->getVar('uid'));
    $xoopsTpl->assign('sstatus_lastUser', $lastUser->getVar('uname'));
}
// Smarty variables
if(isset($aMemos)){
    $xoopsTpl->assign('sstatus_memoList', $aMemos);
}
$xoopsTpl->assign('sstatus_serviceInfoTitle', _SSTATUS_SERVICE_INFO_TITLE);
$xoopsTpl->assign('sstatus_serviceid', $serviceInfo->getVar('id'));
$xoopsTpl->assign('sstatus_serviceName', $serviceInfo->getVar('name'));
$xoopsTpl->assign('sstatus_serviceDesc', $serviceInfo->getVar('description'));
$xoopsTpl->assign('sstatus_serviceStatus', $serviceInfo->getVar('status'));
$xoopsTpl->assign('sstatus_lastUpdated', $serviceInfo->lastUpdated());
$xoopsTpl->assign('sstatus_modifyService', _SSTATUS_MODIFY_SERVICE);
$xoopsTpl->assign('sstatus_removeService', _SSTATUS_REMOVE_SERVICE);
$xoopsTpl->assign('sstatus_addMemo', _SSTATUS_ADD_MEMO);
$xoopsTpl->assign('sstatus_name', _SSTATUS_NAME);
$xoopsTpl->assign('sstatus_desc', _SSTATUS_DESC);
$xoopsTpl->assign('sstatus_memoText', _SSTATUS_MEMO_TEXT);
$xoopsTpl->assign('sstatus_currStatus', _SSTATUS_CURRSTATUS);
$xoopsTpl->assign('sstatus_lastUpdatedText', _SSTATUS_LASTUPDATED);
$xoopsTpl->assign('sstatus_updatedBy', _SSTATUS_UPDATED_BY);
$xoopsTpl->assign('sstatus_posted', _SSTATUS_POSTED);
$xoopsTpl->assign('sstatus_status', _SSTATUS_STATUS);
$xoopsTpl->assign('sstatus_previousInfo', _SSTATUS_PREVIOUS_INFO);
$xoopsTpl->assign('sstatus_pageNav', $pageNav->renderNav());

include(XOOPS_ROOT_PATH . '/footer.php');
//redirect_header("index.php", 3, $message);
?>