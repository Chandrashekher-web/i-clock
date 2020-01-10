<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Department extends MY_Controller
    {

        private $_department_listing_headers = 'department_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Department_model');
        }

        public function add_department($department_id = null)
        {
            $session_site_id = get_session_site_id();
            $this->form_validation->set_rules('name', 'Department Name', 'required|trim|unique[department.name.department_id.' . $this->input->post('department_id') . ']');

            $dataArray = array();



            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Department";
                $dataArray['form_action'] = current_url();
                if (!empty($department_id))
                {
                    $dataArray['form_caption'] = 'Edit Department';
                    $department_data = $this->Department_model->get_department_by_id($department_id);
                    $dataArray['name'] = $department_data['name'];
                    $dataArray['department_id'] = $department_id;
                }

                $dataArray['local_css'] = array(
                );
                $dataArray['local_js'] = array(
                );
                $this->load->view('department-form', $dataArray);
            }
            else
            {
                $department_id = $this->input->post('department_id');

                $dataValues = array(
                    'name' => $this->input->post('name'),
                    'site_id' => $session_site_id,
                );


                if (!empty($department_id))
                {
                    $dataValues['department_id'] = $department_id;
                }

                $this->Department_model->save_department($dataValues);
                $this->session->set_flashdata('department_operation_message', 'Department saved successfully.');
                redirect('admin/department/list_department');
            }
        }

        public function list_department_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_department_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Department_model->get_all_department($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_department_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_department()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('department_operation_message');
            $table_config = array(
                'source' => site_url('admin/department/list_department_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_department_listing_headers, $table_config),
                'message' => $message
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap',
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
            );

            $dataArray['table_heading'] = 'Department List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/department/add_department';
            $dataArray['new_entry_caption'] = "Add Department";
            $this->load->view('department-list', $dataArray);
        }

        function delete_department($department_id)
        {
            $status = $this->Department_model->delete_department_by_id($department_id);
            if ($status == true)
            {
                $this->session->set_flashdata('department_operation_message', 'Department deleted successfully');
                redirect('admin/department/list_department');
            }
            else
            {
                show_error('The Department Details you are trying to delete does not exist.');
            }
        }

    }
    