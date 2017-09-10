<?php

include_once 'inc/db_connection.php';

class html_form {
    
    private $tag;
    private $html;
    private $form;
    private $form_name;
    private $success_msg = '';

    function __construct($form_name) {
        static $json = null;
        if($json == null) {
            $json = file_get_contents('forms.json');
            $json = json_decode($json, true);
        }

        $this->form_name = $form_name;
        $this->form = [];
        foreach($json[$form_name] as $field) {
            $this->form[$field['name']] = $field;
            $this->form[$field['name']]['invalid'] = false;
        }
    }

    function add_attributes($attr_ar) {
        $str = '';
        // check minimized (boolean) attributes
        $min_atts = array('checked', 'disabled', 'readonly', 'multiple',
                'required', 'autofocus', 'novalidate', 'formnovalidate'); 
        foreach($attr_ar as $key=>$val) {
            if(!$key)
                continue;
            if(in_array($key, $min_atts)) {
                if(!empty($val)) { 
                    $str .= " $key";
                }
            } else {
                $str .= " " . htmlentities((string)$key) . "=" . htmlentities((string)$val);
            }
        }
        return $str;
    }

    function start_form($class='', $action='#', $method='post', $id='', $attr_ar=array()) {
        $str = "<form class=\"$class\" action=\"$action\" method=\"$method\"";
        if(!empty($id)) {
            $str .= " id=\"$id\"";
        }
        $str .= $attr_ar ? $this->add_attributes($attr_ar) . '>': '>';
        return $str;
    }
    
    function add_input($type, $class='', $name, $value, $placeholder='', $attr_ar = array()) {
        if(is_array($value))
            var_dump($name,$value);
        $str = "<input type=\"$type\" class=\"$class\" name=\"$name\" value=\"" . htmlentities($value) . "\"  placeholder=\"$placeholder\"";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= '>';
        return $str;
    }

    function add_input_no_placeholder($type, $class='', $name, $value, $attr_ar = array()) {
        if(is_array($value))
            var_dump($name,$value);
        $str = "<input type=\"$type\" class=\"$class\" name=\"$name\" value=\"" . htmlentities($value) . "\"";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= '>';
        return $str;
    }

    function add_stars($name, $val) {
        static $title = ['', 'Poor', 'Fair', 'Average', 'Good', 'Excellent'];
        $str = '<fieldset class="rating">';
        for($i = 5; $i > 0; $i--)
            $str .= '<input type="radio" id="star' . $i . '" name="' . htmlentities($name) . '" value="' . $i . '"' . ($val == $i ? ' checked' : '') . ' required /><label class = "full" for="star' . $i . '" title="' . $title[$i] . '"></label>';
        $str .='</fieldset><br>';
        return $str;
    }

    function add_button($type, $class='', $name, $value) {
        $str = "<button type=\"$type\" class=\"$class\" name=\"$name\">";
        $str .= htmlentities($value) . '</button>';
        return $str;
    }
    
    function add_textarea($name, $rows=4, $cols=30, $value='', $class='', $attr_ar=array()) {
        $str = "<textarea class=\"$class\" name=\"$name\" rows=\"$rows\" cols=\"$cols\"";
        if($attr_ar) {
            $str .= $this->add_attributes( $attr_ar );
        }
        $str .= ">" . htmlentities($value) . "</textarea>";
        return $str;
    }
    
    function add_label($id, $text, $attr_ar=array()) {
        $str = "<label for=\"$id\"";
        if($attr_ar) {
            $str .= $this->add_attributes($attr_ar);
        }
        $str .= ">" . htmlentities($text) . "</label>";
        return $str;
    }
    
