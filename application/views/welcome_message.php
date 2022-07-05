<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Solventa SAS</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
</head>
<body>
<div id="container" class="__welcome_body">
    <div id="vue-app">
        <chats-main user_id="<?= $_SESSION['idoperador'] ?>"></chats-main>
    </div>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="__logo pt-5 pb-5 text-center">
                    <img src="/assets/images/backgrounds/solventa_logo.svg" alt="Solventa Colombia">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript" src="/assets/js/app.js"></script>
<!-- componente js y css area chat -->
<link href="../../public/chat_files/css/jquery.fullPage.css" type="text/css" rel="stylesheet">
<link href="../../public/chat_files/css/estilos.css" type="text/css" rel="stylesheet">
<link href="../../public/chat_files/css/customchat.css" type="text/css" rel="stylesheet">
<link href="../../public/chat_files/css/customchat-movil.css" type="text/css" rel="stylesheet">

<script type="text/javascript" src="../../public/chat_files/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../public/chat_files/js/modernizr.js"></script>
<script type="text/javascript" src="../../public/chat_files/js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="../../public/chat_files/js/jquery.fullPage.min.js"></script>
<script type="text/javascript" src="../../public/chat_files/js/customchat.js"></script>

<script>
    function iframeHeight(height) {
        $('#ifrm-contenedor').height(height + 100);
    }
</script>
<!-- fin componente js y css area chat -->
</body>
</html>