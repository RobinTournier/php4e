<?php
// initialisation de la session
session_start();


define('RACINE_WEB', '/php/boutique/');
define('PHOTO_WEB', RACINE_WEB . 'photo/');

define('PHOTO_DIR', $_SERVER['DOCUMENT_ROOT'] . '/php/boutique/photo/');
define('PHOTO_DEFAULT', 'https://dummyimage.com/600x400/000/ffffff&text=Pas+d\'image');

require __DIR__ . '/cnx.php';
require __DIR__ . '/fonctions.php';

