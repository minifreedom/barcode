<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('FPDF_FONTPATH','C:\xampp\htdocs\freelance\pdf\application\libraries\fpdf\font/');
require('fpdf/fpdf.php');
class Pdf extends FPDF
{

	function __construct($orientation='P', $unit='mm', $size='A4')
	{
    parent::__construct($orientation,$unit,$size);
	}
}