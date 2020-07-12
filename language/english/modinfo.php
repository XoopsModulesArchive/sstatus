<?php
//$Id: modinfo.php,v 1.1 2004/06/01 16:24:59 ackbarr Exp $
define('_MI_SSTATUS_NAME', 'Service Status');
define('_MI_SSTATUS_DESC', 'Used to display a quick overview of IT services with their current status.');
define('_MI_SSTATUS_BNAME1','Service Status Overview');
define('_MI_SSTATUS_BNAME1_DESC','Gives a list of the most recently updated services');
define('_MI_SSTATUS_BNAME2','SSTATUS Options');
define('_MI_SSTATUS_BNAME2_DESC','Gives the admin options for updating services and memos');
define('_MI_NEWS_SMNAM1','Submit');
define('_MI_NEWS_SMNAM2','Archive');

// Config constants
define('_MI_PAGENAV_MAX', 'Number of services and status messages to display per page');
define('_MI_PAGENAV_MAXDSC', '');
//define('_MI_PAGENAV_MIN', 'Set maximum number of services to display per page');
//define('_MI_PAGENAV_MINDSC', '');
//define('_MI_PAGENAV_DEFAULT', 'Set default number of services to display per page');
//define('_MI_PAGENAV_DEFAULTDSC', '');

define('_MI_SSTATUS_GLOBAL_NOTIFY','Global');
define('_MI_SSTATUS_GLOBAL_NOTIFYDSC','Global service notification options');

define('_MI_SSTATUS_SERVICE_NOTIFY','Service');
define('_MI_SSTATUS_SERVICE_NOTIFYDSC','Notification options that apply to the current service');

define('_MI_SSTATUS_GLOBAL_NEWSERVICE_NOTIFY','New Service');
define('_MI_SSTATUS_GLOBAL_NEWSERVICE_NOTIFYCAP','Notify me when a new service is created');
define('_MI_SSTATUS_GLOBAL_NEWSERVICE_NOTIFYDSC','Receive notification when a new service is created');
define('_MI_SSTATUS_GLOBAL_NEWSERVICE_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : New service');

define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_NOTIFY','Modify Service Info');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_NOTIFYCAP','Notify me when a service\'s info has been modified');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_NOTIFYDSC','Receive notification when a service\'s info has been modified');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Modify service snfo');

define('_MI_SSTATUS_GLOBAL_REMOVEDSERVICE_NOTIFY','Delete Service');
define('_MI_SSTATUS_GLOBAL_REMOVEDSERVICE_NOTIFYCAP','Notify me when a service has been deleted');
define('_MI_SSTATUS_GLOBAL_REMOVEDSERVICE_NOTIFYDSC','Receive notification when a service has been deleted');
define('_MI_SSTATUS_GLOBAL_REMOVEDSERVICE_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Delete service');

define('_MI_SSTATUS_GLOBAL_CHANGEDSTATUS_NOTIFY','Update Status');
define('_MI_SSTATUS_GLOBAL_CHANGEDSTATUS_NOTIFYCAP','Notify me when a service status has been updated');
define('_MI_SSTATUS_GLOBAL_CHANGEDSTATUS_NOTIFYDSC','Receive notification when a service status has been updated');
define('_MI_SSTATUS_GLOBAL_CHANGEDSTATUS_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Updated service status');

define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_NOTIFY','Modify this service\'s info');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_NOTIFYCAP','Notify me when this service\'s info has been modified');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_NOTIFYDSC','Receive notification when this service\'s info has been modified');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Modify this service\'s info');

define('_MI_SSTATUS_SERVICE_REMOVEDSERVICE_NOTIFY','Delete this service');
define('_MI_SSTATUS_SERVICE_REMOVEDSERVICE_NOTIFYCAP','Notify me when this service has been deleted');
define('_MI_SSTATUS_SERVICE_REMOVEDSERVICE_NOTIFYDSC','Receive notification when this service has been deleted');
define('_MI_SSTATUS_SERVICE_REMOVEDSERVICE_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Delete this service');

define('_MI_SSTATUS_SERVICE_CHANGEDSTATUS_NOTIFY','Update this status');
define('_MI_SSTATUS_SERVICE_CHANGEDSTATUS_NOTIFYCAP','Notify me when this service status has been updated');
define('_MI_SSTATUS_SERVICE_CHANGEDSTATUS_NOTIFYDSC','Receive notification when this service status has been updated');
define('_MI_SSTATUS_SERVICE_CHANGEDSTATUS_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Updated service status');

define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_STATUS_NOTIFY','Modify status message');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_STATUS_NOTIFYCAP','Notify me when a service\'s status message has been modified');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_STATUS_NOTIFYDSC','Receive notification when a service\'s status message has been modified');
define('_MI_SSTATUS_GLOBAL_MODIFIEDSERVICE_STATUS_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Modify status message');

define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_STATUS_NOTIFY','Modify this status message');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_STATUS_NOTIFYCAP','Notify me when this service\'s status message has been modified');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_STATUS_NOTIFYDSC','Receive notification when this service\'s status message has been modified');
define('_MI_SSTATUS_SERVICE_MODIFIEDSERVICE_STATUS_NOTIFYSBJ','[{X_SITENAME}] {X_MODULE} auto-notify : Modify this service\'s status message');

define('_MI_SSTATUS_TEMP_INDEX','Template for index.php');
define('_MI_SSTATUS_TEMP_SERVICE','Template for service.php');
define('_MI_SSTATUS_TEMP_ADD','Template for addservice.php');
define('_MI_SSTATUS_TEMP_EDIT','Template for modifyService.php');
define('_MI_SSTATUS_TEMP_MEMO', 'Template for addMemo.php');
define('_MI_SSTATUS_TEMP_MODIFYMEMO', 'Template for modifyMemo.php');

define('_MI_SSTATUS_MENU_NEWSERVICE', 'Add a Service');
define('_MI_SSTATUS_MENU_MODIFYSERVICE', 'Modify a Service');
define('_MI_SSTATUS_MENU_REMOVESERVICE', 'Remove a Service');
define('_MI_SSTATUS_MENU_NEWMEMO', 'Update a Service Status');
define('_MI_SSTATUS_MENU_VIEWSERVICES', 'View All Services');
define('_MI_SSTATUS_MENU_VIEWSERVICEDETAILS', 'View Service Details');
define('_MI_SSTATUS_MENU_GROUP_PERM', 'Group Permissions');
?>