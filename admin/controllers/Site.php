<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Site extends MY_Controller
    {

        private $_site_listing_headers = 'site_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Employee_model');
            $this->load->model('admin/Reader_model');
        }

        public function add_site($site_id = null)
        {
            $this->form_validation->set_rules('site_code', 'Site Code', 'required|trim');
            $this->form_validation->set_rules('name', 'Name', 'required|trim|unique[site.name.site_id.' . $this->input->post('site_id') . ']');
            
            $this->form_validation->set_rules('data_format', 'Data Format', 'required|trim');
            $this->form_validation->set_rules('license_validity', 'License Validity', 'required|trim');
            $this->form_validation->set_rules('server_ip', 'Server IP', 'required|trim');

            $dataArray = array();

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Site";
                $dataArray['form_action'] = current_url();
                if (!empty($site_id))
                {
                    $dataArray['form_caption'] = 'Edit Site';
                    $site_data = $this->Site_model->get_site_by_id($site_id);

                    $dataArray['site_id'] = $site_id;
                    $dataArray['site_code'] = $site_data['site_code'];
                    $dataArray['name'] = $site_data['name'];
                    $dataArray['contact'] = $site_data['contact'];
                    $dataArray['notes'] = $site_data['notes'];
//                    $dataArray['license_validity'] = date('m/d/Y', strtotime($site_data['license_validity']));
                    $dataArray['license_validity'] = date('d/m/Y', strtotime($site_data['license_validity']));
                    $dataArray['data_format'] = $site_data['data_format'];
                    $dataArray['data_format_other'] = $site_data['data_format_other'];
                    $dataArray['license_key'] = $site_data['license_key'];
                    $dataArray['server_ip'] = $site_data['server_ip'];
                    $dataArray['server_port'] = $site_data['server_port'];
                    $dataArray['swver'] = $site_data['swver'];
                    $dataArray['status'] = $site_data['status'];
                    $dataArray['access_user'] = $site_data['access_user'];
                }

                $dataArray['local_css'] = array(
                    'jquery.contextMenu',
                    'datepicker'
                );
                $dataArray['local_js'] = array(
                    'jquery.ip-address',
                    'jquery.contextMenu',
                    'jquery.ui.position',
                    'jquery.caret',
                    'ipformat',
                    'datepicker'
                );
                $this->load->view('site-form', $dataArray);
            }
            else
            {

                $site_id = $this->input->post('site_id');

                $dataValues = array(
                    'site_code' => $this->input->post('site_code'),
                    'name' => $this->input->post('name'),
                    'contact' => $this->input->post('contact'),
                    'notes' => $this->input->post('notes'),
                );

                if (!empty($site_id))
                {
                    $dataValues['site_id'] = $site_id;
                }

                $dataValues_site_trans = array(
                    'license_validity' => getSADate($this->input->post('license_validity')),
                    'data_format' => $this->input->post('data_format'),
                    'data_format_other' => $this->input->post('data_format_other'),
                    'server_ip' => $this->input->post('server_ip'),
//                    'status' => $this->input->post('status'),
                    'access_user' => empty($this->input->post('access_user')) ? "No" : "Yes",
                );

                if (!empty($site_id))
                {
                    $site_data = $this->Site_model->get_site_by_id($site_id);
                    if (date('d/m/Y', strtotime($site_data['license_validity'])) != $this->input->post('license_validity'))
                    {
                        $dataValues_site_trans['license_key'] = generate_license_key($this->input->post('site_code'), $this->input->post('license_validity'));
                    }
                    else
                    {
                        $dataValues_site_trans['license_key'] = $site_data['license_key'];
                    }
                }
                else
                {
                    $dataValues_site_trans['license_key'] = generate_license_key($this->input->post('site_code'), $this->input->post('license_validity'));
                }

                if (!empty($site_id))
                {
                    $dataValues_site_trans['site_id'] = $site_id;
                }
                $this->Site_model->save_site($dataValues, $dataValues_site_trans);
                $this->session->set_flashdata('site_operation_message', 'Site saved successfully.');
                redirect('admin/site/list_site');
            }
        }

        public function list_site_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_site_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Site_model->get_all_site($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_site_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_site()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('site_operation_message');
            $table_config = array(
                'source' => site_url('admin/site/list_site_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_site_listing_headers, $table_config),
                'message' => $message
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap',
                'bootbox'
            );

            $dataArray['local_js'] = array(
                'dataTables',
                'dataTables.FilterOnReturn',
                'dataTables.bootstrap',
                'dataTables.responsive',
                'responsive.bootstrap',
                'dataTables.buttons',
                'buttons.bootstrap',
                'buttons.html5',
                'buttons.flash',
                'buttons.print',
                'bootbox'
            );

            $dataArray['table_heading'] = 'Site List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/site/add_site';
            $dataArray['new_entry_caption'] = "Add Site";
            $this->load->view('site-list', $dataArray);
        }

        function delete_site($site_id)
        {
            //get employees associated with site id
            $emp_arr = $this->Employee_model->get_employees_by_site_id($site_id);
            $emp_arr = array_column($emp_arr, 'employee_id');
//            p($emp_arr);
            //get readers associated with site id
            $reader_arr = $this->Reader_model->get_readers_by_site_id_new($site_id);
            $reader_arr = array_column($reader_arr, 'reader_id');
//            p($reader_arr);

            $status = $this->Site_model->delete_site_by_id($site_id, $emp_arr, $reader_arr);
            if ($status == true)
            {
                $this->session->set_flashdata('site_operation_message', 'Site deleted successfully');
                redirect('admin/site/list_site');
            }
            else
            {
                show_error('The Site Details you are trying to delete does not exist.');
            }
        }

    }
    