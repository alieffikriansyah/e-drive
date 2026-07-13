<?php
$mysqli = new mysqli("localhost", "root", "", "pos_warung");
$res = $mysqli->query("SHOW CREATE TABLE cabang");
print_r($res->fetch_assoc());
