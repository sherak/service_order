<?php

class html_form {
    
    private $tag;
    private $html;
    
    function __construct($form_name) {
        $this->form_name = $form_name;
    }

    function add_attributes($attr_ar) {
        $str = '';
        // check minimized (boolean) attributes
        $min_atts = array('checked', 'disabled', 'readonly', 'multiple',
                'required', 'autofocus', 'novalidate', 'formnovalidate'); 
        foreach($attr_ar as $key=>$val) {
            if(in_array($key, $min_atts)) {
                if(!empty($val)) { 
                    $str .= " $key";
                }
            } else {
                $str .= " " . (string)addslashes($key) . "=" . (string)addslashes($val);
            }
        }
        return $str;
    }

    function start_form($action='#', $method='post', $id='', $attr_ar=array()) {
        $str = "<form action=\"$action\" method=\"$method\"";
        if(!empty($id)) {
            $str .= " id=\"$id\"";
        }
        $str .= $attr_ar ? $this->add_attributes($attr_ar) . '>': '>';
        return $str;
    }
    
    function add_input($type, $name, $value, $attr_ar = array()) {
        $str = "<input type=\"$type\" name=\"$name\" value=\"$value\"";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= '>';
        return $str;
    }

    function add_button($type, $name, $value) {
        $str = "<button type=\"$type\" name=\"$name\">";
        $str .= $value . '</button>';
        return $str;
    }
    
    function add_textarea($name, $rows=4, $cols=30, $value='', $attr_ar=array()) {
        $str = "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\"";
        if($attr_ar) {
            $str .= $this->add_attributes( $attr_ar );
        }
        $str .= ">$value</textarea>";
        return $str;
    }
    
    function add_label($id, $text, $attr_ar=array()) {
        $str = "<label for=\"$id\"";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= ">$text</label>";
        return $str;
    }
    
    // option values and text come from one array (can be assoc)
    // $check_val false if text serves as value (no value attr)
    function add_select_list($name, $option_list, $check_val=true, $selected_value=NULL,
            $header=NULL, $attr_ar=array()) {
        $str = "<select name=\"$name\"";
        if($attr_ar) {
            $str .= $this->add_attributes( $attr_ar );
        }
        $str .= ">\n";
        if(isset($header)) {
            $str .= "  <option value=\"\">$header</option>\n";
        }
        foreach ($option_list as $val => $text) {
            $str .= $check_val ? "  <option value=\"$val\"": "  <option";
            if(isset($selected_value) && ( $selected_value === $val || $selected_value === $text)) {
                $str .= ' selected';
            }
            $str .= ">$text</option>\n";
        }
        $str .= "</select>";
        return $str;
    }

    function add_select_list_arrays($name, $val_list, $txt_list, $selected_value=NULL,
            $header=NULL, $attr_ar=array()) {
        $option_list = array_combine($val_list, $txt_list);
        $str = $this->addSelectList($name, $option_list, true, $selected_value, $header, $attr_ar);
        return $str;
    }
    
    function start_tag($tag, $attr_ar=array()) {
        $this->tag = $tag;
        $str = "<$tag";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= '>';
        return $str;
    }
    
    function end_tag($tag='') {
        $str = $tag ? "</$tag>": "</$this->tag>";
        $this->tag = '';
        return $str;
    }
    
    function add_empty_tag($tag, $attr_ar=array()) {
        $str = "<$tag";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= '>';
        return $str;
    }

    function end_form() {
        return "</form>";
    }

    function getHtml() {
        $str = file_get_contents('forms.json');
        $json = json_decode($str, true);
        if($this->form_name == 'login') {
            $str = $this->add_input($json['login'][0]['type'], $json['login'][0]['name'], '', array('required' => $json['login'][0]['required']));
            $str .= $this->add_input($json['login'][1]['type'], $json['login'][1]['name'], '', array('required' => $json['login'][1]['required']));
            $str .= $this->add_button($json['login'][2]['type'], $json['login'][2]['name'], $json['login'][2]['value']);
            return $str;
        }
        else if($this->form_name == 'register') {
            $str = $this->add_input($json['register'][0]['type'], $json['register'][0]['name'], '', array('register' => $json['login'][0]['required']));
            $str .= $this->add_input($json['register'][1]['type'], $json['register'][1]['name'], '', array('register' => $json['login'][1]['required']));
            $str .= $this->add_button($json['register'][6]['type'], $json['register'][6]['name'], $json['register'][6]['value']);
            return $str;
        }
    }
    
}

$x = new html_form('login');
echo $x->getHtml();
$x = new html_form('register');
echo $x->getHtml();