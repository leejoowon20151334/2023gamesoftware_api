<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class SetScore extends API
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
            'score'=>$_REQUEST['score'],
        ];
    }

    public function _Process()
    {
        $sql = "insert into Score (`userid`,`type`,`score`) values('{$this->input['id']}','','{$this->input['score']}') on duplicate key update `score`='{$this->input['score']}'";
        $this->db->Exec($sql);
    }

    public function _End()
    {

    }
}