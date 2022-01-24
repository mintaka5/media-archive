<?php 
$o=array();

############### GET ###############

$o['GET']=array();

#==== GET assets/one

$o['GET']['assets/one']=array (
	  'class_name' => 'Assets',
	  'method_name' => 'getOne',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);

#==== GET assets/one/:id

$o['GET']['assets/one/:id']=array (
	  'class_name' => 'Assets',
	  'method_name' => 'getOne',
	  'arguments' => 
	  array (
	    'id' => 0,
	  ),
	  'defaults' => 
	  array (
	    0 => NULL,
	  ),
	  'metadata' => 
	  array (
	  ),
	  'method_flag' => 0,
	);
return $o;
?>