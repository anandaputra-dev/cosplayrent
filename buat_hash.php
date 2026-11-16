<?php
$password = 'admin1234';

$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash;
?>