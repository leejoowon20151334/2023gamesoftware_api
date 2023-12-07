<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class GetRank extends API
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
        ];
    }

    public function _Process()
    {
        $sql = "select a.*,b.name from Score a inner join User b on a.userid=b.id order by score desc limit 10";
        $q = $this->db->Exec($sql);
        while($val = $q->fetch_assoc()){
            $this->data[] = $val;
        }
    }

    public function _End()
    {

    }
}