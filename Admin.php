<?php

/**
 *
 */
class Admin extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $this->load->helper('url');
    $this->load->Model('Model_data_survey');
    $this->load->Model('Model_admin');
    //model

    $this->load->model('User_model');


    $this->load->library(array('session'));
    $this->load->helper(array('url'));
    $this->load->model('user_model');
  }

  public function index()
  {
    		// create the data object
    		$data = new stdClass();

        if ($this->is_logged_in('logged_in') == 1) {
          // user login ok

          $data = array();
          $data['cobaa'] = $this->user_model->getRoleName($this->is_logged_in('is_role_user'));
          $this->template->display('welcome_message',$data);

        }else {


            redirect(base_url().'index.php/admin/login');

        }

  }

  public function is_logged_in($values)
  {
          $user = $this->session->userdata($values);
          return isset($user);
  }



  public function data_survey_siswa()
  {
    if($this->session->userdata('is_role_user') == 2222){
         $this->load->library('pagination');
            $config['base_url'] = base_url() . 'index.php/Admin/data_survey_siswa/';
            $config['total_rows'] = $this->Model_data_survey->get_data_siswa()->num_rows();
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $data['paging'] = $this->pagination->create_links();

            $hal = $this->uri->segment(3);
            $hal = $hal==""?0:$hal;

            $data['record'] = $this->Model_data_survey->get_data_siswa_paging($hal, $config['per_page'] = 10)->result();
        $this->template->display('template/data_survey/siswa', $data);
    }

    else {
      redirect(base_url().'index.php/admin/login');
    }

  }

  public function data_survey_fasilitator()
  {
    if($this->session->userdata('is_role_user') == 2222){
            $this->load->library('pagination');
            $config['base_url'] = base_url() . 'index.php/Admin/data_survey_fasilitator/';
            $config['total_rows'] = $this->Model_data_survey->get_data_fasilitator()->num_rows();
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $data['paging'] = $this->pagination->create_links();

            $hal = $this->uri->segment(3);
            $hal = $hal==""?0:$hal;

            $data['record'] = $this->Model_data_survey->get_data_fasilitator_paging($hal, $config['per_page'] = 10)->result();
        $this->template->display('template/data_survey/fasilitator', $data);
    }

    else {
      redirect(base_url().'index.php/admin/login');
    }

  }



  public function login_page()
  {

      $this->load->view('template/login',null);
  }



  public function add()
  {
    $this->template->display('template/add_mahasiswa',null);
  }

  public function detail($value='')
  {
    //echo "rizki aja".$value;
    $data = array();
    $data['detail'] = $this->Mahasiswa_model->getDetailMahasiswa($value);

    echo json_encode($data);
  }

  public function store()
  {
    $getNPM = $this->input->post('npm');
    $getNamaMhs = $this->input->post('nm_mhs');
    $getKelasMhs = $this->input->post('kelas');
    $getSemester = $this->input->post('semester');
    $getPeminatan = $this->input->post('peminatan');

    $data = $this->Mahasiswa_model->insertMahasiswa($getNPM,$getNamaMhs,$getKelasMhs,$getSemester,$getPeminatan);

    if ($data== true) {
      $this->session->set_flashdata('flsh_msg', 'data berhasil ditambahkan');
      redirect('admin/mahasiswa');
    }

  }

  /**
   * login function.
   *
   * @access public
   * @return void
   */
  public function login() {

    // create the data object
    $data = new stdClass();

    // load form helper and validation library
    $this->load->helper('form');
    $this->load->library('form_validation');

    // set validation rules
    $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
    $this->form_validation->set_rules('password', 'Password', 'required');

    if ($this->form_validation->run() == false) {

      // validation not ok, send validation errors to the view

      if ($this->is_logged_in('logged_in') == 1) {
        // user login ok

          $this->template->display('welcome_message');

      }else {

          $this->load->view('template/login');

      }


    } else {

      // set variables from the form
      $username = $this->input->post('username');
      $password = $this->input->post('password');

      if ($this->user_model->resolve_user_login($username, $password)) {

        $user_id = $this->user_model->get_user_id_from_username($username);

        $user = $this->user_model->get_user($user_id);

        //$user = $this->User_model->getRoleName();


        // set session user datas
        $_SESSION['user_id']      = (int)$user->id;
        $_SESSION['username']     = (string)$user->username;
        $_SESSION['logged_in']    = (bool)true;
        $_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
        $_SESSION['is_admin']     = (bool)$user->is_admin;
        $_SESSION['is_role_user'] = (int)$user->role_id;

        $user = $this->user_model->get_role_name($this->session->userdata('is_role_user'));
        $_SESSION['is_role_name'] = (string)$user->nm_role_user;


        //$_SESSION['is_role_name'] = (string)$user->role_name;

        // user login ok

        $coba['waw'] = $user;

        redirect('/admin');



      } else {

        // login failed
        $data->error = 'Wrong username or password.';

        // send error to the view

        $this->load->view('template/login', $data);


      }

    }

  }

  /**
   * logout function.
   *
   * @access public
   * @return void
   */
  public function logout() {

    // create the data object
    $data = new stdClass();

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

      // remove session datas
      foreach ($_SESSION as $key => $value) {
        unset($_SESSION[$key]);
      }

      // user logout ok

      $this->load->view('template/user/logout/logout_success', $data);

      redirect('admin');

    } else {

      // there user was not logged in, we cannot logged him out,
      // redirect him to site root
      redirect('/');

    }

  }

