<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class SaveData extends API
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
            'data'=>$_REQUEST['data'] ?? null,
        ];
    }

    public function _Process()
    {
        $sql = "insert into SaveData ('userid','data') values('{$this->input['id']}','{$this->input['data']}')";
        $this->db->Exec($sql);
    }

    public function _End()
    {

    }
}