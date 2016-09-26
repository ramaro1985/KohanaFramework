<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'ioc' => array(

			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'IOC',

			// A short description of this module, shown on the index page
			'description' => 'Dependency Injection component.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008-2009 Fabien Potencier',
		)	
	)
);
