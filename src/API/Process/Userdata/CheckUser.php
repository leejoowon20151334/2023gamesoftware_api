<?php
namespace src\API\Process\Userdata;

use src\Core\API;
use src\System\DataSourceDB;

class CheckUser extends API
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
            'key'=>$_REQUEST['key'] ?? null,
            'name'=>$_REQUEST['name'] ?? null,
        ];
    }

    public function _Process()
    {
        $val = $this->Check($this->input['key']);
        if($val==null){
            if($this->input['key'] == null)
                $this->input['key'] = hash("sha256",$this->Count());
            if($this->input['name'] == null)
                $this->input['name'] = $this->RandomName();
            $this->Add($this->input['key'],$this->input['name']);
            $val = $this->Check($this->input['key']);
        }
        $this->data = $val;
    }

    public function _End()
    {

    }

    private function Check($key){
        $sql = "select * from User where `key` = '{$key}'";
        $q = $this->db->Exec($sql);
        if($q->num_rows<1) return null;
        $val = $q->fetch_assoc();
        return $val;
    }

    private function Add($key,$name){
        $sql = "insert into User (`key`,`name`) values('{$key}','{$name}')";
        $this->db->Exec($sql);
    }

    private function Count(){
        $sql = "select count(*) as num from User";
        $q = $this->db->Exec($sql);
        $val = $q->fetch_assoc();
        return $val['num'];
    }

    private function RandomName(){
        $names = ['고양이', '강아지', '거북이', '토끼', '뱀', '사자', '호랑이', '표범', '치타', '하이에나', '기린', '코끼리', '코뿔소', '하마', '악어', '펭귄', '부엉이', '올빼미', '곰', '돼지', '소', '닭', '독수리', '타조', '고릴라', '오랑우탄', '침팬지', '원숭이', '코알라', '캥거루', '고래', '상어', '칠면조', '직박구리', '쥐', '청설모', '메추라기', '앵무새', '삵', '스라소니', '판다', '오소리', '오리', '거위', '백조', '두루미', '고슴도치', '두더지', '우파루파', '맹꽁이', '너구리', '개구리', '두꺼비', '카멜레온', '이구아나', '노루', '제비', '까치', '고라니', '수달', '당나귀', '순록', '염소', '공작', '바다표범', '들소', '박쥐', '참새', '물개', '바다사자', '살모사', '구렁이', '얼룩말', '산양', '멧돼지', '카피바라', '도롱뇽', '북극곰', '퓨마', '', '미어캣', '코요테', '라마', '딱따구리', '기러기', '비둘기', '스컹크', '돌고래', '까마귀', '매', '낙타', '여우', '사슴', '늑대', '재규어', '알파카', '양', '다람쥐', '담비'];
        return $names[rand(0,count($names))];
    }
}