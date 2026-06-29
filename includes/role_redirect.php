<?php

switch($_SESSION['role'])
{
    case 0:
        header("Location: student/dashboard.php");
        break;

    case 1:
        header("Location: supervisor/dashboard.php");
        break;

    case 2:
        header("Location: organization/dashboard.php");
        break;

    case 3:
        header("Location: registrar/dashboard.php");
        break;
}

exit();