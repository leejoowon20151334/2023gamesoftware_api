<?php

namespace src\Core;

use src\Util\Logger;

class API
{
    public $data = [];
    public $options = [];
    protected $result = [];

    public function Call()
    {
        $this->CallExec();
        $this->Success();
    }

    public function Exec(array $param = [])
    {
        $req = $this->SetRequest($param);

        $this->_Init();
        $this->_Process();
        $this->_End();

        $this->Result();
        $this->UnsetRequest($req);
        return $this->result;
    }

    public function CallExec()
    {
        $this->_Init();
        $this->_Process();
        $this->_End();
    }

    public function _Init()
    {
    }

    public function _Process()
    {
    }

    public function _End()
    {
    }

    public function AddOption($params, $is_required, $type, $etc = null)
    {
        $option = [];

        $option['params'] = $params;
        $option['is_required'] = $is_required;
        $option['type'] = $type;

        if ($etc !== null) {
            $option['option'] = $etc;
        }

        $this->options[] = $option;
    }

    public function Error403($msg = 'Forbidden')
    {
        $this->Fail(403, $msg);
    }

    public function Error404($msg = 'API Not Found')
    {
        $this->Fail(404, $msg);
    }

    public function Error($msg = 'Error', $code = 999)
    {
        $this->Fail($code, $msg);
    }

    /**
     * Get request parameter.
     * If not set request parameter, default parameter data is set.
     *
     * @param array $request
     * @param string $key
     * @param array $options keys [default, type]
     *
     * @return float|int|string
     */
    public function getParam(array $request, string $key, array $options = [])
    {
        if (!isset($request[$key]) && !isset($options['default'])) {
            $this->Error("There is no parameter: {$key}");
        }

        $param = $request[$key] ?? $options['default'];

        if (isset($options['type']) && !static::checkType($param, $options['type'])) {
            $this->Error("Wrong parameter type: {$key}");
        }

        return $param;
    }

    /**
     * Use $type to check the type of $param data.
     *
     * @param        $param
     * @param string $type
     *
     * @return bool
     */
    public static function checkType($param, string $type): bool
    {
        if (($type === 'string') && !is_string($param)) {
            return false;
        }

        if (($type === 'numeric') && !is_numeric($param)) {
            return false;
        }

        return true;
    }

    protected function Success()
    {
        $this->Result();
        $this->View();
    }

    protected function Result()
    {
        $this->result['status'] = 'success';
        $this->result['data'] = $this->data;

        if ($this->options !== [] && is_array($this->options)) {
            $this->result['options'] = $this->options;
        }
    }

    protected function Fail($code, $msg = '')
    {
        $this->result['status'] = 'fail';
        $this->result['code'] = $code;
        $this->result['msg'] = $msg;

        $this->View();
    }

    protected function View()
    {
        try {
            header('Content-Type: application/json; charset=utf-8;');
        } catch (\Exception $e) {
            echo $e . PHP_EOL . PHP_EOL;
        }
        echo json_encode($this->result);
        exit();
    }

    protected function SetRequest(array $arr = [])
    {
        $req = $_REQUEST;
        unset($_REQUEST);
        foreach ((array)$arr as $key => $val)
            $_REQUEST[$key] = $val;
        return $req;
    }

    protected function UnsetRequest(array $req = [])
    {
        unset($_REQUEST);
        $_REQUEST = $req;
    }
}
