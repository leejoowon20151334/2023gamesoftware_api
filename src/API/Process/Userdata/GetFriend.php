<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class GetFriend extends API
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
        $sql = "select a.*,b.name from Friend a inner join User b on a.friendid=b.id where a.userid = '{$this->input['id']}'";
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