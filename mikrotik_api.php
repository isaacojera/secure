<?php

class RouterosAPI {

    private $socket;
    private $connected = false;

    /* ================= CONNECT ================= */

    public function connect($ip, $user, $pass, $port = 8728, $timeout = 5) {

        $this->socket = fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (!$this->socket) {
            return false;
        }

        stream_set_timeout($this->socket, $timeout);

        // Login for RouterOS >= 6.43
        $this->writeSentence(['/login', "=name=$user", "=password=$pass"]);
        $response = $this->readSentence();

        if (isset($response[0]) && $response[0] == '!done') {
            $this->connected = true;
            return true;
        }

        return false;
    }

    /* ================= WRITE ================= */

    private function writeSentence(array $words) {
        foreach ($words as $word) {
            $this->writeWord($word);
        }
        $this->writeWord(''); // end sentence
    }

    private function writeWord($word) {
        $len = strlen($word);
        fwrite($this->socket, $this->encodeLength($len) . $word);
    }

    private function encodeLength($len) {
        if ($len < 0x80) {
            return chr($len);
        } elseif ($len < 0x4000) {
            return chr(($len >> 8) | 0x80) . chr($len & 0xFF);
        } elseif ($len < 0x200000) {
            return chr(($len >> 16) | 0xC0) .
                   chr(($len >> 8) & 0xFF) .
                   chr($len & 0xFF);
        } elseif ($len < 0x10000000) {
            return chr(($len >> 24) | 0xE0) .
                   chr(($len >> 16) & 0xFF) .
                   chr(($len >> 8) & 0xFF) .
                   chr($len & 0xFF);
        } else {
            return chr(0xF0) . pack('N', $len);
        }
    }

    /* ================= READ ================= */

    private function readSentence() {
        $response = [];
        while (true) {
            $word = $this->readWord();
            if ($word == '') break;
            $response[] = $word;
        }
        return $response;
    }

    private function readWord() {
        $len = ord(fread($this->socket, 1));
        if ($len & 0x80) {
            $len &= ~0x80;
            $len = ($len << 8) + ord(fread($this->socket, 1));
        }
        return fread($this->socket, $len);
    }

    /* ================= COMMAND ================= */

    public function comm($command, $params = []) {

        $sentence = [$command];

        foreach ($params as $key => $value) {
            $sentence[] = "=$key=$value";
        }

        $this->writeSentence($sentence);

        $responses = [];

        while (true) {
            $res = $this->readSentence();

            if (in_array('!done', $res)) {
                break;
            }

            if (in_array('!re', $res)) {
                $entry = [];
                foreach ($res as $word) {
                    if (strpos($word, '=') === 0) {
                        $parts = explode('=', $word, 3);
                        $entry[$parts[1]] = $parts[2] ?? '';
                    }
                }
                $responses[] = $entry;
            }
        }

        return $responses;
    }

    /* ================= DISCONNECT ================= */

    public function disconnect() {
        if ($this->socket) {
            fclose($this->socket);
        }
        $this->connected = false;
    }
}
