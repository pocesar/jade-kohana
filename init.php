<?php defined('SYSPATH') OR die('No direct access allowed.');

define('JADE_PATH', realpath(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Jade').DIRECTORY_SEPARATOR);

/**
 * Test route
Route::set('jade-test', 'jadetest')
	->defaults(array(
		'directory' => 'Jade',
		'controller' => 'Test',
		'action' => 'index'
	));
 */

spl_autoload_register(function($class){
	Kohana::auto_load($class, 'vendor/src');
}, false);