<?php
class htmtemp {
    public $filename = '';
    public $file = '';
    public $html = '';
    public $data = array();
    public $error_msg = "";
    function __construct($dir = '', $data = '') {
        if ($dir != '') { $this->read_file($dir); }
        if ($data != '') { $this->data = $data; }
        if ($dir != '' && $data != '') {  $this->combine(); }
    }

    function gen($dir, $data) {
        try {
            if ($dir == '') { throw new Exception("[Error] dir not assigned"); }
            if ($data == '') { throw new Exception("[Error] data not assigned"); }
            if (!is_array($data)) { throw new Exception("[Error] Invalid data"); }
            if (count($data) == 0) { throw new Exception("[Error] data is empty"); }
            $this->html = "";
            $this->read_file($dir);
            $this->data = $data;
            $this->combine();

            return $this->html;
        } catch (Exception $e) {
            $this->add_error_msg($e->getMessage());
            return "";
        }
    }

    //filename is same like dir
    function read_file($filename = '') {
        try {
            if ($filename != '') { $this->filename = $filename; }
            if ($this->filename == "") { throw new Exception("[Error] filename not assigned"); }
            if (!file_exists($this->filename)) { throw new Exception("[Error] file not found"); }
            $myfile = fopen($this->filename, "r") or die("Unable to open file!");
            $this->file = fread($myfile,filesize($this->filename));
            fclose($myfile);

            return true;
        } catch (Exception $e) {
            $this->add_error_msg($e->getMessage());
            return false;
        }
    }

    //combine html with data variable
    function combine() {
        try {
            if (count($this->data) == 0) { throw new Exception("[Error] data is empty"); }
            if ($this->file == "") { throw new Exception("[Error] file is empty"); }
            $str = str_split($this->file);
            $var_area = false;
            $var_name = "";
            foreach ($str as $val) {
                if ($val == "}") {
                    $this->html .= $this->extract($this->key_split($var_name));
                    $var_name = "";
                    $var_area = false;
                } else if ($val == "{") {
                    $var_area = true;
                } else if ($var_area) {
                    $var_name .= $val;
                } else {
                    $this->html .= $val;
                }
            }
        } catch (Exception $e) {
            $this->add_error_msg($e->getMessage());
            return false;
        }
    }

    //split string to string array by sign dot(.)
    //Example Input: 1d.2d.3d
    //Example Output: array('1d','2d','3d')
    function key_split($str) {
        try {
            if ($str == "") { throw new Exception("[Error] parameter str in key_split function is empty"); }
            $key = array();
            $str = str_split($str);
            $name = "";
            foreach ($str as $val) {
                if ($val == ".") {
                    $key[] = $name;
                    $name = "";
                } else {
                    $name .= $val;
                }
            }
            $key[] = $name;

            return $key;
        } catch (Exception $e) {
            $this->add_error_msg($e->getMessage());
            return array();
        }
    }

    //extract data from data variable by array key
    function extract($key) {
        try {
            if (count($key) == 0) { throw new Exception("{[Error] key is empty"); }
            $return = $this->data;
            foreach ($key as $val) {
                if (!isset($return[$val])) { throw new Exception("[Error] key not found: $val"); }
                $return = $return[$val];
            }
            return $return;
        } catch (Exception $e) {
            $this->add_error_msg($e->getMessage());
            return false;
        }
    }

    //record error log
    function add_error_msg($msg) {
        $br = $this->error_msg == "" ? "<br>" : "";
        $this->error_msg .= $br . $msg;
        return true;
    }
}