<?php
/**
 *  List or roles and allowed resources
 */
return array(
    'guest'=> array(
        'login',
        'logout',
        'login/process'
    ),
    'admin'=> array(
        'home',
        'login',
        'logout',
        'login/process',
        'user'
    ),
);