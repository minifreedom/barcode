<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index extends CI_Controller {

	public function __construct() {        
		parent::__construct();
		$this->load->library('pdf');
		$this->pdf->fontpath = 'fonts/';

		$this->load->library('zend');
        $this->zend->load('Zend/Barcode');
		
		$this->load->helper('download');
		$this->load->library('zip');
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
		$img = imagejpeg($barcode,'assets/barcode/barcode'.$id.'.jpg', 100);
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
 		  $sex = $student->sex;
		  $pic = $student->photo;
		  $m  = $student->classst;
		}
		$passId = substr($pid,0,1).'-'.substr($pid,1,4).'-'.substr($pid,5,5).'-'.substr($pid,10,2).'-'.substr($pid,12,1);
		
		$this->pdf->FPDF('P','cm','A4');
		$this->pdf->AddPage();
		$this->pdf->Line(0.25,0.25,0.25,8);
		$this->pdf->Line(20.25,0.25,20.25,8);
		
		$this->pdf->Line(10.25,0.25,10.25,8);
		
		$this->pdf->Line(0.25,0.25,20.25,0.25);
		$this->pdf->Line(0.25,8,20.25,8);
		
		$this->pdf->Image(base_url().'assets/images/dla.jpg',0.85,0.5,2,2);
		$this->pdf->Image(base_url().'assets/stdpic/'.$pic,0.5,2.7,2.75,3.5);
		$this->pdf->Image(base_url('assets/barcode/barcode'.$id.'.jpg'),0.7,6.5,2.5,0.75);
		$this->pdf->AddFont('angsau','','angsau.php');
		$this->pdf->SetFont('angsau','',26);
		$this->pdf->Text(3.7,1.5,iconv('UTF-8','TIS-620','บัตรประจำตัวนักเรียน'));
		$this->pdf->SetFont('angsau','',18);
		$this->pdf->Text(3.7,2.3,iconv('UTF-8','TIS-620','โรงเรียนต้นแก้วผดุงพิทยาลัย'));
		$this->pdf->AddFont('THSarabun','','THSarabun.php');
		$this->pdf->SetFont('THSarabun','',18);
		if($sex=='girl')
		{
			$this->pdf->Text(3.7,3.2,iconv('UTF-8','TIS-620','เด็กหญิง'));
			$this->pdf->Text(5.4,3.2,$name.' '.$surename);
		}
		if($sex=='men')
		{
			$this->pdf->Text(3.7,3.2,iconv('UTF-8','TIS-620','เด็กชาย '));
			$this->pdf->Text(5.1,3.2,$name.' '.$surename);
		}
		
		$this->pdf->SetFont('angsau','',18);
		$this->pdf->Text(3.7,4,iconv('UTF-8','TIS-620','เลขประจำตัวนักเรียน '.$sid));
		$this->pdf->Text(3.7,4.7,iconv('UTF-8','TIS-620','บัตรประชาชน '.$passId));
		
		if($m=='p1' or $m=='p2' or $m=='p3')
		{
			$this->pdf->SetFont('THSarabun','',18);
			$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  '));
			$this->pdf->SetFont('angsau','',18);
			$this->pdf->Text(5.3,5.5,iconv('UTF-8','TIS-620','ประถมศึกษาตอนต้น'));
		}
		if($m=='p4' or $m=='p5' or $m=='p6')
		{
			$this->pdf->SetFont('THSarabun','',18);
			$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  '));
			$this->pdf->SetFont('angsau','',18);
			$this->pdf->Text(5.3,5.5,iconv('UTF-8','TIS-620','ประถมศึกษาตอนปลาย'));
		}
		if($m=='m1' or $m=='m2' or $m=='m3')
		{
			$this->pdf->SetFont('THSarabun','',18);
			$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  '));
			$this->pdf->SetFont('angsau','',18);
			$this->pdf->Text(5.3,5.5,iconv('UTF-8','TIS-620','มัธยมศึกษาตอนต้น'));
		}
		$this->pdf->Text(3.7,6.5,iconv('UTF-8','TIS-620','.....................................................'));
		$this->pdf->Text(5.5,7.2,iconv('UTF-8','TIS-620','ผู้อำนวยการ'));
		
		$this->pdf->Image(base_url().'assets/images/logo.png',10.7,0.5,2,2.5);
		$this->pdf->Image(base_url().'assets/images/qrcode_tk.png',10.5,4,2.5,2.5);
		$this->pdf->SetFont('angsau','',26);
		$this->pdf->Text(13,1.5,iconv('UTF-8','TIS-620','โรงเรียนต้นแก้วผดุงพิทยาลัย'));
		$this->pdf->SetFont('angsau','',18);
		$this->pdf->Text(13.2,2.2,iconv('UTF-8','TIS-620','Tonkaewphadungpittayalai School'));
		$this->pdf->Text(13.2,2.7,iconv('UTF-8','TIS-620','www.tonkaew.ac.th'));
		$this->pdf->Text(13.2,3.5,iconv('UTF-8','TIS-620','69 หมู่ 1 ตำบล ขุนคง อำเภอ หางดง '));
		$this->pdf->Text(13.2,4.2,iconv('UTF-8','TIS-620','จังหวัด เชียงใหม่ รหัสไปรษณีย์ 50230 '));
		$this->pdf->Text(13.2,5,iconv('UTF-8','TIS-620','โทร 053-434175 '));
		$this->pdf->Text(13.2,6.2,iconv('UTF-8','TIS-620','ออกบัตร 16 พฤษภาคม 2557'));
		$this->pdf->Text(13.2,7,iconv('UTF-8','TIS-620','บัตรหมดอายุ 16 พฤษภาคม 2559'));
		
		$this->pdf->Output();
	}
	
	public function download()
	{
		$students = $this->index_model->student_all();
		foreach($students as $student)
		{
			$id = $student->id_student;
			$pid  = $student->pass_student;
			$name = $student->name_student;
			$surename = $student->surename_student;
			$sex = $student->sex;
			$pic = $student->photo;
			$m  = $student->classst;
		
			$this->barcode($id);
			$passId = substr($pid,0,1).'-'.substr($pid,1,4).'-'.substr($pid,5,5).'-'.substr($pid,10,2).'-'.substr($pid,12,1);
			
			$this->pdf->FPDF('P','cm','A4');
			$this->pdf->AddPage();
			$this->pdf->Image(base_url().'assets/images/dla.jpg',0.85,0.5,2,2);
			$this->pdf->Image(base_url().'assets/stdpic/'.$pic,0.5,2.7,2.75,3.5);
			$this->pdf->Image(base_url('assets/barcode/barcode'.$id.'.jpg'),0.7,6.5,2.5,0.75);
			$this->pdf->AddFont('angsau','','angsau.php');
			$this->pdf->SetFont('angsau','',26);
			$this->pdf->Text(3.7,1.5,iconv('UTF-8','TIS-620','บัตรประจำตัวนักเรียน'));
			$this->pdf->SetFont('angsau','',18);
			$this->pdf->Text(3.7,2.3,iconv('UTF-8','TIS-620','โรงเรียนต้นแก้วผดุงพิทยาลัย'));
			$this->pdf->AddFont('THSarabun','','THSarabun.php');
			if($sex=='girl')
			{
				$this->pdf->Text(3.7,3.2,iconv('UTF-8','TIS-620','เด็กหญิง'));
				$this->pdf->Text(5.4,3.2,$name.' '.$surename);
			}
			if($sex=='men')
			{
				$this->pdf->Text(3.7,3,iconv('UTF-8','TIS-620','เด็กชาย '));
				$this->pdf->Text(5,3,$name.' '.$surename);
			}
			
			$this->pdf->Text(3.7,4,iconv('UTF-8','TIS-620','เลขประจำตัวนักเรียน '.$id));
			$this->pdf->Text(3.7,4.7,iconv('UTF-8','TIS-620','บัตรประชาชน '.$passId));
			
			if($m=='p1' or $m=='p2' or $m=='p3')
			{
				$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  ประถมศึกษาตอนต้น'));
			}
			if($m=='p4' or $m=='p5' or $m=='p6')
			{
				$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  ประถมศึกษาตอนปลาย'));
			}
			if($m=='m1' or $m=='m2' or $m=='m3')
			{
				$this->pdf->Text(3.7,5.5,iconv('UTF-8','TIS-620','ระดับชั้น  มัธยมศึกษาตอนต้น'));
			}
			$this->pdf->Text(3.7,6.5,iconv('UTF-8','TIS-620','.....................................................'));
			$this->pdf->Text(5.5,7.2,iconv('UTF-8','TIS-620','ผู้อำนวยการ'));
			
			$this->pdf->Image(base_url().'assets/images/logo.png',10.7,0.5,2,2.5);
			$this->pdf->Image(base_url().'assets/images/qrcode_tk.png',10.5,4,2.5,2.5);
			$this->pdf->SetFont('angsau','',26);
			$this->pdf->Text(13.2,1.5,iconv('UTF-8','TIS-620','โรงเรียนต้นแก้วผดุงพิทยาลัย'));
			$this->pdf->SetFont('angsau','',18);
			$this->pdf->Text(13.2,2.2,iconv('UTF-8','TIS-620','Tonkaewphadungpittayalai School'));
			$this->pdf->Text(13.2,2.7,iconv('UTF-8','TIS-620','www.tonkaew.ac.th'));
			$this->pdf->Text(13.2,3.5,iconv('UTF-8','TIS-620','69 หมู่ 1 ตำบล ขุนคง อำเภอ หางดง '));
			$this->pdf->Text(13.2,4.2,iconv('UTF-8','TIS-620','จังหวัด เชียงใหม่ รหัสไปรษณีย์ 50230 '));
			$this->pdf->Text(13.2,5,iconv('UTF-8','TIS-620','โทร 053-434175 '));
			$this->pdf->Text(13.2,6.2,iconv('UTF-8','TIS-620','ออกบัตร 16 พฤษภาคม 2557'));
			$this->pdf->Text(13.2,7,iconv('UTF-8','TIS-620','บัตรหมดอายุ 16 พฤษภาคม 2559'));
			
			$this->pdf->Output('assets/download/pdf'.$id.'.pdf','F');
		}
		$path = base_url('assets/download/');
		$this->zip->read_dir($path); 
		$this->zip->download('pdf.zip');
	}
	
}
