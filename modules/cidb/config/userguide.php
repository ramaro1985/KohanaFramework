<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'cidb' => array(

			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'CI Database',

			// A short description of this module, shown on the index page
			'description' => 'Database drivers and Active Record implementation from CodeIgniter.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008 - 2011, EllisLab, Inc. <br /> &copy; 2008 - 2011, Rafael Ernesto Espinosa Santiesteban',
		)	
	)
);
