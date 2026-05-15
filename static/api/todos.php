<?php

error_reporting(E_ALL & ~E_DEPRECATED);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../library/rb-sqlite.php';
require_once __DIR__ . '/../library/msgpack.php';

R::setup('sqlite:' . __DIR__ . '/todos.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $todos = MsgPack::decode($input);
    
    if (is_array($todos)) {
        R::wipe('todo');
        foreach ($todos as $t) {
            $todo = R::dispense('todo');
            $todo->text = $t['text'];
            $todo->done = (bool)$t['done'];
            R::store($todo);
        }
        echo "OK";
    } else {
        http_response_code(400);
        echo "Invalid data";
    }
} else {
    $todos = R::findAll('todo');
    $res = [];
    foreach ($todos as $t) {
        $res[] = [
            'id' => (int)$t->id,
            'text' => $t->text,
            'done' => (bool)$t->done
        ];
    }
    header('Content-Type: application/x-msgpack');
    echo MsgPack::encode($res);
}
