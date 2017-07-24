<?php

require 'html_form.php';

$x = new html_form();                          
echo $x->getHtml('search_engine');

$tag = 'a';

$attr_ar = array("href" => "edit_profile.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Edit profile';
$str .= $x->end_tag($tag);
echo $str;

echo '<br>';

$attr_ar = array("href" => "logout.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Logout';
$str .= $x->end_tag($tag);
echo $str;

