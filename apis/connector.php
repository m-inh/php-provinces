<?php

global $conn;
$conn = mysqli_connect(
    '127.0.0.1',
    'tungnguyen',
    'lemmein',
    'DanhMuc',
    3306
);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    mysqli_set_charset($conn,"utf8");
}

?>