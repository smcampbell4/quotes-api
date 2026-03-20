<?php

require_once '../config/Database.php';
require_once '../config/headers.php';
require_once '../../models/Author.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->connect();

$author = new Author($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (isset($_GET['id'])) {
        $author->id = $_GET['id'];
        $result = $author->read_single();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $author_arr = array(
                'id' => $row['id'],
                'author' => $row['author']
            );

            echo json_encode($author_arr);
        } else {
            echo json_encode(array('message' => 'author_id Not Found'));
        }

    } else {
        $result = $author->read();

        if ($result->rowCount() > 0) {
            $authors_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $author_item = array(
                    'id' => $row['id'],
                    'author' => $row['author']
                );

                array_push($authors_arr, $author_item);
            }

            echo json_encode($authors_arr);
        } else {
            echo json_encode(array('message' => 'author_id Not Found'));
        }
    }

} elseif ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->author)) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $author->author = $data->author;

    if ($author->create()) {
        echo json_encode(array(
            'author' => $author->author
        ));
    } else {
        echo json_encode(array('message' => 'author Not Created'));
    }

} elseif ($method === 'PUT') {

    $data = json_decode(file_get_contents("php://input"));

    if (
        empty($data->id) ||
        empty($data->author)
    ) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $author->id = $data->id;
    $author->author = $data->author;

    if (!$author->authorExists()) {
        echo json_encode(array('message' => 'author_id Not Found'));
        exit();
    }

    if ($author->update()) {
        echo json_encode(array(
            'id' => $author->id,
            'author' => $author->author
        ));
    } else {
        echo json_encode(array('message' => 'author Not Updated'));
    }

} elseif ($method === 'DELETE') {

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id)) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $author->id = $data->id;

    if (!$author->authorExists()) {
        echo json_encode(array('message' => 'author_id Not Found'));
        exit();
    }

    if ($author->delete()) {
        echo json_encode(array(
            'id' => $author->id
        ));
    } else {
        echo json_encode(array('message' => 'author Not Deleted'));
    }

} else {
    echo json_encode(array('message' => 'Method Not Allowed'));
}

?>