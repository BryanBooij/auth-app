<?php

namespace App\Services;

use Framework\Component\Container;
use Framework\Component\Service;
use mysqli;

//require 'config/google.php';
//require 'application.php';

class DatabaseService extends Service
{
    public $conn;

    /**
     * Service constructor.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $servername = config("google.servername");
        $Gusername = config("google.usernamelocalhost");
        $Gpassword = config("google.passwordlocalhost");
        $database = config("google.database");
        $this->conn = new mysqli($servername, $Gusername, $Gpassword, $database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

    }
}