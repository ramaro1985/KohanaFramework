<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Wrapper to mPdf class.
 *
 * @package		Kohana/Mpdf
 * @category	Base
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @author      Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 */
class KMpdf
{
    private $mpdf;
    
    public function __construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
    {
        $this->mpdf = new mPDF($mode, $format, $default_font_size, $default_font, 
            $mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $orientation);
    } 
    
    public function download($filename)
    {
        $this->mpdf->Output($filename.'.pdf','D');
        exit;
    }
    
    public function new_page($format='A4')
    {
        $this->mpdf->WriteHTML("<pagebreak sheet-size=\"{$format}\" />");
        return $this;
    }
    
    public function add_html($html)
    {
        $this->mpdf->WriteHTML($html);
        return $this;
    }
}