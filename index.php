<?php

require 'html_form.php';

$x = new html_form();

echo $x->getHtml('login');
if( !empty( $_REQUEST['login_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['login_alert'] );
}

echo $x->getHtml('register');
if( !empty( $_REQUEST['register_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['register_alert'] );
}

echo $x->getHtml('search_engine');