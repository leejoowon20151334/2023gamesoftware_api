<?php
namespace src\Util;

class MySQL
{
    protected $db_host;
    protected $db_user;
    protected $db_password;
    protected $db_name;

    protected $connect;

    public function __construct($db_config)
    {
        $this->Init($db_config);
        $this->Connect();
    }

    public function __destruct()
    {
        mysqli_close($this->connect);
    }

    public function Init($db_config) {
        $this->db_host = $db_config['db_host'];
        $this->db_user = $db_config['db_user'];
        $this->db_password = $db_config['db_password'];
        $this->db_name = $db_config['db_name'];
    }

    public function Exec($sql) {
        $this->ReConnect();
        $rs = mysqli_query($this->connect, $sql);

        if ($rs) {
            return $rs;
        }

        die($this->connect->error);
    }

    public function InsertID() {
        $this->ReConnect();
        return mysqli_insert_id($this->connect);
    }

    public function Escape($string) {
        $this->ReConnect();
        return mysqli_escape_string($this->connect, $string);
    }

    private function Connect()
    {
        if ($this->connect === null) {
            $this->connect = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name) or die('DB ERROR: ' . mysqli_error());
            //$this->connect = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name) or die('DB ERROR: ' . mysqli_connect_error());
        }
    }

    private function ReConnect()
    {
        if (mysqli_ping($this->connect) === false) {
            $this->connect = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name) or die('DB ERROR: ' . mysqli_error());
            //$this->connect = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name) or die('DB ERROR: ' . mysqli_connect_error());
        }
    }
}