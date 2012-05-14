<?php
/*
 * plugin Name: Event Manger with Surcharge Options
 * author: Mahibul Hasan Sohag
 * */


//if(!class_exists('EM_Object')) return;

define('EM_SURCHARGE_DIR', dirname(__FILE__));
define('EM_SURCHARGE_URL', plugins_url('', __FILE__));

include EM_SURCHARGE_DIR . '/classes/surcharge.class.php';
EM_Surcharge :: init();

?>
