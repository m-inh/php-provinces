<?php

include '../utils/responder.php';
include '../utils/query_parser.php';
include '../utils/method_detector.php';
include '../utils/data_processor.php';
include 'connector.php';

$method = getQueryMethod();
$requestData = retrieveRequestData();
$id = getQuery("id");
switch ($method) {
    case 'GET':
        if (!$id) {
            getAllProvinces($conn);
        } else {
            getProvince($conn, $id);
        }
        break;
    case 'POST':
        addProvince($conn, $requestData);
        break;
    case 'PUT':
        updateProvince($conn, $id, $requestData);
        break;
    case 'DELETE':
        deleteProvince($conn, $id);
        break;
    default:
        break;
};

function getAllProvinces($conn) {
    $query = "SELECT DSTinh.id, DSTinh.name, COUNT(DSHuyen.tinhid) AS districts
        FROM
            DSTinh
                LEFT JOIN
            DSHuyen ON DSTinh.id = DSHuyen.tinhid
        GROUP BY DSTinh.id
        ORDER BY districts DESC;";
    $results = mysqli_query($conn, $query);
    $res = array();
    if (mysqli_num_rows($results) > 0) {
        while($s = mysqli_fetch_assoc($results)) {
            array_push($res, array(
                "id" => $s["id"],
                "name" => $s["name"],
                "districts" => $s["districts"],
            ));
        }
    }
    $res = wrapResponse(true, 200, "data", $res);
    http_response_code(200);
    echo json_encode($res);
};

function getProvince($conn, $id) {
    $query = "SELECT * FROM DSTinh WHERE id = '"."$id"."';";
    $results = mysqli_query($conn, $query);
    $res = array();
    if (mysqli_num_rows($results) > 0) {
        while($s = mysqli_fetch_assoc($results)) {
            $res =  array(
                "id" => $s["id"],
                "name" => $s["name"]
            );
        }
    }
    $res = wrapResponse(true, 200, "data", $res);
    http_response_code(200);
    echo json_encode($res);
};

function addProvince($conn, $newData) {
    $_id = $newData["id"];
    $_name = $newData["name"];
    $query = 
        "INSERT INTO DSTinh (`id`, `name`) 
        VALUES ('"."$_id"."', '"."$_name"."');";
    $success = mysqli_query($conn, $query);
    $res = array();
    if ($success) {
        $res =  array(
            "id" => $_id,
            "name" => $_name,
            "districts" => 0,
        );
        $res = wrapResponse(true, 200, "data", $res);
        http_response_code(200);
    } else {
        $res = wrapResponse(false, 400, "reason", "Failed to add new data");
        http_response_code(400);
    }
    echo json_encode($res);
};

function updateProvince($conn, $id, $newData) {
    $_id = $newData["id"];
    $_name = $newData["name"];
    $query = 
        "UPDATE DSTinh
        SET id='"."$_id"."', 
            name='"."$_name"."' 
        WHERE `id`='"."$id"."';";
    $success = mysqli_query($conn, $query);
    $affected_rows = mysqli_affected_rows($conn);
    $res = array();
    if ($success && $affected_rows === 1) {
        $query = "SELECT COUNT(*) AS districts FROM DSHuyen WHERE tinhid = '"."$_id"."';";
        $results = mysqli_query($conn, $query);
        $s = mysqli_fetch_assoc($results);
        $res =  array(
            "id" => $_id,
            "name" => $_name,
            "districts" => $s["districts"],
        );
        $res = wrapResponse(true, 200, "data", $res);
        http_response_code(200);
    } else {
        $res = wrapResponse(false, 400, "reason", "Failed to update");
        http_response_code(400);
    }
    echo json_encode($res);
};

function deleteProvince($conn, $id) {
    $query = "DELETE FROM DSTinh WHERE id='"."$id"."';";
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