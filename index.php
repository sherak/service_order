<?php

require 'html_form.php';

$x = new html_form();                          
echo $x->getHtml('login');
echo $x->getHtml('register');
echo $x->getHtml('search_engine');