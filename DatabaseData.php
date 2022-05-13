<?php
    class DatabaseData 
    {
        public static function isAuthenticationSuccessful() 
        {
            $oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
            $oQueryWithLogin = $oConnection->query('select session_id from users where session_id = \'' . $_COOKIE['session'] . '\'');
            if ($aResult = $oQueryWithLogin->fetch_assoc()) {
                return true;
            } else {
                return false;
            }
            $oConnection->close();
        }
        
        public static function getTasksData() 
        {
            $aResult = [];
            $oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
            $oQueryWithLogin = $oConnection->query(
                'select caption, priority.value as priority, 
                expiration_date, 
                users.name, 
                users.surname, 
                status.value as status
                from tasks
                join status on tasks.status = status.id
                join priority on tasks.priority = priority.id
                join users on tasks.responsible = users.id'
            );
            while ($aResultRow = $oQueryWithLogin->fetch_assoc())
            {
                $aResult[] = $aResultRow;
            }
            return $aResult;
        }
    }