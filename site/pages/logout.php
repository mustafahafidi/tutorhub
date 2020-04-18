<?php

use TutorHub\Session;

Session::invalidate();
header('Location: index.php');
die();
