<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Backup extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Backup_model');
        }

        public function index()
        {
            $data['form_caption'] = "Backup Employee & Reader"; 
            $data['form_action'] = current_url();
            $message = $this->session->flashdata('employe_status_update_operation_message');
            $dataArray['message'] = $message;
            if (!empty($this->input->post('mode')))
            {
                if ($this->input->post('mode') == 'backup')
                {
                    $this->Backup_model->downloadbackup();
                    $data['message'] = "Employee & Reader Backup Successfully!";
                    $this->session->set_flashdata('product_operation_message', 'Employee Attendance Imported Successfully!');
                }
                else if ($this->input->post('mode') == 'restore')
                {
                    $this->Backup_model->restorebackup();
                    $data['message'] = "Employee & Reader Backup Restore Successfully!";
                    $this->session->set_flashdata('product_operation_message', 'Employee Attendance Imported Successfully!');
                }
            }
            $this->load->view('backup', $data);
        }

    }
    