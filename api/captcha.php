<?php
/**
 * File ini berisi endpoint API untuk membuat captcha
 */
require_once '../config/database.php';
require_once '../controllers/AuthController.php';

header('Content-type: image/png');
session_start();
$database = new Database();
$db = $database->getConnection();

$authController = new AuthController($db);
$authController->generateCaptcha();
?>