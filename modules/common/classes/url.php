<?php defined('SYSPATH') or die('No direct script access.');

/**
 * URL helper class.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @author     Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright  Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license    http://kohanaframework.org/license
 */
class URL extends Kohana_URL 
{
    
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     *
     * @uses    URL::site
     * @param   string      the URL
     * @param   string      the method: location or refresh
     * @return  string
     */
    public static function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        if ( ! preg_match('#^https?://#i', $uri))
        {
            $uri = URL::site($uri);
        }
        
        if($method == 'refresh')
        {
            header("Refresh:0;url=".$uri);
        }
        else
        {
           header("Location: ".$uri, TRUE, $http_response_code); 
        }

        exit;
    }
}