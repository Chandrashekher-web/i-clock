<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Attendance extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->library("Upload");
            $this->load->model('admin/Reader_model');
            $this->load->library('commonlibrary');
        }

        public function import_employee_attendance()
        {
            $site_id = get_session_site_id();
            $this->form_validation->set_rules('reader', 'Reader', 'required|trim');
            $data = array();
            if ($this->form_validation->run() == true)
            {
                $reader_id = $this->input->post('reader');
                $fname = time().".dat";
                $attendance_config = get_custom_config_item('employee_attendance');

                if (!is_dir($attendance_config['upload_path']))
                {
                    mkdir('assets/uploads/'.$attendance_config['folder_name'], 0777, TRUE);
                }

                $file = $attendance_config['upload_path'].$fname;

                if (!empty($_FILES['import_employee_attendance']))
                {
                    $attendance_config['file_name'] = $fname;
                    if ($this->commonlibrary->is_file_uploaded('import_employee_attendance'))
                    {
                        $uploaddata = $this->upload->upload_file("import_employee_attendance", $attendance_config['upload_path'], $attendance_config);

                        if (($handle = fopen($file, "r")) !== FALSE)
                        {
                            while (($data = fgets($handle)) !== FALSE)
                            {
                                $arr_attendance_record = explode("\t", "$data");

                                if (!empty($arr_attendance_record) && isset($arr_attendance_record[1]))
                                {
                                    $employee_pin = trim($arr_attendance_record[0]);
                                    if (validateDate(trim($arr_attendance_record[1])) === TRUE)
                                    {
                                        $att_datetime = trim($arr_attendance_record[1]);
                                        $work = "";
                                        if (isset($arr_attendance_record[5]))
                                        {
                                            $work = trim($arr_attendance_record[5]);
                                        }
                                        $status = isset($arr_attendance_record[3]) ? trim($arr_attendance_record[3]) : "";
                                        $verify_mode = isset($arr_attendance_record[4]) ? trim($arr_attendance_record[4]) : 0;

                                        $employee_id = 0;
                                        $employee_data = $this->iclock_model->get_employee_by_pin($employee_pin, $site_id);

                                        if (!empty($employee_data))
                                        {
                                            $employee_id = $employee_data[0]['employee_id'];
                                        }
                                        $dataValues = array(
                                            'employee_id' => $employee_id,
                                            'employee_pin' => $employee_pin,
                                            'clock' => $att_datetime,
                                            'work' => $work,
                                            'mode' => $verify_mode,
                                            'status' => $status,
                                            'reader_id' => $reader_id
                                        );

                                        //check attendance exist on same time
                                        $att_count = $this->iclock_model->check_attendance_already_exist($dataValues);

                                        if ($att_count == 0)
                                        {
                                            $this->iclock_model->save_attendance($dataValues);
                                        }
                                    }
                                }
                            }
                            fclose($handle);
                        }
                        $data['message'] = "Employee Attendance Imported Successfully!";
                        $this->session->set_flashdata('product_operation_message', 'Employee Attendance Imported Successfully!');
                    }
                    else
                    {
                        $data['message'] = $this->upload->display_errors();
                        $this->session->set_flashdata('product_operation_message', 'Error occured!');
                    }
                }
            }
           
            $arr_readers = $this->Reader_model->get_readers_by_site_id($site_id);
            $data['arr_reader'] = add_blank_option($arr_readers, "-- Select Reader --");
            $data['form_caption'] = "Import Employee Attendance";
            $data['form_action'] = current_url();
            $this->load->view('import-employees-attendance', $data);
        }

    }
    