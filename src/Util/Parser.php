<?php

namespace src\Util;

class Parser
{
    public static function ObjectToArray($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map('self::' . __FUNCTION__, $d);
        }

        return $d;
    }

    public static function ArrayToObject($d)
    {
        if (is_array($d)) {
            return (object) array_map('self::' . __FUNCTION__, $d);
        }

        return $d;
    }

    public static function ArrayToUISQL(string $table_name, array $data) {
        $ordered_key = array_keys($data[0]);

        $t_keys = implode('`,`', $ordered_key);
        $keys = "(`{$t_keys}`)";

        $t_values = [];

        foreach ($data as $row) {
            $t_value = [];

            foreach ($ordered_key as $k) {
                if (is_array($row[$k])) {
                    Logger::Log($row[$k], true);
                }
                $t_value[] = "'{$row[$k]}'";
            }

            $t_value = implode(',', $t_value);
            $t_values[] = "({$t_value})";
        }

        $values = implode(',', $t_values);

        $t_unique = [];

        foreach ($ordered_key as $u) {
            $t_unique[] = "`{$u}` = VALUES(`{$u}`)";
        }

        $unique = implode(',', $t_unique);

        $sql = "INSERT INTO {$table_name} {$keys} VALUES {$values} ON DUPLICATE KEY UPDATE {$unique}; ";

        return $sql;
    }


    public static function XmltoJson($xml)
    {
        $result = [];
        Parser::normalizeSimpleXML(simplexml_load_string($xml), $result);
        return json_encode($result);
    }

    public static function XmltoArray($xml) {
        $result = [];
        Parser::normalizeSimpleXML(simplexml_load_string($xml), $result);
        return $result;
    }

    public static function normalizeSimpleXML($obj, &$result) {
        $data = $obj;
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $res = null;
                Parser::normalizeSimpleXML($value, $res);
                if (($key == '@attributes') && ($key)) {
                    $result = $res;
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $data;
        }
    }

    public static function GetValueByAttr($attr, $str) {
        return Parser::GetAttrValue($attr, $str);
    }

    public static function GetValueByTag($str) {
        return Parser::GetValue($str);
    }

    public static function GetAttrValue($attr, $str) {
        $reg = "/^<[^<>]*?{$attr} *= *\\\\*\"([^<>\\\\\"]*)\\\\*\"[^<>]*?>/";
        $rs = Parser::GetValueByReg($reg, $str, 1);
        if (!empty($rs)) return $rs;

        $reg = "/^<[^<>]*?{$attr} *= *\\\\*'([^<>\\\\']*)\\\\*'[^<>]*?>/";
        $rs = Parser::GetValueByReg($reg, $str, 1);
        if (!empty($rs)) return $rs;

        $reg = "/^<[^<>]*?{$attr} *=([^<> ]*)[^<>]*?>/";
        $rs = Parser::GetValueByReg($reg, $str, 1);
        return $rs;
    }

    public static function GetValue($str) {
        $reg = "/<[^<>]*?>([\S\s]*)< *\/[^<>]*?>/";
        $rs = Parser::GetValueByReg($reg, $str, 1);
        return $rs;
    }

    public static function GetValueByReg($reg, $target, $num) {
        $rs = array();
        $strs = array();

        if (gettype($target) == "string") {
            $strs[0] = $target;
        } else if (gettype($target) == "array") {
            $strs = $target;
        }

        for ($n = 0; $n < count($strs); $n++) {
            if (gettype($strs[$n]) != "string") continue;

            $preg_array = array(); preg_match($reg, $strs[$n], $preg_array);

            if (!empty($preg_array[$num])) {
                $rs[] = $preg_array[$num];
            }
        }

        if (count($rs) == 1)
            return $rs[0];

        return $rs;
    }

    public static function GetFullByAttr($tag, $attr, $value, $str) {
        if (gettype($tag) != "string" || !preg_match("/^[0-9a-zA-Z\.\+\=\-_]+$/", $tag)) return false;
        if (gettype($attr) != "string") return false;
        if (gettype($value) != "string") return false;

        $tag = preg_replace("/([\.\+\=\-_])/", "\\\\$1", $tag);
        $attr = preg_replace("/([\/\\\.\+\=\-])/", "\\\\$1", $attr);
        $attr = preg_replace("/\*/", "([A-Za-z0-9\-\_]*?)", $attr);
        $value = preg_replace("/([\/\\\.\+\=\-])/", "\\\\$1", $value);
        $value = preg_replace("/\*/", "([^<>]*?)", $value);
        $reg = "/< *{$tag}[^<>]*?{$attr} *= *([\\\\\"]+{$value}[\\\\\"]+|[\\\\']+{$value}[\\\\']+|{$value})[^<>]*?>/";

        return Parser::GetFullByReg($reg, $tag, $str);
    }

    public static function GetFullByTag($tag, $str, $type = "all") {
        if (gettype($tag) != "string" || !preg_match("/^[0-9a-zA-Z\.\+\=\-_]+$/", $tag)) return false;

        $tag = preg_replace("/([\.\+\=\-_])/", "\\\\$1", $tag);

        if ($type == "all")
            $reg = "/< *{$tag}[^<>]*?>/";
        else
            $reg = "/< *{$tag} *>/";

        return Parser::GetFullByReg($reg, $tag, $str);
    }

    public static function GetFullByReg($reg, $tag, $target) {
        $standalone_tags = ['area', 'command', 'img', 'meta', 'base', 'embed', 'input', 'param', 'br', 'hr', 'link', 'source', 'col'];
        $rs_elements = array();
        $strs = array();

        if (gettype($target) == "string") {
            $strs[0] = $target;
        } else if (gettype($target) == "array") {
            $strs = $target;
        }

        for ($n = 0; $n < count($strs); $n++) {
            if (gettype($strs[$n]) != "string") continue;
            $str = $strs[$n];

            if (array_search($tag, $standalone_tags) !== false) {
                $elements = array(); preg_match_all("/<[^<>]+?>/", $str, $elements);

                for ($i = 0; $i < count($elements[0]); $i++) {
                    if (preg_match($reg, $elements[0][$i])) {
                        $rs_elements[] = $elements[0][$i];
                    }
                }
            } else {
                $elements = array(); preg_match_all("/<[^<>]+?>[^<]*/", $str, $elements);

                for ($i = 0; $i < count($elements[0]); $i++) {
                    if (preg_match($reg, $elements[0][$i])) {
                        $rs_element = "";
                        $opentag_count = 0;
                        $closetag_count = 0;
                        $opentag_reg = "/< *{$tag}[^<>]*?>/";
                        $closetag_reg = "/< *\\/ *{$tag} *>/";

                        do {
                            if (preg_match($opentag_reg, $elements[0][$i])) {
                                $opentag_count++;
                            } else if (preg_match($closetag_reg, $elements[0][$i])) {
                                $closetag_count++;
                            }
                            $rs_element.= $elements[0][$i];
                            $i++;
                        } while ($opentag_count > $closetag_count && $i < count($elements[0]));

                        $rs_elements[] = preg_replace("/>[^<>]*$/", ">", $rs_element);
                        $i--;
                    }
                }
            }
        }

        if (count($rs_elements) == 1)
            return $rs_elements[0];

        return $rs_elements;
    }
}
