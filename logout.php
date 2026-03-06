<?php
session_start();
session_destroy();
header('Location: /male-fashion-store/index.php');
exit;
?>