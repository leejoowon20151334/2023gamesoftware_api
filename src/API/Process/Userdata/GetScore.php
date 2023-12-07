<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class GetScore extends API
{
    private $db;
    private $input;

    public function __construct()
    {
        $this->db = DataSourceDB::GetInstance();
    }

    public function _Init()
    {
        $this->input = [
            'id'=>$_REQUEST['id'],
        ];
    }

    public function _Process()
    {
        $sql = "select * from Score where userid = '{$this->input['id']}'";
        $q = $this->db->Exec($sql);
        $val = $q->fetch_assoc();

        $this->data = $val;
    }

    public function _End()
    {

    }
}