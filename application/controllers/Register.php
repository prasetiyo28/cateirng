<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('MCatering');

	}

	public function index()
	{
		$this->load->view('register');
	}

	public function reset_pass()
	{
		$this->load->view('reset');
	}


	public function kirim_reset()
	{
		$data['email'] = $_POST['email'];
		$cek_email = $this->MCatering->cek_id($data['email']);
		if (empty($cek_email)) {
			
			$this->session->set_flashdata('alert','gagal');
			redirect('register/reset_pass');

		}else{
			// echo  json_encode($cek_email);
			$htmlContent = '<h1>Klik Link di bawah ini untuk reset password akun anda , abaikan jika ini bukan permintaan anda</h1>';
			$htmlContent .= '<a href='.base_url().'register/reset_password/'.$cek_email->id_user.'>Reset Password</a>';
			$ci = get_instance();
			$ci->load->library('email');
			$config['protocol'] = "smtp";
			$config['smtp_host'] = "ssl://mail.plug-in.id";
			$config['smtp_port'] = "465";
			$config['smtp_user'] = "catering@plug-in.id";
			$config['smtp_pass'] = "C_atering12345";
			$config['charset'] = "utf-8";
			$config['mailtype'] = "html";
			$config['newline'] = "\r\n";
			$config['crlf'] = "\r\n";
			$ci->email->initialize($config);
			$ci->email->from('catering@plug-in.id', 'Reset Password');
			$list = array($this->input->post('email'));
			$ci->email->to($list);
			$ci->email->subject('Reset Password');


			$ci->email->message($htmlContent);
			if ($this->email->send()) {
				$this->session->set_flashdata('alert','berhasil');
				redirect('register/reset_pass');
			} else {
				show_error($this->email->print_debugger());
			}

			
		}
	}


	public function reset_password($id)
	{
		$data['id'] = $id;
		$this->load->view('reset_password',$data);
	}

	public function update_password()
	{
		$id = $this->input->post('id');
		$data['password'] = md5($this->input->post('password'));
		$this->MCatering->update_data('user',$id,'id_user',$data);
		redirect('login'); 
	}

	public function register(){
		


		$data['nama'] = $_POST['nama'];
		$data['no_hp'] = $_POST['nomor'];
		$data['email'] = $_POST['email'];
		$data['jenis_user'] = '1';
		$data['password'] = md5($_POST['password']);

		$cek_email = $this->MCatering->cek_id($data['email']);
		echo json_encode($cek_email);
		if (empty($cek_email)) {
			$tabel = 'user';

			$this->MCatering->tambah_data($tabel,$data);
			$regis = $this->MCatering->cek_id($data['email']);
			$this->send($regis->id_user,$data['email']);
			$this->session->set_flashdata('alert','berhasil');
			redirect('register');

		}else{
			$this->session->set_flashdata('alert','gagal');
			redirect('register');
		}






	}



	public function send($id,$email){
		$htmlContent = '<h1>Klik Link di bawah ini untuk memverifikasi akun anda</h1>';
		$htmlContent .= '<a href='.base_url().'register/verifikasi/'.$id.'>Verifikasi</a>';
		$ci = get_instance();
		$ci->load->library('email');
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "ssl://mail.plug-in.id";
		$config['smtp_port'] = "465";
		$config['smtp_user'] = "catering@plug-in.id";
		$config['smtp_pass'] = "C_atering12345";
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		$config['crlf'] = "\r\n";
		$ci->email->initialize($config);
		$ci->email->from('catering@plug-in.id', 'Verif Catering');
		$list = array($email);
		$ci->email->to($list);
		$ci->email->subject('Verifikasi Catering');


		$ci->email->message($htmlContent);
		if ($this->email->send()) {
			echo 'Email sent.';
		} else {
			show_error($this->email->print_debugger());
		}
	}

	public function verifikasi($id){
		$this->MCatering->verif('user',$id,'id_user');
		redirect('login');
	}
}