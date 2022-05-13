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
                users.patronymic_name,
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

        public static function getAllResponsibles()
        {
            $aResult = [];
            $oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
            $oQueryWithLogin = $oConnection->query(
                'select users.name, 
                users.surname, 
                users.patronymic_name
                from tasks
                join users on tasks.responsible = users.id
                where tasks.responsible is not null 
                group by tasks.responsible'
            );
            while ($aResultRow = $oQueryWithLogin->fetch_assoc())
            {
                $aResult[] = $aResultRow;
            }
            return $aResult;
        }
    }