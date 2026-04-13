<?php
$nueva = 'admin123'; // cambia esto por la contraseña que quieras
echo password_hash($nueva, PASSWORD_DEFAULT);