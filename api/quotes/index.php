<?php

require_once '../config/Database.php';
require_once '../config/headers.php';
require_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();

$quote = new Quote($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (isset($_GET['id'])) {
        $quote->id = $_GET['id'];
        $result = $quote->read_single();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $quote_arr = array(
                'id' => $row['id'],
                'quote' => $row['quote'],
                'author' => $row['author'],
                'category' => $row['category']
            );

            echo json_encode($quote_arr);
        } else {
            echo json_encode(array('message' => 'No Quotes Found'));
        }

    } elseif (isset($_GET['author_id']) || isset($_GET['category_id'])) {
        $quote->author_id = isset($_GET['author_id']) ? $_GET['author_id'] : null;
        $quote->category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

        $result = $quote->read_filtered();

        if ($result->rowCount() > 0) {
            $quotes_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quote_item = array(
                    'id' => $row['id'],
                    'quote' => $row['quote'],
                    'author' => $row['author'],
                    'category' => $row['category']
                );

                array_push($quotes_arr, $quote_item);
            }

            echo json_encode($quotes_arr);
        } else {
            echo json_encode(array('message' => 'No Quotes Found'));
        }

    } else {
        $result = $quote->read();

        if ($result->rowCount() > 0) {
            $quotes_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quote_item = array(
                    'id' => $row['id'],
                    'quote' => $row['quote'],
                    'author' => $row['author'],
                    'category' => $row['category']
                );

                array_push($quotes_arr, $quote_item);
            }

            echo json_encode($quotes_arr);
        } else {
            echo json_encode(array('message' => 'No Quotes Found'));
        }
    }

} elseif ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    if (
        empty($data->quote) ||
        empty($data->author_id) ||
        empty($data->category_id)
    ) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    if (!$quote->authorExists()) {
        echo json_encode(array('message' => 'author_id Not Found'));
        exit();
    }

    if (!$quote->categoryExists()) {
        echo json_encode(array('message' => 'category_id Not Found'));
        exit();
    }

    if ($quote->create()) {
        echo json_encode(array(
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
        ));
    } else {
        echo json_encode(array('message' => 'Quote Not Created'));
    }

    } elseif ($method === 'PUT') {

    $data = json_decode(file_get_contents("php://input"));

    if (
        empty($data->id) ||
        empty($data->quote) ||
        empty($data->author_id) ||
        empty($data->category_id)
    ) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $quote->id = $data->id;
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    if (!$quote->quoteExists()) {
        echo json_encode(array('message' => 'No Quotes Found'));
        exit();
    }

    if (!$quote->authorExists()) {
        echo json_encode(array('message' => 'author_id Not Found'));
        exit();
    }

    if (!$quote->categoryExists()) {
        echo json_encode(array('message' => 'category_id Not Found'));
        exit();
    }

    if ($quote->update()) {
        echo json_encode(array(
            'id' => $quote->id,
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
        ));
    } else {
        echo json_encode(array('message' => 'Quote Not Updated'));
    }

    } elseif ($method === 'DELETE') {

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id)) {
        echo json_encode(array('message' => 'Missing Required Parameters'));
        exit();
    }

    $quote->id = $data->id;

    if (!$quote->quoteExists()) {
        echo json_encode(array('message' => 'No Quotes Found'));
        exit();
    }

    if ($quote->delete()) {
        echo json_encode(array('id' => $quote->id));
    } else {
        echo json_encode(array('message' => 'Quote Not Deleted'));
    }

} else {
    echo json_encode(array('message' => 'Method Not Allowed'));
}

?>