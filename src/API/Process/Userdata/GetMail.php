<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class GetMail extends API
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
            'type'=>$_REQUEST['type'] ?? null,
        ];
    }

    public function _Process()
    {
        $sql = "select * from Mail where userid = '{$this->input['id']}' and check='0'";
        $q = $this->db->Exec($sql);
        $list = [];
        while ($val = $q->fetch_assoc()){
            $list[]=[
                'id'=>$val['friendid'],
                'name'=>$val['name'],
            ];
        }

        $this->data = $list;
    }

    public function _End()
    {

    }
}