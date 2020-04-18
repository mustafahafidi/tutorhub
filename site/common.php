<?php

require_once(__DIR__ . '/core/tutorhub/common.php');
require_once(__DIR__ . '/core/utils/common.php');

class Pages
{
    /**
     * Page is accessible by everyone.
     * File name: {page}.php
     */
    const PAGE_NORMAL = 0;
    /**
     * Page is accessible by not logged in users.
     * If the user is logged in, it will redirect to HOME_LOGGEDIN.
     * File name: {page}.php
     */
    const PAGE_NOT_LOGGEDIN = 1;
    /**
     * Page is accessible by logged in users.
     * If the user is not logged in, it will redirect to LOGIN.
     * File name: loggedin_{page}.php
     */
    const PAGE_LOGGEDIN = 2;
    /**
     * Page is accessible by logged in users.
     * If the user is not logged in, it will redirect to LOGIN.
     * Page depends on the user's type (S = Student, T = Tutor).
     * File name: loggedin_{type}_{page}.php
     */
    const PAGE_TYPED = 3;
    /**
     * Page is accessible by logged in students.
     * If the user is not logged in, it will redirect to LOGIN.
     * File name: loggedin_S_{page}.php
     */
    const PAGE_STUDENT = 4;
    /**
     * Page is accessible by logged in students.
     * If the user is not logged in, it will redirect to LOGIN.
     * File name: loggedin_T_{page}.php
     */
    const PAGE_TUTOR = 5;

    /**
     * Page => type mapping.
     */
    const PAGES = [
        'contact-us' => self::PAGE_NORMAL,
        'logout' => self::PAGE_NORMAL,
        'home' => self::PAGE_NOT_LOGGEDIN,
        'login' => self::PAGE_NOT_LOGGEDIN,
        'signup' => self::PAGE_NOT_LOGGEDIN,
        'appointments' => self::PAGE_LOGGEDIN,
        'dashboard' => self::PAGE_LOGGEDIN,
        'modify-account' => self::PAGE_LOGGEDIN,
        'modify-profile' => self::PAGE_LOGGEDIN,
        'view-profile' => self::PAGE_LOGGEDIN,
        'search' => self::PAGE_STUDENT,
        'search-results' => self::PAGE_STUDENT,
        'availability' => self::PAGE_TUTOR
    ];

    /**
     * Root of page files.
     */
    const PAGES_ROOT = __DIR__ . '/pages';

    /**
     * Name of home page.
     */
    const HOME = 'home';

    /**
     * Name of home page for logged users.
     */
    const HOME_LOGGEDIN = 'dashboard';

    /**
     * Name of login page.
     */
    const LOGIN = 'login';
}
