<?php
require_once 'functions/user.php';
session_start();
if (isset($_SESSION['user_name'])&& checkIfUserExists($_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}
?>