public function excel_fasilitator()
        {
            //load librarynya terlebih dahulu
            //jika digunakan terus menerus lebih baik load ini ditaruh di auto load
            $this->load->library("excel1/PHPExcel");

            //membuat objek PHPExcel
            $objPHPExcel = new PHPExcel();

            //mengatur lebar pertanyaan

$a = range('A', 'Z');
for($x=0; $x<count($a); $x++){
    $objPHPExcel->getActiveSheet()->getColumnDimension("$a[$x]")->setWidth(20);
}
for($x=0; $x<count($a); $x++){
    $objPHPExcel->getActiveSheet()->getColumnDimension('A'. $a[$x])->setWidth(20);
}

            //Memberi judul
            $SI = $objPHPExcel->setActiveSheetIndex(0);
                    //mengisikan value pada tiap-tiap cell, A1 itu alamat cellnya
                    //Hello merupakan isinya
                                        $SI->setCellValue('A1', 'Data Survey Fasilitator');
                                        $SI->setCellValue('A3', 'Waktu');
                                        $SI->setCellValue('B3', 'Nama Fasilitator');
                                        $SI->setCellValue('C3', 'Asal Daerah Provinsi');
                                        $SI->setCellValue('D3', 'Asal Daerah Kota/Kabupaten');
                                        $SI->setCellValue('E3', 'Object Sekolah');
                                        $SI->setCellValue('F3', 'Alamat Sekolah');
                                        $SI->setCellValue('G3', 'Profil Biodata');
                                        $SI->setCellValue('H3', 'Signature');
                                        $SI->setCellValue('I3', 'Jawaban 1 A');
                                        $SI->setCellValue('J3', 'Jawaban 1 B');
                                        $SI->setCellValue('K3', 'Jawaban 1 C');
                                        $SI->setCellValue('L3', 'Jawaban 1 D');
                                        $range = range('M', 'Z');

                                        for ($x=0; $x<count($range); $x++) {
                                        	$count = $x+2;
                                            $SI->setCellValue("$range[$x]3", "Jawaban $count");
                                        }

                                        $SI->setCellValue('AA3', 'Jawaban 16');
                                        $SI->setCellValue('AB3', 'Jawaban 17');
                                        $SI->setCellValue('AC3', 'Jawaban 18');
                                         $SI->setCellValue('AD3', 'Jawaban 19');
                                        $SI->setCellValue('AE3', 'Jawaban 20');

$headerStylenya = new PHPExcel_Style();
$bodyStylenya   = new PHPExcel_Style();

$headerStylenya->applyFromArray(
  array('fill'  => array(
      'type'    => PHPExcel_Style_Fill::FILL_SOLID,
      'color'   => array('argb' => 'FFEEEEEE')),
      'borders' => array('bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'left'      => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)
      )
  ));

$bodyStylenya->applyFromArray(
  array('fill'  => array(
      'type'  => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array('argb' => 'FFFFFFFF')),
      'borders' => array(
            'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'left'      => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)
      )
    ));

//Menggunakan HeaderStylenya
$a = range('A', 'Z');
  for($x=0; $x<count($a); $x++){
      $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "$a[$x]3");
  }

    $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "AA3");
    $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "AB3");
    $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "AC3");
    $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "AD3");
    $objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "AE3");

            //set title pada sheet (me rename nama sheet)
            $objPHPExcel->getActiveSheet()->setTitle('Excel Pertama');

            //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $sql = "SELECT * FROM list_question_fasilitator ORDER BY id DESC";
            $data = $this->db->query($sql)->result();
            $baris = 4;
            foreach ($data as $key => $value) {
                $SI->setCellValue('A' . $baris , $value->waktu);
                $SI->setCellValue('B' . $baris , $value->nm_fasilitator);
                $SI->setCellValue('C' . $baris , $value->asal_daerah_provinsi);
                $SI->setCellValue('D' . $baris , $value->asal_daerah_kotakab);
                $SI->setCellValue('E' . $baris , $value->obj_sekolah);
                $SI->setCellValue('F' . $baris , $value->alamat_sekolah);
                $SI->setCellValue('G' . $baris , $value->profile_biodata);
                $SI->setCellValue('H' . $baris , $value->signature);

                //Array
                $jawaban1a = array('Siomay' => 1, 'Soto' => 2, 'Bakso' => 3, 'Batagor' => 4, 'Gado-gado' => 5);
                $jawaban1b = array('Gorengan' => 1, 'Lemper' => 2, 'Kue Lapis' => 3, 'Donat' => 4, 'Biskuit' => 5, 'Keripik' => 6);
                $jawaban1c = array('Es Buah' => 1, 'Es Campur' => 2, 'Minuman Soda' => 3, 'Teh' => 4, 'Sari Buah' => 5);
                $jawaban1d = array('Rujak' => 1, 'Buah Potong' => 2);
                $jawaban3 = array('Rambut' => 1, 'Kuku' => 2, 'Stapler' => 3, 'Batu' => 4, 'Kaki Binatang' => 5);
                $jawaban2 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban4 = array('Memakai celemek' => 1, 'Memakai topi/penutup kepala' => 2, 'Berambut gondrong' => 3, 'Memakai Perhiasan di tangannya' => 4, 'Terdapat luka di jarinya' => 5);
                $jawaban5 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban6 = array('Penjepit' => 1, 'Sendok' => 2, 'Stapler' => 3, 'Garpu' => 4);
                $jawaban7 = array('Kantin sekolah' => 1, 'Pedagang disekitar sekolah' => 2);
                $jawaban8 = array('Bersih' => 1, 'Kurang bersih' => 2, 'Kotor' => 3);
                $jawaban9 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban10 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban11 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban12 = array('Tertutup' => 1, 'Terbuka' => 2);
                $jawaban13 = array('Kertas koran' => 1, 'Kantong pelastik' => 2, 'Styrofoam' => 3, 'Kertas bekas' => 4, 'Kertas putih polos' => 5, 'Kertas nasi (berwarna coklat)' => 6);
                $jawaban14 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban15 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban16 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban17 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban18 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban19 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban20 = array('Tidak pernah' => 1, 'Tidak tahu' => 2, 'Pernah pada tahun ini' => 3, 'Pernah pada tahun lalu' => 4, 'Pernah lebih pada dua tahun yang lalu' => 5);

                foreach ($jawaban1a as $data => $datas) {
                    if(!empty($value->pilihan1a)) {
                        if($value->pilihan1a === $data) {
                            $SI->setCellValue('I' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1a)) {
                            $SI->setCellValue('I' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('I' . $baris , 0);
                    }
                }

                foreach ($jawaban1b as $data => $datas) {
                    if(!empty($value->pilihan1b)) {
                        if($value->pilihan1b === $data) {
                            $SI->setCellValue('J' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1b)) {
                            $SI->setCellValue('J' . $baris , 7);
                        }
                    }
                    else {
                        $SI->setCellValue('J' . $baris , 0);
                    }
                }

                foreach ($jawaban1c as $data => $datas) {
                    if(!empty($value->pilihan1c)) {
                        if($value->pilihan1c === $data) {
                            $SI->setCellValue('K' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1c)) {
                            $SI->setCellValue('K' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('K' . $baris , 0);
                    }
                }

                foreach ($jawaban1d as $data => $datas) {
                    if(!empty($value->pilihan1d)) {
                        if($value->pilihan1d === $data) {
                            $SI->setCellValue('L' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1d)) {
                            $SI->setCellValue('L' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('L' . $baris , 0);
                    }
                }

                foreach ($jawaban2 as $data => $datas) {
                    if(!empty($value->pilihan2)) {
                        if($value->pilihan2 === $data) {
                            $SI->setCellValue('M' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan2)) {
                            $SI->setCellValue('M' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('M' . $baris , 0);
                    }
                }

                foreach ($jawaban3 as $data => $datas) {
                    if(!empty($value->pilihan3)) {
                        if($value->pilihan3 === $data) {
                            $SI->setCellValue('N' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan3)) {
                            $SI->setCellValue('N' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('N' . $baris , 0);
                    }
                }

                foreach ($jawaban4 as $data => $datas) {
                    if(!empty($value->pilihan4)) {
                        if($value->pilihan4 === $data) {
                            $SI->setCellValue('O' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan4)) {
                            $SI->setCellValue('O' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('O' . $baris , 0);
                    }
                }

                foreach ($jawaban5 as $data => $datas) {
                    if(!empty($value->pilihan5)) {
                        if($value->pilihan5 === $data) {
                            $SI->setCellValue('P' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan5)) {
                            $SI->setCellValue('P' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('P' . $baris , 0);
                    }
                }

                foreach ($jawaban6 as $data => $datas) {
                    if(!empty($value->pilihan6)) {
                        if($value->pilihan6 === $data) {
                            $SI->setCellValue('Q' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan6)) {
                            $SI->setCellValue('Q' . $baris , 5);
                        }
                    }
                    else {
                        $SI->setCellValue('Q' . $baris , 0);
                    }
                }

                foreach ($jawaban7 as $data => $datas) {
                    if(!empty($value->pilihan7)) {
                        if($value->pilihan7 === $data) {
                            $SI->setCellValue('R' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan7)) {
                            $SI->setCellValue('R' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('R' . $baris , 0);
                    }
                }

                foreach ($jawaban8 as $data => $datas) {
                    if(!empty($value->pilihan8)) {
                        if($value->pilihan8 === $data) {
                            $SI->setCellValue('S' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan8)) {
                            $SI->setCellValue('S' . $baris , 4);
                        }
                    }
                    else {
                        $SI->setCellValue('S' . $baris , 0);
                    }
                }

                foreach ($jawaban9 as $data => $datas) {
                    if(!empty($value->pilihan9)) {
                        if($value->pilihan9 === $data) {
                            $SI->setCellValue('T' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan9)) {
                            $SI->setCellValue('T' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('T' . $baris , 0);
                    }
                }

                foreach ($jawaban10 as $data => $datas) {
                    if(!empty($value->pilihan10)) {
                        if($value->pilihan10 === $data) {
                            $SI->setCellValue('U' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan10)) {
                            $SI->setCellValue('U' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('U' . $baris , 0);
                    }
                }

                foreach ($jawaban11 as $data => $datas) {
                    if(!empty($value->pilihan11)) {
                        if($value->pilihan11 === $data) {
                            $SI->setCellValue('V' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan11)) {
                            $SI->setCellValue('V' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('V' . $baris , 0);
                    }
                }

                foreach ($jawaban12 as $data => $datas) {
                    if(!empty($value->pilihan12)) {
                        if($value->pilihan12 === $data) {
                            $SI->setCellValue('W' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan12)) {
                            $SI->setCellValue('W' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('W' . $baris , 0);
                    }
                }

                foreach ($jawaban13 as $data => $datas) {
                    if(!empty($value->pilihan13)) {
                        if($value->pilihan13 === $data) {
                            $SI->setCellValue('X' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan13)) {
                            $SI->setCellValue('X' . $baris , 7);
                        }
                    }
                    else {
                        $SI->setCellValue('X' . $baris , 0);
                    }
                }

                foreach ($jawaban14 as $data => $datas) {
                    if(!empty($value->pilihan14)) {
                        if($value->pilihan14 === $data) {
                            $SI->setCellValue('Y' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan14)) {
                            $SI->setCellValue('Y' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('Y' . $baris , 0);
                    }
                }

                foreach ($jawaban15 as $data => $datas) {
                    if(!empty($value->pilihan15)) {
                        if($value->pilihan15 === $data) {
                            $SI->setCellValue('Z' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan15)) {
                            $SI->setCellValue('Z' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('Z' . $baris , 0);
                    }
                }

                foreach ($jawaban16 as $data => $datas) {
                    if(!empty($value->pilihan16)) {
                        if($value->pilihan16 === $data) {
                            $SI->setCellValue('AA' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan16)) {
                            $SI->setCellValue('AA' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('AA' . $baris , 0);
                    }
                }

                foreach ($jawaban17 as $data => $datas) {
                    if(!empty($value->pilihan17)) {
                        if($value->pilihan17 === $data) {
                            $SI->setCellValue('AB' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan17)) {
                            $SI->setCellValue('AB' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('AB' . $baris , 0);
                    }
                }

                foreach ($jawaban18 as $data => $datas) {
                    if(!empty($value->pilihan18)) {
                        if($value->pilihan18 === $data) {
                            $SI->setCellValue('AC' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan18)) {
                            $SI->setCellValue('AC' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('AC' . $baris , 0);
                    }
                }

                foreach ($jawaban19 as $data => $datas) {
                    if(!empty($value->pilihan19)) {
                        if($value->pilihan19 === $data) {
                            $SI->setCellValue('AD' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan19)) {
                            $SI->setCellValue('AD' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('AD' . $baris , 0);
                    }
                }

                foreach ($jawaban20 as $data => $datas) {
                    if(!empty($value->pilihan20)) {
                        if($value->pilihan20 === $data) {
                            $SI->setCellValue('AE' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan20)) {
                            $SI->setCellValue('AE' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('AE' . $baris , 0);
                    }
                }

                $baris++;
            }

             $baris = $baris-1;
            $objPHPExcel->getActiveSheet()->setSharedStyle($bodyStylenya, "A4:AE$baris");
            //sesuaikan headernya
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //ubah nama file saat diunduh
            header('Content-Disposition: attachment;filename="data_fasilitator.xlsx"');
            //unduh file
            $objWriter->save("php://output");

            //Mulai dari create object PHPExcel itu ada dokumentasi lengkapnya di PHPExcel,
            // Folder Documentation dan Example
            // untuk belajar lebih jauh mengenai PHPExcel silakan buka disitu

 }

           public function excel_siswa()
{
            //load librarynya terlebih dahulu
            //jika digunakan terus menerus lebih baik load ini ditaruh di auto load
            $this->load->library("excel1/PHPExcel");

            //membuat objek PHPExcel
            $objPHPExcel = new PHPExcel();

  $a = range('A', 'Z');
  for($x=0; $x<count($a); $x++){
      $objPHPExcel->getActiveSheet()->getColumnDimension("$a[$x]")->setWidth(20);
  }
  for($x=0; $x<count($a); $x++){
      $objPHPExcel->getActiveSheet()->getColumnDimension('A'. $a[$x])->setWidth(20);
  }

$objPHPExcel->getActiveSheet()->getStyle('A3:AB3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A3:AB3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A3:AB3')->getFill()->getStartColor()->setARGB('FF808080');
            $SI = $objPHPExcel->setActiveSheetIndex(0);
                    //mengisikan value pada tiap-tiap cell, A1 itu alamat cellnya
                    //Hello merupakan isinya
                                        $SI->setCellValue('A1', 'Data Survey Siswa');
                                        $SI->setCellValue('A3', 'Waktu');
                                        $SI->setCellValue('B3', 'Nama Siswa');
                                        $SI->setCellValue('C3', 'Kelas');
                                        $SI->setCellValue('D3', 'Sekolah');
                                        $SI->setCellValue('E3', 'Alamat Sekolah');
                                        $SI->setCellValue('F3', 'Jawaban 1 A');
                                        $SI->setCellValue('G3', 'Jawaban 1 C');
                                        $SI->setCellValue('H3', 'Jawaban 1 B');
                                        $SI->setCellValue('I3', 'Jawaban 1 D');

                                        $range = range('J', 'Z');

                                        for ($x=0; $x<count($range); $x++) {
                                        	$count = $x+2;
                                            $SI->setCellValue("$range[$x]3", "Jawaban $count");
                                        }

                                        $SI->setCellValue('AA3', 'Jawaban 19');
                                        $SI->setCellValue('AB3', 'Jawaban 20');

$headerStylenya = new PHPExcel_Style();
$bodyStylenya   = new PHPExcel_Style();

$headerStylenya->applyFromArray(
  array('fill'  => array(
      'type'    => PHPExcel_Style_Fill::FILL_SOLID,
      'color'   => array('argb' => 'FFEEEEEE')),
      'borders' => array('bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'left'      => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)
      )
  ));

$bodyStylenya->applyFromArray(
  array('fill'  => array(
      'type'  => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array('argb' => 'FFFFFFFF')),
      'borders' => array(
            'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'left'      => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)
      )
    ));

$objPHPExcel->getActiveSheet()->setSharedStyle($headerStylenya, "A3:AB3");

            //set title pada sheet (me rename nama sheet)
            $objPHPExcel->getActiveSheet()->setTitle('Excel Pertama');

            //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

           $sql = "SELECT * FROM list_question_siswa ORDER BY id DESC";
            $data = $this->db->query($sql)->result();
            $baris = 4;
            foreach ($data as $key => $value) {
                $SI->setCellValue('A' . $baris , $value->waktu);
                $SI->setCellValue('B' . $baris , $value->nm_siswa);
                $SI->setCellValue('C' . $baris , $value->kelas);
                $SI->setCellValue('D' . $baris , $value->sekolah);
                $SI->setCellValue('E' . $baris , $value->alamat_sekolah);

                $jawaban1a = array('Siomay' => 1, 'Soto' => 2, 'Bakso' => 3, 'Batagor' => 4, 'Gado-gado' => 5);
                $jawaban1b = array('Gorengan' => 1, 'Lemper' => 2, 'Kue Lapis' => 3, 'Donat' => 4, 'Biskuit' => 5, 'Keripik' => 6);
                $jawaban1c = array('Es Buah' => 1, 'Es Campur' => 2, 'Minuman Soda' => 3, 'Teh' => 4, 'Sari Buah' => 5);
                $jawaban1d = array('Rujak' => 1, 'Buah Potong' => 2);
                $jawaban3 = array('Rambut' => 1, 'Kuku' => 2, 'Stapler' => 3, 'Batu' => 4, 'Kaki Binatang' => 5);
                $jawaban2 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban4 = array('Memakai celemek' => 1, 'Memakai topi/tutup kepala' => 2, 'Berambut gondrong' => 3, 'Memakai Perhiasan di tangannya' => 4, 'Terdapat luka di jarinya' => 5);
                $jawaban5 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban6 = array('Penjepit' => 1, 'Sendok' => 2, 'Stapler' => 3, 'Garpu' => 4);
                $jawaban7 = array('Kantin sekolah' => 1, 'Pedagang disekitar sekolah' => 2);
                $jawaban8 = array('Bersih' => 1, 'Kurang bersih' => 2, 'Kotor' => 3);
                $jawaban9 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban10 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban11 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban12 = array('Tertutup' => 1, 'Terbuka' => 2);
                $jawaban13 = array('Kertas koran' => 1, 'Kantong pelastik' => 2, 'Styrofoam' => 3, 'Kertas bekas' => 4, 'Kertas putih polos' => 5, 'Kertas nasi (berwarna coklat)' => 6);
                $jawaban14 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban15 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban16 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban17 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban18 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban19 = array('Ya' => 1, 'Tidak' => 2);
                $jawaban20 = array('Tidak pernah' => 1, 'Tidak tahu' => 2, 'Pernah pada tahun ini' => 3, 'Pernah pada tahun lalu' => 4, 'Pernah lebih pada dua tahun yang lalu' => 5);

                foreach ($jawaban1a as $data => $datas) {
                    if(!empty($value->pilihan1a)) {
                        if($value->pilihan1a === $data) {
                            $SI->setCellValue('F' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1a)) {
                            $SI->setCellValue('F' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('F' . $baris , 0);
                    }
                }

                foreach ($jawaban1b as $data => $datas) {
                    if(!empty($value->pilihan1b)) {
                        if($value->pilihan1b === $data) {
                            $SI->setCellValue('G' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1b)) {
                            $SI->setCellValue('G' . $baris , 7);
                        }
                    }
                    else {
                        $SI->setCellValue('G' . $baris , 0);
                    }
                }

                foreach ($jawaban1c as $data => $datas) {
                    if(!empty($value->pilihan1c)) {
                        if($value->pilihan1c === $data) {
                            $SI->setCellValue('H' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1c)) {
                            $SI->setCellValue('H' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('H' . $baris , 0);
                    }
                }

                foreach ($jawaban1d as $data => $datas) {
                    if(!empty($value->pilihan1d)) {
                        if($value->pilihan1d === $data) {
                            $SI->setCellValue('I' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan1d)) {
                            $SI->setCellValue('I' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('I' . $baris , 0);
                    }
                }

                foreach ($jawaban2 as $data => $datas) {
                    if(!empty($value->pilihan2)) {
                        if($value->pilihan2 === $data) {
                            $SI->setCellValue('J' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan2)) {
                            $SI->setCellValue('J' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('J' . $baris , 0);
                    }
                }

                foreach ($jawaban3 as $data => $datas) {
                    if(!empty($value->pilihan3)) {
                        if($value->pilihan3 === $data) {
                            $SI->setCellValue('K' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan3)) {
                            $SI->setCellValue('K' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('K' . $baris , 0);
                    }
                }

                foreach ($jawaban4 as $data => $datas) {
                    if(!empty($value->pilihan4)) {
                        if($value->pilihan4 === $data) {
                            $SI->setCellValue('L' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan4)) {
                            $SI->setCellValue('L' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('L' . $baris , 0);
                    }
                }

                foreach ($jawaban5 as $data => $datas) {
                    if(!empty($value->pilihan5)) {
                        if($value->pilihan5 === $data) {
                            $SI->setCellValue('M' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan5)) {
                            $SI->setCellValue('M' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('M' . $baris , 0);
                    }
                }

                foreach ($jawaban6 as $data => $datas) {
                    if(!empty($value->pilihan6)) {
                        if($value->pilihan6 === $data) {
                            $SI->setCellValue('N' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan6)) {
                            $SI->setCellValue('N' . $baris , 5);
                        }
                    }
                    else {
                        $SI->setCellValue('N' . $baris , 0);
                    }
                }

                foreach ($jawaban7 as $data => $datas) {
                    if(!empty($value->pilihan7)) {
                        if($value->pilihan7 === $data) {
                            $SI->setCellValue('O' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan7)) {
                            $SI->setCellValue('O' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('O' . $baris , 0);
                    }
                }

                foreach ($jawaban8 as $data => $datas) {
                    if(!empty($value->pilihan8)) {
                        if($value->pilihan8 === $data) {
                            $SI->setCellValue('P' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan8)) {
                            $SI->setCellValue('P' . $baris , 4);
                        }
                    }
                    else {
                        $SI->setCellValue('P' . $baris , 0);
                    }
                }

                foreach ($jawaban9 as $data => $datas) {
                    if(!empty($value->pilihan9)) {
                        if($value->pilihan9 === $data) {
                            $SI->setCellValue('Q' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan9)) {
                            $SI->setCellValue('Q' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('Q' . $baris , 0);
                    }
                }

                foreach ($jawaban10 as $data => $datas) {
                    if(!empty($value->pilihan10)) {
                        if($value->pilihan10 === $data) {
                            $SI->setCellValue('R' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan10)) {
                            $SI->setCellValue('R' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('R' . $baris , 0);
                    }
                }

                foreach ($jawaban11 as $data => $datas) {
                    if(!empty($value->pilihan11)) {
                        if($value->pilihan11 === $data) {
                            $SI->setCellValue('S' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan11)) {
                            $SI->setCellValue('S' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('S' . $baris , 0);
                    }
                }

                foreach ($jawaban12 as $data => $datas) {
                    if(!empty($value->pilihan12)) {
                        if($value->pilihan12 === $data) {
                            $SI->setCellValue('T' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan12)) {
                            $SI->setCellValue('T' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('T' . $baris , 0);
                    }
                }

                foreach ($jawaban13 as $data => $datas) {
                    if(!empty($value->pilihan13)) {
                        if($value->pilihan13 === $data) {
                            $SI->setCellValue('U' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan13)) {
                            $SI->setCellValue('U' . $baris , 7);
                        }
                    }
                    else {
                        $SI->setCellValue('U' . $baris , 0);
                    }
                }

                foreach ($jawaban14 as $data => $datas) {
                    if(!empty($value->pilihan14)) {
                        if($value->pilihan14 === $data) {
                            $SI->setCellValue('V' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan14)) {
                            $SI->setCellValue('V' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('V' . $baris , 0);
                    }
                }

                foreach ($jawaban15 as $data => $datas) {
                    if(!empty($value->pilihan15)) {
                        if($value->pilihan15 === $data) {
                            $SI->setCellValue('W' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan15)) {
                            $SI->setCellValue('W' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('W' . $baris , 0);
                    }
                }

                foreach ($jawaban16 as $data => $datas) {
                    if(!empty($value->pilihan16)) {
                        if($value->pilihan16 === $data) {
                            $SI->setCellValue('X' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan16)) {
                            $SI->setCellValue('X' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('X' . $baris , 0);
                    }
                }

                foreach ($jawaban17 as $data => $datas) {
                    if(!empty($value->pilihan17)) {
                        if($value->pilihan17 === $data) {
                            $SI->setCellValue('Y' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan17)) {
                            $SI->setCellValue('Y' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('Y' . $baris , 0);
                    }
                }

                foreach ($jawaban18 as $data => $datas) {
                    if(!empty($value->pilihan18)) {
                        if($value->pilihan18 === $data) {
                            $SI->setCellValue('Z' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan18)) {
                            $SI->setCellValue('Z' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('Z' . $baris , 0);
                    }
                }

                foreach ($jawaban19 as $data => $datas) {
                    if(!empty($value->pilihan19)) {
                        if($value->pilihan19 === $data) {
                            $SI->setCellValue('AA' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan19)) {
                            $SI->setCellValue('AA' . $baris , 3);
                        }
                    }
                    else {
                        $SI->setCellValue('AA' . $baris , 0);
                    }
                }

                foreach ($jawaban20 as $data => $datas) {
                    if(!empty($value->pilihan20)) {
                        if($value->pilihan20 === $data) {
                            $SI->setCellValue('AB' . $baris , $datas);
                        }
                        else if(preg_match_all('/lainnya/', $value->pilihan20)) {
                            $SI->setCellValue('AB' . $baris , 6);
                        }
                    }
                    else {
                        $SI->setCellValue('AB' . $baris , 0);
                    }
                }

                $baris++;
            }
            $baris = $baris-1;
            $objPHPExcel->getActiveSheet()->setSharedStyle($bodyStylenya, "A4:AB$baris");


            //sesuaikan headernya
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //ubah nama file saat diunduh
            header('Content-Disposition: attachment;filename="data_siswa.xlsx"');
            //unduh file
            $objWriter->save("php://output");

            //Mulai dari create object PHPExcel itu ada dokumentasi lengkapnya di PHPExcel,
            // Folder Documentation dan Example
            // untuk belajar lebih jauh mengenai PHPExcel silakan buka disitu

        }

        function pertanyaan7() {
                $pilihan1 = array('Kantin sekolah', 'Pedagang disekitar sekolah');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan7($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }

        function pertanyaan8() {
                $pilihan1 = array('Bersih', 'Kurang bersih', 'Kotor');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan8($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }

        function pertanyaan5() {
                $pilihan1 = array('ya', 'tidak');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan5($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }

        function pertanyaan18() {
              $pilihan1 = array('ya', 'tidak');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan18($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }

        function pertanyaan19() {
                $pilihan1 = array('ya', 'tidak');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan19($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }

        function pertanyaan20() {
              $pilihan1 = array('tidak pernah', 'pernah pada tahun ini', 'tidak tahu', 'pernah pada tahun yang lalu', 'pernah lebih dua tahun yang lalu');
                $array = array();

                foreach ($pilihan1 as $key => $value) {
                         $data = $this->Model_admin->count_pertanyaan20($value)->row_array();
                         $hasil[0] = $value;
                         $hasil[1] = $data['total'];
                         array_push($array, $hasil);
                }
                print json_encode($array, JSON_NUMERIC_CHECK);
        }
}
