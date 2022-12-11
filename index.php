<?php

session_start();

require_once('config.php');
require_once('functions.php');
require_once('classes/ActionClass.php');


$action = new ActionClass();

$templateVars = [];
if(!empty($_GET['action'])){
    if($_GET['action'] == 'import'){
        $action::importBooks();
    }
    elseif($_GET['action'] == 'search' && !empty($_GET['param'])){
        $searchResults = $action::searchForAuthors($_GET['param']);
        $templateVars['searchResults'] = $searchResults;
    }
}

loadTemplate('layout', $templateVars);
