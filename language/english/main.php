<?php
//$Id: main.php,v 1.1 2004/06/01 16:24:59 ackbarr Exp $
// define('', '');

define('_SSTATUS_ADD_MEMO', 'Update Status');
define('_SSTATUS_ADD_OPTIONAL', '(optional)');
define('_SSTATUS_ADD_SERVICE', 'Add a Service');
define('_SSTATUS_ADD_TITLE', 'Add Service');
define('_SSTATUS_BUTTON_DELETEMEMO', 'Delete Message');
define('_SSTATUS_BUTTON_DELETESERVICE', 'Delete Service');
define('_SSTATUS_BUTTON_SUBMIT', 'Submit');
define('_SSTATUS_BUTTON_RESET', 'Reset');
define('_SSTATUS_CURRSTATUS', 'Current Status:');
define('_SSTATUS_DESC', 'Description:');
define('_SSTATUS_INDEX_TITLE', 'Service Status Overview');
define('_SSTATUS_LASTUPDATED', 'Last Updated:');
define('_SSTATUS_MEMO_TEXT', 'Status Message:');
define('_SSTATUS_MODIFY_MEMO', 'Modify Status Information');
define('_SSTATUS_MODIFY_SERVICE', 'Modify Service Information');
define('_SSTATUS_NAME', 'Service Name:');
define('_SSTATUS_PREVIOUS_INFO', 'Previous Information:');
define('_SSTATUS_REMOVE_SERVICE', 'Remove Service');
define('_SSTATUS_SERVICE_INFO_TITLE', 'Service Information');
define('_SSTATUS_START_STATUS', 'Starting Status:');
define('_SSTATUS_STATUS', 'Status:');
define('_SSTATUS_OPTION1', 'Online');
define('_SSTATUS_OPTION2', 'Minor Problems');
define('_SSTATUS_OPTION3', 'Offline');
define('_SSTATUS_UPDATED_BY', 'Updated By:');
define('_SSTATUS_POSTED', 'Posted:');
define('_SSTATUS_GROUP_PERM_NAME', 'Permissions');
define('_SSTATUS_MODIFIED_BY', 'Modified by: %s on %s');

// Permissions variables
define('_SSTATUS_CATEGORY1', 'Add Service');
define('_SSTATUS_CATEGORY2', 'Delete Service');
define('_SSTATUS_CATEGORY3', 'Delete Memo');
define('_SSTATUS_CATEGORY4', 'Modify Service');
define('_SSTATUS_CATEGORY5', 'Modify Status message');
define('_SSTATUS_CATEGORY6', 'Update Service Status');
define('_SEC_ADD_SERVICE', '1');
define('_SEC_DELETE_SERVICE','2');
define('_SEC_DELETE_MEMO', '3');
define('_SEC_MODIFY_SERVICE', '4');
define('_SEC_MODIFY_MEMO', '5');
define('_SEC_ADD_MEMO', '6');

// Service Message constants
define('_SSTATUS_MESSAGE_ADD_SERVICE', 'Service was added');
define('_SSTATUS_MESSAGE_ADD_SERVICE_ERROR', 'Error: service was not added');
define('_SSTATUS_MESSAGE_ADD_SERVICE_PERM_ERROR', 'You do not have permission to add a service');
define('_SSTATUS_MESSAGE_MODIFY_SERVICE', 'Service info was updated');
define('_SSTATUS_MESSAGE_MODIFY_SERVICE_ERROR', 'Error: service info was not updated');
define('_SSTATUS_MESSAGE_MODIFY_SERVICE_PERM_ERROR', 'You do not have permission to modify a service');
define('_SSTATUS_MESSAGE_DELETE_SERVICE', 'Service was deleted successfully');
define('_SSTATUS_MESSAGE_DELETE_SERVICE_ERROR', 'Error: service was not deleted');

// Memo Message constants
define('_SSTATUS_MESSAGE_ADD_MEMO', 'Status was updated');
define('_SSTATUS_MESSAGE_ADD_MEMO_ERROR', 'Error: status was not updated');
define('_SSTATUS_MESSAGE_ADD_MEMO_BLANK', 'You must add a message to the updated status');
define('_SSTATUS_MESSAGE_ADD_MEMO_PERM_ERROR', 'You do not have permission to update a service status');
define('_SSTATUS_MESSAGE_MODIFY_MEMO', 'Status information was updated');
define('_SSTATUS_MESSAGE_MODIFY_MEMO_ERROR', 'Error: status information was not updated');
define('_SSTATUS_MESSAGE_MODIFY_MEMO_PERM_ERROR', 'You do not have permission to modify a service status');
define('_SSTATUS_MESSAGE_DELETE_MEMO', 'Status message was deleted successfully');
define('_SSTATUS_MESSAGE_DELETE_MEMO_ERROR', 'Error: status message was not deleted');
?>