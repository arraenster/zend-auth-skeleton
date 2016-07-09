<?php
/**
 *  List or roles and allowed resources
 */
return array(
    'guest'=> array(
        'login',
        'logout',
        'guest',
        'guest/open',
        'login/process'
    ),
    'admin'=> array(
        'home',
        'login',
        'logout',
        'login/process',
        'guest',
        'user'
    ),
);