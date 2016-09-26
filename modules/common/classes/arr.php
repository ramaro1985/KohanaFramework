<?php defined('SYSPATH') or die('No direct script access.');

class Arr extends Kohana_Arr {
    
    /**
     * Convert input array into object.
     *
     * @param   array      arr     The input array
     * @return  boolean
     */
    public static function to_object($arr)
    {
        return json_decode(json_encode($arr));
    }
    
    /**
     * Convert input object into array.
     *
     * @param   object      obj     The input object
     * @return  boolean
     */
    public static function from_object($obj)
    {
        return json_decode(json_encode($obj), true);
    }
    
    /**
     * Overwrites an array with values from input arrays recursively.
     * Keys that do not exist in the first array will not be added!
     *
     *     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
     *     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');
     *
     *     // Overwrite the values of $a1 with $a2
     *     $array = Arr::overwrite($a1, $a2);
     *
     *     // The output of $array will now be:
     *     array('name' => 'jack', 'mood' => 'happy', 'food' => 'tacos')
     *
     * @param   array   master array
     * @param   array   input array that will overwrite existing values
     * @return  array
     */
    public static function overwrite_recursive($array1, $array2)
    {
        foreach (array_intersect_key($array2, $array1) as $key => $value)
		{
            if(is_array($value))
            {
                $array1[$key] = self::overwrite_recursive($array1[$key], $array2[$key]);
            }
            else
            {
                $array1[$key] = $value;
            }
		}
        return $array1;
    }
}