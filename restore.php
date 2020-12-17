<?php
require __DIR__ . '/vendor/autoload.php';

$uri = '<enter_user_server_uri>'; // e.g https://cloud.damken.com
$username = '<enter_your_username>';
$password = '<enter_your_password>';
$restoreDate = '2020-12-08';

$script = new RestoreTrash($uri, $username, $password, $restoreDate);
$script->run();