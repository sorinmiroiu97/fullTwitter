<?php

session_start();

// TODO: validate session id
if(!isset($_SESSION['userId'])) { sendError(400, 'session user id not set', __LINE__); };
if(strlen($_SESSION['userId']) != 19) { sendError(400, 'session user id invalid', __LINE__); };

require_once(__DIR__.'/../database/arangodb.php');
use ArangoDBClient\Statement as ArangoStatement;

try {

    $statementGetUserConversations = new ArangoStatement(
        $db, 
        [
            'query' => 'RETURN DOCUMENT(@userId)',
            'bindVars' => [
                'userId' => $_SESSION["userId"]
            ]
        ]
    );

    $cursorStatementGetUserConversations = $statementGetUserConversations->execute();
    $dataUserConversations = $cursorStatementGetUserConversations->getAll();

    http_response_code(200);
    header('Content-type: application/json');
    echo json_encode($dataUserConversations[0]->chatWith);
    exit();

} catch (Exception $ex) {
    sendError(500, 'system under maintainance', $ex);
}


function sendError($iResponseCode, $sMessage, $iLine){
    http_response_code($iResponseCode);
    header('Content-Type: application/json');
    echo '{"message":"'.$sMessage.'", "error":'.$iLine.'}';
    exit();
}