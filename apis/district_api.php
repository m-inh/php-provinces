<?php

include '../utils/responder.php';
include '../utils/query_parser.php';
include '../utils/method_detector.php';
include '../utils/data_processor.php';
include 'connector.php';

$method = getQueryMethod();
$requestData = retrieveRequestData();
$id = getQuery("id");
$province_id = getQuery("province_id");

switch ($method) {
    case 'GET':
        if ($province_id) {
            getAllDistricts($conn, $province_id);
        } else if ($id) {
            getDistrict($conn, $id);
        } else {
            $res = wrapResponse(true, 200, "data", array());
            http_response_code(200);
            echo json_encode($res);
        }
        break;
    case 'POST':
        addDistrict($conn, $province_id, $requestData);
        break;
    case 'PUT':
        updateDistrict($conn, $id, $requestData);
        break;
    case 'DELETE':
        deleteDistrict($conn, $id);
        break;
    default:
        break;
};

function getAllDistricts($conn, $province_id) {
    $query = "SELECT * FROM DSHuyen WHERE DSHuyen.tinhid = '"."$province_id"."';";
    $results = mysqli_query($conn, $query);
    $res = array();
    if (mysqli_num_rows($results) > 0) {
        while($s = mysqli_fetch_assoc($results)) {
            array_push($res, array(
                "id" => $s["id"],
                "name" => $s["name"],
                "tinhid" => $s["tinhid"],
            ));
        }
    }
    $res = wrapResponse(true, 200, "data", $res);
    http_response_code(200);
    echo json_encode($res);
};

function getDistrict($conn, $id) {
    $query = "SELECT * FROM DSHuyen WHERE id = '"."$id"."';";
    $results = mysqli_query($conn, $query);
    $res = array();
    if (mysqli_num_rows($results) > 0) {
        $s = mysqli_fetch_assoc($results);
        $res =  array(
            "id" => $s["id"],
            "name" => $s["name"],
            "tinhid" => $s["tinhid"],
        );
    }
    $res = wrapResponse(true, 200, "data", $res);
    http_response_code(200);
    echo json_encode($res);
};

function addDistrict($conn, $province_id, $newData) {
    $_id = $newData["id"];
    $_name = $newData["name"];
    $query = 
        "INSERT INTO DSHuyen (`id`, `name`, `tinhid`) 
        VALUES ('"."$_id"."', '"."$_name"."', '"."$province_id"."');";
    $success = mysqli_query($conn, $query);
    $res = array();
    if ($success) {
        $res =  array(
            "id" => $_id,
            "name" => $_name,
            "tinhid" => $province_id,
        );
        $res = wrapResponse(true, 200, "data", $res);
        http_response_code(200);
    } else {
        $res = wrapResponse(false, 400, "reason", "Failed to add new data");
        http_response_code(400);
    }
    echo json_encode($res);
};

function updateDistrict($conn, $id, $newData) {
    $_id = $newData["id"];
    $_name = $newData["name"];
    $query = 
        "UPDATE DSHuyen
        SET id='"."$_id"."', 
            name='"."$_name"."' 
        WHERE `id`='"."$id"."';";
    $success = mysqli_query($conn, $query);
    $affected_rows = mysqli_affected_rows($conn);
    $res = array();
    if ($success && $affected_rows === 1) {
        $query = "SELECT * FROM DSHuyen WHERE id = '"."$_id"."';";
        $results = mysqli_query($conn, $query);
        $s = mysqli_fetch_assoc($results);
        $res =  array(
            "id" => $s["id"],
            "name" => $s["name"],
            "tinhid" => $s["tinhid"],
        );
        $res = wrapResponse(true, 200, "data", $res);
        http_response_code(200);
    } else {
        $res = wrapResponse(false, 400, "reason", "Failed to update");
        http_response_code(400);
    }
    echo json_encode($res);
};

function deleteDistrict($conn, $id) {
    $query = "DELETE FROM DSHuyen WHERE id='"."$id"."';";
    $success = mysqli_query($conn, $query);
    $affected_rows = mysqli_affected_rows($conn);
    if ($success && $affected_rows === 1) {
        $res = wrapResponse(true, 200, "data", array());
        http_response_code(200);
    } else {
        $res = wrapResponse(false, 400, "reason", "Failed to delete");
        http_response_code(400);
    }
    echo json_encode($res);
};

?>