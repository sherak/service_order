<?php

require 'db_connection.php';

class html_form {
    
    private $tag;
    private $html;
    
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
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= ">\n";
        if(isset($header)) {
            $str .= "  <option value=\"\">$header</option>\n";
        }
        foreach($option_list as $val => $text) {
            $str .= $check_val ? "  <option value=\"$val\"": "  <option";
            if(isset($selected_value) && ($selected_value === $val || $selected_value === $text)) {
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

    function getHtml($form_name, $method='post', $new_line=true) {
        $json = file_get_contents('forms.json');
        $json = json_decode($json, true);
        $c = new db_connection();
        $sql = "SELECT city FROM service_provider";
        $option_list = $c->query($sql);
        $cities_arr = array();
        foreach($option_list as $key => $value) {
            $cities_arr[$value[key($value)]] = $value[key($value)]; 
        }
        $str = $method == 'get' ? $this->start_form($form_name . '.php', $method) : $this->start_form($form_name . '.php');
        foreach($json[$form_name] as $json) {
            $checked = '';
            # TODO: better checking for editing profile
            if(isset($_SESSION['user']) and $form_name == 'edit_profile') {
                $user = $_SESSION['user'];
                # TODO: password unhashing
                if($json['type'] != 'radio')
                    $json['value'] = isset($user[$json['name']]) ? $user[$json['name']] : $json['value'];
                else
                    $checked =  $user[$json['name']] == $json['value']  ? 'checked' : '';
            }
            $str .= $json['label'];
            if($json['type'] == 'submit')
                $str .= $this->add_button($json['type'], $json['name'], $json['value']);
            else if($json['type'] == 'select') {
                $str .= $this->add_select_list($json['name'], $cities_arr);
            }
            else if($json['type'] == 'textarea') {
                $str .= $this->add_textarea($json['name']);
            }
            else 
                $str .= $this->add_input($json['type'], $json['name'], $json['value'], array('id' => $json['name'], 'required' => $json['required'], $checked => $checked));
            if($new_line)
                $str .= '</br>';
        }
        $str .= $this->end_form();
        return $str;
    }

}