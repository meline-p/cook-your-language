<?php $title = "Bienvenue !" ?>

<?php ob_start(); ?>


<?php $content = ob_get_clean(); ?>

<?php require_once(__DIR__ . '/../templates/parts/layout.php'); ?>