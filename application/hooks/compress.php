<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function compress() {
  ini_set("pcre.recursion_limit", "16777");
  $CI =& get_instance();
  $buffer     	= $CI->output->get_output();
  $new_buffer 	= preg_replace('/(?:(?:\r\n|\r|\n)\s*){1}/s', "\n", $buffer);
  
  if ($new_buffer === null) {
    $new_buffer = $buffer;
  }

  $CI->output->set_output($new_buffer);
  $CI->output->_display();
}