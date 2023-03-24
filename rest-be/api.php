<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('config.php');
$db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        read_games($db);
        break;
    case 'POST':
        create_games($db, $_POST);
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        update_games($db, $_GET['id'], $_PUT);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        delete_games($db, $id);
        break;
}

/**
 * READ from Games
 * @param $db
 * @return void
 */
function read_games($db)
{
    $stmt = $db->query('SELECT * from games;');
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($games);
}

/**
 * CREATE method for olympic games
 * @param $db
 * @param $data
 * @return void
 */
function create_games($db, $data) {
    $stmt = $db->prepare('INSERT INTO games (type, year, games_order, city, country) VALUES (:type, :year, :games_order, :city, :country);');
    $stmt->bindParam(':type', $data['type']);
    $stmt->bindParam(':year', $data['year']);
    $stmt->bindParam(':games_order', $data['games_order']);
    $stmt->bindParam(':city', $data['city']);
    $stmt->bindParam(':country', $data['country']);
    $stmt->execute();
    echo json_encode(array('success' => 'Data created successfully'));
}

/**
 * UPDATE method for games
 * @param $db
 * @param $id
 * @param $data
 * @return void
 */
function update_games($db, $id, $data)
{
    $stmt = $db->prepare('UPDATE games SET type = :type, year = :year, games_order = :games_order, city = :city, country = :country WHERE id = :id;');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':type', $data['type']);
    $stmt->bindParam(':year', $data['year']);
    $stmt->bindParam(':games_order', $data['games_order']);
    $stmt->bindParam(':city', $data['city']);
    $stmt->bindParam(':country', $data['country']);
    $stmt->execute();
    echo json_encode(array('success' => 'Data updated successfully'));
}

/**
 * DELETE games by ID
 * @param $db
 * @param $id
 * @return void
 */
function delete_games($db, $id) {
    if(!isEmpty($id)) {
        echo json_encode(array('error' => 'Delete failed'));
        http_response_code(400);
        return;
    } else {
        $stmt = $db->prepare('DELETE FROM games WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo json_encode(array('success' => 'Data deleted successfully'));
    }
}

function isEmpty($param) {

    if(empty($param)) {
        $isOk = false;
    } else {
        $isOk = true;
    }

    return $isOk;
}
