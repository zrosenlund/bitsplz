<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);

    //Require the autoload file
    require_once('vendor/autoload.php');

    //Require the database credential
    //require_once '/home/mdenggre/db-config.php';

    //Create an instance of the Base class
    $f3 = Base::instance();

    //Set debug level
    $f3->set('DEBUG', 3); //3 is higher than 0, will present more info

    //Define a default route
    $f3->route('GET|POST /', function($f3, $params) {

        //echo Template::instance() -> render('views/whatever.html');

    });

    //Run fat free
    $f3->run();