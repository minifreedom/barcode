<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index extends CI_Controller {

	public function __construct() {        
		parent::__construct();
		$this->load->library('pdf');
		$this->pdf->fontpath = 'fonts/';

		$this->load->library('zend');
        $this->zend->load('Zend/Barcode');
		
	}
	
	public function index()
	{
		$config['base_url']=base_url('page');
		$config['total_rows']=$this->index_model->count_student();
		$config['per_page']=20;
		$config['uri_segment']=2;
		$config['use_page_numbers'] = False;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
											
		$this->pagination->initialize($config);
		$page=$this->uri->segment(2);
		$data['query'] = $this->index_model->student($config['per_page'],$page); 
		$data['links'] = $this->pagination->create_links();
			
		$this->load->view('header');
		$this->load->view('data',$data);
		$this->load->view('footer'); 

	}
	
	public function student_id($id)
	{
		$students = $this->index_model->student_id($id);
		foreach($students as $student)
		{
		  $sid = $student->id_student;
		  $name = $student->name_student;
		  $surename = $student->surename_student;
		}
		echo '#'.$sid.' '.$name.' '.$surename;
	}

	private function barcode($id) 
	{
		$barcode = Zend_Barcode::draw('code39', 'image', array('text' => $id), array());
		imagejpeg($barcode,'assets/barcode/barcode.jpg', 100);
		//imagedestroy($barcode); 
	}
	 
	public function student_id_pdf($id)
	{
		$this->barcode($id);
		$students = $this->index_model->student_id($id);
		foreach($students as $student)
		{
		  $sid = $student->id_student;
		  $pid  = $student->pass_student;
		  $name = $student->name_student;
		  $surename = $student->surename_student;
		  $pic = $student->photo;
		  $sex = $student->sex_student;
		  $m  = $student->classst;
		}
		$passId = substr($pid,0,1).'-'.substr($pid,1,4).'-'.substr($pid,5,5).'-'.substr($pid,10,2).'-'.substr($pid,12,1);
		
		$this->pdf->FPDF('L','cm',array(8.5,5.5));
		$this->pdf->AddPage();
		$this->pdf->Image(base_url().'assets/images/dla.jpg',0.65,0.2,1.25,1.25);
		$this->pdf->Image(base_url().'assets/stdpic/'.$pic,0.5,1.5,1.75,2.5);
		$this->pdf->Image(base_url('assets/barcode/barcode.jpg'),0.4,4.5,2,0.75);
		$this->pdf->AddFont('THSarabun','','THSarabun.php');
		$this->pdf->SetFont('THSarabun','',18);
		$this->pdf->Text(3,1,iconv('UTF-8','TIS-620','บัตรประจำตัวนักเรียน'));
		$this->pdf->AddFont('THSarabun','','THSarabun.php');
		$this->pdf->SetFont('THSarabun','',15);
		$this->pdf->Text(3,1.5,iconv('UTF-8','TIS-620','โรงเรียนแก้วผดุงพิทยาลัย'));
		if($sex=='หญิง')
		{
			$this->pdf->Text(3,2,iconv('UTF-8','TIS-620','เด็กหญิง'));
		}else{
			$this->pdf->Text(3,2.2,iconv('UTF-8','TIS-620','เด็กชาย '));
		}
		$this->pdf->Text(4.1,2.2,$name.' '.$surename);
		$this->pdf->SetFont('THSarabun','',10);
		$this->pdf->Text(3,2.8,iconv('UTF-8','TIS-620','เลขประจำตัวนักเรียน '.$sid));
		$this->pdf->Text(3,3.3,iconv('UTF-8','TIS-620','บัตรประชาชน '.$passId));
		
		if($m=='p1' or $m=='p2' or $m=='p3')
		{
			$this->pdf->Text(3,3.8,iconv('UTF-8','TIS-620','ระดับชั้น  ประถมศึกษาตอนต้น'));
		}
		if($m=='p4' or $m=='p5' or $m=='p6')
		{
			$this->pdf->Text(3,3.8,iconv('UTF-8','TIS-620','ระดับชั้น  ประถมศึกษาตอนปลาย'));
		}
		if($m=='m1' or $m=='m2' or $m=='m3')
		{
			$this->pdf->Text(3,3.8,iconv('UTF-8','TIS-620','ระดับชั้น  มัธยมศึกษาตอนต้น'));
		}
		$this->pdf->Text(3,4.6,iconv('UTF-8','TIS-620','.....................................................'));
		$this->pdf->Text(3.7,5,iconv('UTF-8','TIS-620','ผู้อำนวยการ'));
		
		/*-------*/
		
		$this->pdf->AddPage();
		$this->pdf->Image(base_url().'assets/images/logo.png',0.5,0.2,1.75,2);
		$this->pdf->Image(base_url().'assets/images/qrcode_tk.png',0.5,3,2,2);
		$this->pdf->AddFont('THSarabun','','THSarabun.php');
		$this->pdf->SetFont('THSarabun','',18);
		$this->pdf->Text(3,1,iconv('UTF-8','TIS-620','โรงเรียนแก้วผดุงพิทยาลัย'));
		$this->pdf->AddFont('THSarabun','','THSarabun.php');
		$this->pdf->SetFont('THSarabun','',14);
		$this->pdf->Text(3,1.5,iconv('UTF-8','TIS-620','Tonkaewphadungpittayalai School'));
		$this->pdf->Text(3,2,iconv('UTF-8','TIS-620','www.tonkaew.ac.th'));
		$this->pdf->SetFont('THSarabun','',10);
		$this->pdf->Text(3,2.5,iconv('UTF-8','TIS-620','69 หมู่ 1 ตำบล ขุนคง อำเภอ หางดง '));
		$this->pdf->Text(3,3,iconv('UTF-8','TIS-620','จังหวัด เชียงใหม่ รหัสไปรษณีย์ 50230 '));
		$this->pdf->Text(3,3.5,iconv('UTF-8','TIS-620','โทร 053-434175 '));
		$this->pdf->Text(3,4.5,iconv('UTF-8','TIS-620','ออกบัตร 16 พฤษภาคม 2557'));
		$this->pdf->Text(3,5,iconv('UTF-8','TIS-620','บัตรหมดอายุ 16 พฤษภาคม 2559'));
		
		$this->pdf->Output();
	}
	
	public function export_all()
	{
		
	}

}
