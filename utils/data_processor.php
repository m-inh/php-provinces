<?php

function retrieveRequestData() {
    $data = file_get_contents('php://input');
    return json_decode($data, true);
};

?>