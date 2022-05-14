<?php
    class DatabaseData 
    {
        public static function isAuthenticationStatus()
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
            $sQuery = 'select caption, priority.value as priority, 
                expiration_date, 
                concat(r.surname, 
                    \' \',
                r.name,
                    \' \',
                r.patronymic_name) as rName,
                status.value as status,
                description,
                date_of_creation,
                update_date,
                creator,
                responsible,
                tasks.id,
                concat(c.surname, 
                    \' \',
                c.name,
                    \' \',
                c.patronymic_name) as cName
                from tasks 
                join status on tasks.status = status.id
                join priority on tasks.priority = priority.id
                join users as r on tasks.responsible = r.id
                join users as c on tasks.creator = c.id';
            if (!empty($_POST['filterData'])) {
                switch ($_POST['filterData']) {
                    case 'today': {
                        $oCurrentDate = new DateTime(date('Y-m-d'));
                        $sQuery = $sQuery.'where expiration_date=\''.$oCurrentDate->format('Y-m-d').'\'';
                        break;
                    }
                    case 'duringThisWeek': {
                        $oNextWeekDate = new DateTime(date('Y-m-d'));
                        $oNextWeekDate->add(new DateInterval('P1W'));
                        $sQuery = $sQuery.'where expiration_date<\''.$oNextWeekDate->format('Y-m-d').'\'';
                        break;
                    }
                }
            }
            if (!empty($_POST['sortByUpdateDate'])) {
                $sQuery = $sQuery.' order by update_date asc';
            }
            else {
                $sQuery = $sQuery.' order by expiration_date asc';
            }
            $oQueryWithLogin = $oConnection->query($sQuery);
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
            $sQuery = 'select users.id,
                concat(users.surname, 
                    \' \',
                users.name,
                    \' \',
                users.patronymic_name) as name
                from tasks
                join users on tasks.responsible = users.id
                where tasks.responsible is not null 
                ';
            if (!empty($_POST['filterResponsible'])) {
                $sQuery = $sQuery.'and users.id='.$_POST['filterResponsible'];
            }
            $sQuery = $sQuery.' group by tasks.responsible';
            $oQueryWithLogin = $oConnection->query($sQuery);
            while ($aResultRow = $oQueryWithLogin->fetch_assoc())
            {
                $aResult[] = $aResultRow;
            }
            return $aResult;
        }
    }