<?php
require_once('header.php');

// Check user permissions
global $xoopsModule;
$perm_name = _SSTATUS_GROUP_PERM_NAME;      // Set the permission name that is being checked
if(isset($_GET['category_id'])){
    $perm_itemid = intval($_GET['category_id']);
} else {
    $perm_itemid = 0;
}
if($xoopsUser){     // Get user's groups
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
$module_id = $xoopsModule->getVar('mid');
$gperm_handler =& xoops_gethandler('groupperm');

// Make sure user doesn't manually type URL
if($gperm_handler->checkRight($perm_name, _SEC_ADD_MEMO, $groups, $module_id)){
} else {
    $message = _SSTATUS_MESSAGE_ADD_SERVICE_PERM_ERROR;
    redirect_header("index.php", 3, $message);
}

if(!isset($_POST['submitted']))        // Initial load of page
{
    // Always set main template before including the header
    $xoopsOption['template_main'] = 'sstatus_add.html';
    include(XOOPS_ROOT_PATH . '/header.php');
    // Smarty variables
    $xoopsTpl->assign('sstatus_addServiceTitle', _SSTATUS_ADD_TITLE);
    $xoopsTpl->assign('sstatus_serviceName', _SSTATUS_NAME);
    $xoopsTpl->assign('sstatus_serviceDesc', _SSTATUS_DESC);
    $xoopsTpl->assign('sstatus_serviceStatus', _SSTATUS_START_STATUS);
    $xoopsTpl->assign('sstatus_serviceMemo', _SSTATUS_MEMO_TEXT);
    $xoopsTpl->assign('sstatus_optional', _SSTATUS_ADD_OPTIONAL);
    $xoopsTpl->assign('sstatus_submit', _SSTATUS_BUTTON_SUBMIT);
    $xoopsTpl->assign('sstatus_reset', _SSTATUS_BUTTON_RESET);
    $xoopsTpl->assign('sstatus_option1', _SSTATUS_OPTION1);
    $xoopsTpl->assign('sstatus_option2', _SSTATUS_OPTION2);
    $xoopsTpl->assign('sstatus_option3', _SSTATUS_OPTION3);

    include(XOOPS_ROOT_PATH . '/footer.php');
} else {
// Add new service
$hService =& xoops_getmodulehandler('service', 'sstatus');
$service =& $hService->create();

// Fill variables in $service class for new service
$service->setVar('name', $_POST['serviceName']);
$service->setVar('description', $_POST['description']);
$service->setVar('status', $_POST['startStatus']);



if($hService->insert($service))     // Check if new service got inserted into DB
{
    if(strlen($_POST['memo']) > 0) {    // If there is something in the memo field
        $memo =& $service->createMemo($_POST['memo'], $_POST['startStatus']); 
        $service->insertMemo($memo);        // Insert memo into sstatus_memo DB
    }
    // Notification
    $notification_handler =& xoops_gethandler('notification');
    // Notification array
    $tags = array();
    $tags['SERVICE_NAME'] = $service->getVar('name');
    
    $notification_handler->triggerEvent('global', 0, 'new_service', $tags);
    $message = _SSTATUS_MESSAGE_ADD_SERVICE;     // Successfully added new service
} else {
    $message = _SSTATUS_MESSAGE_ADD_SERVICE_ERROR . $service->getHtmlErrors();     // Unsuccessfully added new service
}
redirect_header("index.php", 3, $message);
}
?>