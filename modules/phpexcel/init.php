<?php defined('SYSPATH') or die('No direct script access.');
	/* PHP Excel integration */
    define('VENDOR_PACKAGE', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'classes' . 
        DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR);
	require Kohana::find_file('classes', 'vendor/PHPExcel');
