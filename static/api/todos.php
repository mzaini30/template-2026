<?php

error_reporting(E_ALL & ~E_DEPRECATED);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../library/rb-sqlite.php';

R::setup('sqlite:' . __DIR__ . '/todos.db');

/**
 * Pure PHP MessagePack implementation
 */
class MsgPack {
    public static function encode($data) {
        if (is_array($data)) {
            if (array_keys($data) === range(0, count($data) - 1)) {
                $count = count($data);
                if ($count < 16) $res = chr(0x90 | $count);
                else if ($count < 65536) $res = chr(0xdc) . pack('n', $count);
                else $res = chr(0xdd) . pack('N', $count);
                foreach ($data as $v) $res .= self::encode($v);
                return $res;
            } else {
                $count = count($data);
                if ($count < 16) $res = chr(0x80 | $count);
                else if ($count < 65536) $res = chr(0xde) . pack('n', $count);
                else $res = chr(0xdf) . pack('N', $count);
                foreach ($data as $k => $v) {
                    $res .= self::encode((string)$k);
                    $res .= self::encode($v);
                }
                return $res;
            }
        } else if (is_string($data)) {
            $len = strlen($data);
            if ($len < 32) return chr(0xa0 | $len) . $data;
            else if ($len < 256) return chr(0xd9) . chr($len) . $data;
            else if ($len < 65536) return chr(0xda) . pack('n', $len) . $data;
            else return chr(0xdb) . pack('N', $len) . $data;
        } else if (is_int($data)) {
            if ($data >= 0 && $data <= 127) return chr($data);
            if ($data >= -32 && $data < 0) return chr(0xe0 | ($data + 32));
            if ($data > 0 && $data < 256) return chr(0xcc) . chr($data);
            if ($data > 0 && $data < 65536) return chr(0xcd) . pack('n', $data);
            if ($data > 0 && $data < 4294967296) return chr(0xce) . pack('N', $data);
            return chr(0xd2) . pack('N', $data);
        } else if (is_bool($data)) {
            return $data ? chr(0xc3) : chr(0xc2);
        } else if (is_null($data)) {
            return chr(0xc0);
        }
        return '';
    }

    private $data, $offset = 0;
    public static function decode($data) {
        return (new self($data))->read();
    }
    private function __construct($data) { $this->data = $data; }
    private function read() {
        if ($this->offset >= strlen($this->data)) return null;
        $b = ord($this->data[$this->offset++]);
        if ($b <= 0x7f) return $b;
        if ($b >= 0xe0) return $b - 256;
        if ($b >= 0x80 && $b <= 0x8f) return $this->map($b & 0x0f);
        if ($b >= 0x90 && $b <= 0x9f) return $this->arr($b & 0x0f);
        if ($b >= 0xa0 && $b <= 0xbf) return $this->raw($b & 0x1f);
        switch ($b) {
            case 0xc0: return null;
            case 0xc2: return false;
            case 0xc3: return true;
            case 0xcc: return ord($this->raw(1));
            case 0xcd: return unpack('n', $this->raw(2))[1];
            case 0xce: return unpack('N', $this->raw(4))[1];
            case 0xd0: $v = ord($this->raw(1)); return $v > 127 ? $v - 256 : $v;
            case 0xd1: $v = unpack('n', $this->raw(2))[1]; return $v > 32767 ? $v - 65536 : $v;
            case 0xd2: $v = unpack('N', $this->raw(4))[1]; return $v > 2147483647 ? $v - 4294967296 : $v;
            case 0xd9: return $this->raw(ord($this->raw(1)));
            case 0xda: return $this->raw(unpack('n', $this->raw(2))[1]);
            case 0xdb: return $this->raw(unpack('N', $this->raw(4))[1]);
            case 0xdc: return $this->arr(unpack('n', $this->raw(2))[1]);
            case 0xdd: return $this->arr(unpack('N', $this->raw(4))[1]);
            case 0xde: return $this->map(unpack('n', $this->raw(2))[1]);
            case 0xdf: return $this->map(unpack('N', $this->raw(4))[1]);
        }
        return null;
    }
    private function raw($n) { $res = substr($this->data, $this->offset, $n); $this->offset += $n; return $res; }
    private function arr($n) { $res = []; while ($n--) $res[] = $this->read(); return $res; }
    private function map($n) { $res = []; while ($n--) { $k = $this->read(); $res[$k] = $this->read(); } return $res; }
}

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
