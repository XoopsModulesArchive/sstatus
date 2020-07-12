<?php
//$id$
require_once('header.php');
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');

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

// Always set main template before including the header
$xoopsOption['template_main'] = 'sstatus_index.html';

// Include the page header
require(XOOPS_ROOT_PATH.'/header.php');

// Create $service object
$hService =& xoops_getmodulehandler('service', 'sstatus');
$crit = new Criteria('','');
$crit->setOrder('DESC');
$crit->setSort('lastUpdated');

$crit->setLimit($limit);
$crit->setStart($start);
$total = $hService->getCount($crit);

$serviceInfo = $hService->getObjects($crit);            // Get service information
$pageNav = new  XoopsPageNav($total, $limit, $start);   // New PageNav object

$aServices = array();
foreach($serviceInfo as $service) {
    $aServices[] = array('id'=>$service->getVar('id'),
                    'name'=>$service->getVar('name'),
                    'status'=>$service->getVar('status'),
                    'lastUpdated'=>$service->lastUpdated(),
                    'url'=>XOOPS_URL . '/modules/sstatus/service.php?id=' . $service->getVar('id'));
}

// Smarty variables - $xoopsTpl->assign('', );
$xoopsTpl->assign('sstatus_serviceList', $aServices);
$xoopsTpl->assign('sstatus_pageNav', $pageNav->renderNav());
$xoopsTpl->assign('sstatus_indexTitle', _SSTATUS_INDEX_TITLE);
$xoopsTpl->assign('sstatus_addService', _SSTATUS_ADD_SERVICE);
$xoopsTpl->assign('sstatus_modifyService', _SSTATUS_MODIFY_SERVICE);
$xoopsTpl->assign('sstatus_name', _SSTATUS_NAME);
$xoopsTpl->assign('sstatus_currStatus', _SSTATUS_CURRSTATUS);
$xoopsTpl->assign('sstatus_lastUpdated', _SSTATUS_LASTUPDATED);


// Include the page footer
require(XOOPS_ROOT_PATH.'/footer.php');
?>