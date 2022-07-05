<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sqlexceptions {

    public function checkForError() {
        get_instance()->load->database();
        $error = get_instance()->db->error();
        if ($error['code'])
            throw new MySQLException($error);
    }
}

abstract class UserException extends Exception {
    public abstract function getUserMessage();
}

class MySQLException extends UserException {
    private $errorNumber;
    private $errorMessage;

    public function __construct(array $error) {
        $this->errorNumber = "Error Code(" . $error['code'] . ")";
        $this->errorMessage = $error['message'];
    }

    public function getUserMessage() {
        return array(
            "error" => array (
                "code" => $this->errorNumber,
                "message" => $this->errorMessage
            )
        );
    }

}
