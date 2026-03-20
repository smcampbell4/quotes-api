<?php

require_once '../config/Database.php';
require_once '../config/headers.php';
require_once '../../models/Category.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->connect();

$category = new Category($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (isset($_GET['id'])) {
        $category->id = $_GET['id'];
        $result = $category->read_single();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $category_arr = array(
                'id' => $row['id'],
                'category' => $row['category']
            );

            echo json_encode($category_arr);
        } else {
            echo json_encode(array('message' => 'category_id Not Found'));
        }

    } else {
        $result = $category->read();

        if ($result->rowCount() > 0) {
            $categories_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $category_item = array(
                    'id' => $row['id'],
                    'category' => $row['category']
                );

                array_push($categories_arr, $category_item);
            }

            echo json_encode($categories_arr);
        } else {
            echo json_encode(array('message' => 'category_id Not Found'));
        }
    }

} elseif ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->category)) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $category->category = $data->category;

    if ($category->create()) {
        echo json_encode(array(
            'category' => $category->category
        ));
    } else {
        echo json_encode(array('message' => 'category Not Created'));
    }

} elseif ($method === 'PUT') {

    $data = json_decode(file_get_contents("php://input"));

    if (
        empty($data->id) ||
        empty($data->category)
    ) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $category->id = $data->id;
    $category->category = $data->category;

    if (!$category->categoryExists()) {
        echo json_encode(array('message' => 'category_id Not Found'));
        exit();
    }

    if ($category->update()) {
        echo json_encode(array(
            'id' => $category->id,
            'category' => $category->category
        ));
    } else {
        echo json_encode(array('message' => 'category Not Updated'));
    }

} elseif ($method === 'DELETE') {

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id)) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $category->id = $data->id;

    if (!$category->categoryExists()) {
        echo json_encode(array('message' => 'category_id Not Found'));
        exit();
    }

    if ($category->delete()) {
        echo json_encode(array(
            'id' => $category->id
        ));
    } else {
        echo json_encode(array('message' => 'category Not Deleted'));
    }

} else {
    echo json_encode(array('message' => 'Method Not Allowed'));
}

?>