<?php

require(__DIR__ . '/common.php');

use TutorHub\Session;
use TutorHub\User;

Session::start();

function resolve_page($page)
{
    if (!isset(Pages::PAGES[$page]))
        return resolve_page(Session::isLogged() ? Pages::HOME_LOGGEDIN : Pages::HOME);

    switch (Pages::PAGES[$page]) {
        case Pages::PAGE_NORMAL:
            $qualifiedPage = $page;
            break;
        case Pages::PAGE_NOT_LOGGEDIN:
            if (Session::isLogged())
                return resolve_page(Pages::HOME_LOGGEDIN);
            $qualifiedPage = $page;
            break;
        case Pages::PAGE_LOGGEDIN:
            if (!Session::isLogged())
                return resolve_page(Pages::LOGIN);
            $qualifiedPage = 'loggedin_' . $page;
            break;
        case Pages::PAGE_TYPED:
            if (!Session::isLogged())
                return resolve_page(Pages::LOGIN);
            $user = Session::getUser();
            switch ($user['type']) {
                case User::TYPE_STUDENT:
                    $qualifiedPage = 'loggedin_S_' . $page;
                    break;
                case User::TYPE_TUTOR:
                    $qualifiedPage = 'loggedin_T_' . $page;
                    break;
            }
            break;
        case Pages::PAGE_STUDENT:
            $user = Session::getUser();
            if ($user === null || $user['type'] != User::TYPE_STUDENT)
                return resolve_page(Pages::LOGIN);
            $qualifiedPage = 'loggedin_S_' . $page;
            break;
        case Pages::PAGE_TUTOR:
            $user = Session::getUser();
            if ($user === null || $user['type'] != User::TYPE_TUTOR)
                return resolve_page(Pages::LOGIN);
            $qualifiedPage = 'loggedin_T_' . $page;
            break;
    }

    return [$page, $qualifiedPage];
}

$resolve = resolve_page($_GET['page'] ?? '');
$page = $resolve[0];
$qualifiedPage = $resolve[1];

include(Pages::PAGES_ROOT . '/' . $qualifiedPage . '.php');

