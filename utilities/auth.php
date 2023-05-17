<?php

define("OPTIONS_HASH", array(
    'cost' => 12,
));

// https://www.php.net/manual/en/function.password-hash.php
function hashPassword($password){
    return password_hash($password, PASSWORD_BCRYPT, OPTIONS_HASH);
}

?>