    // option values and text come from one array (can be assoc)
    // $check_val false if text serves as value (no value attr)
    function add_select_list($name, $option_list, $class='', $check_val=true, $selected_value=NULL,
            $header=NULL, $attr_ar=array()) {
        $str = "<select class=\"$class\" name=\"$name\"";
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

    function get_value($name) {
        return isset($this->form[$name]['value']) ? $this->form[$name]['value'] : null;
    }

    function set_value($name, $value) {
        $this->form[$name]['value'] = $value;
    }

    function set_values($values) {
        foreach($values as $name => $value) {
            if(isset($this->form[$name]) && $this->form[$name]['type'] != 'submit')
                $this->form[$name]['value'] = $value;
        }
    }

    function set_error($name, $error) {
        $this->form[$name]['invalid'] = $error;
    }

    function check_errors() {
        $form_valid = true;
        foreach($this->form as $field) {
            if(isset($field['required']) && $field['required'] && !$field['value']) {
                $field['invalid'] = isset($field['error']) && $field['error'] ?: $field['label'] . ' is required.';
            }
            if($field['invalid'])
                $form_valid = false;
        }
        return !$form_valid;
    }

    function set_success_msg($msg) {
        $this->success_msg = $msg;
    }

    function get_html($class='', $action, $method='post', $enctype=false) {
        $hidden_fields = '';
        if($method == 'get' && ($n = strpos($action, '?')) !== false) {
            $par = substr($action, $n + 1);
            $action = substr($action, 0, $n);
            parse_str($par, $par);
            foreach($par as $name => $val)
                $hidden_fields .= '<input type="hidden" name="' . htmlentities($name). '" value="' . htmlentities($val) . '">';
        }

        $c = new db_connection();
        if($enctype) {
            $str = $this->start_form($class, $action, $method, '', array('enctype' => 'multipart/form-data'));
            $str .= $hidden_fields;
        }
        else {
            $str = $this->start_form($class, $action, $method);
            $str .= $hidden_fields;
        }
        foreach($this->form as $field) {
            if($field['type'] != 'submit' ) {
                $str .= '<div class="form-group">';
                if(isset($field['class']) && !empty($field['class']))
                    $field['class'] .= ' form-control';
                else
                    $field['class'] = 'form-control';
                if(!strpos($action, 'my_craft_firm') && !strpos($action,  'edit_profile')) {
                    $field['placeholder'] = '' . $field['label'] . '';
                    $field['label'] = '';
                }
                else {
                    $field['label'] = '<h5>' . $field['label'] . '</h5>'; 
                    $field['placeholder'] = '';
                }
            }
            else {
                if(isset($field['class']) && !empty($field['class']))
                    $field['class'] .= ' btn btn-default';
                else
                    $field['class'] = 'btn btn-default'; 
            }
            $checked = '';
            if($field['type'] != 'hidden')
                $str .= $field['label'];
            if($field['type'] == 'submit') {
                $str .= $this->add_button($field['type'], $field['class'], $field['name'], $field['value']);
            }
            else if($field['type'] == 'textarea') {
                $str .= $this->add_textarea($field['name'], 8, 40, $field['value'], $field['class'], array('required' => $field['required']));
            }
            else if($field['type'] == 'radio' && $field['name'] == 'gender') {
                foreach($field['values'] as $value => $label) {
                    $str .= $this->add_input_no_placeholder($field['type'], '', $field['name'], $value, array('required' => $field['required'], 'checked' => $value == $field['value'])) . ' <span>' . $label . '</span> '; 
                }
            }
            else if($field['type'] == 'radio' && $field['name'] == 'stars') {
                $str .= $this->add_stars($field['name'], $field['value']);
            }
            else if($field['type'] == 'select') {
                $value_assoc = array();
                foreach($field['values'] as $value) {
                     $value_assoc[$value] = $value;
                }
                $str .= $this->add_select_list($field['name'], $value_assoc, $field['class'], true, $field['value']);
            }
            else if($field['type'] == 'password') {
                $str .= $this->add_input($field['type'], $field['class'], $field['name'], '', $field['placeholder'], array('required' => $field['required'], $checked => $checked));
            }
            else if($field['type'] == 'file') {
                $str .= $this->add_input($field['type'], '', $field['name'], '', $field['placeholder'], array('required' => $field['required'], $checked => $checked));
            }
            else if($field['type'] == 'hidden') {
                $str .= $this->add_input_no_placeholder($field['type'], $field['class'], $field['name'], $field['value'],  array('required' => $field['required'], $checked => $checked));
            }
            else {
                $str .= $this->add_input($field['type'], $field['class'], $field['name'], $field['value'], $field['placeholder'],  array('required' => $field['required'], $checked => $checked));
            }
            if($field['invalid']) {
                $str .= '<div class="error">' . $field['invalid'] . '</div>';
            }
            if($field['type'] != 'submit')
                $str .= '</div> ';
            //if($field['type'] != 'hidden' && $field['type'] != 'select')
            //    $str .= '<br>';   
        }
        if(!empty($this->success_msg))
            $str .= $this->success_msg;
        $str .= $this->end_form();
        return $str;
    }
}