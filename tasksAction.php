<?php
require_once('DatabaseData.php');

$aTasksData = DatabaseData::getTasksData();
$aResponsibleForTasks = DatabaseData::getAllResponsibles();
if (DatabaseData::isAuthenticationStatus()) {
    echo json_encode([
        'isAuthentication' =>  DatabaseData::isAuthenticationStatus(),
        'tasksData' => DatabaseData::getTasksData(),
        'responsibles' => DatabaseData::getAllResponsibles()
    ]);
}
else {
    echo json_encode([
        'isAuthentication' =>  DatabaseData::isAuthenticationStatus()
    ]);
}
?>