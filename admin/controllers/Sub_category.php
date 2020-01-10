<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Sub_category extends MY_Controller
    {

        private $_sub_category_listing_headers = 'sub_category_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Sub_category_model');
            $this->load->model('admin/Category_model');
        }

        public function add_sub_category($category_id = null)
        {
            $this->form_validation->set_rules('sub_category_name', 'Sub Category Name', 'required|trim|unique[sub_category.sub_category_name.sub_category_id.' . $this->input->post('sub_category_id') . ']');
            $this->form_validation->set_rules('category_id', 'Category', 'required|trim');
            $this->form_validation->set_rules('timed_access', 'Timed Access', 'required|trim');


            $dataArray = array();
            $arr_category = $this->Category_model->get_category_array();
            $dataArray['arr_category'] = add_blank_option($arr_category, "-Select Category-");

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Sub Category";
                $dataArray['form_action'] = current_url();
                if (!empty($category_id))
                {
                    $dataArray['form_caption'] = 'Edit Sub Category';
                    $reader_data = $this->Sub_category_model->get_sub_category_by_id($category_id);

                    $dataArray['sub_category_name'] = $reader_data['sub_category_name'];
                    $dataArray['category_id'] = $reader_data['category_id'];

                    $dataArray['timed_access'] = $reader_data['timed_access'];
                    $dataArray['time_duration_access_start_date'] = date('d/m/Y', strtotime($reader_data['time_duration_access_start_date']));
                    $dataArray['time_duration_access_end_date'] = date('d/m/Y', strtotime($reader_data['time_duration_access_end_date']));
                    $dataArray['time_duration_access_end_time'] = $reader_data['time_duration_access_end_time'];
                    $dataArray['normal_timed_access_start_date'] = date('d/m/Y', strtotime($reader_data['normal_timed_access_start_date']));
                    $dataArray['normal_timed_access_hours_duration'] = $reader_data['normal_timed_access_hours_duration'];
                    $dataArray['normal_timed_access_min_duration'] = $reader_data['normal_timed_access_min_duration'];
                    $dataArray['sub_category_id'] = $category_id;
                }
//                $dataArray['local_css'] = array(
//                    'datepicker'
//                );
//                $dataArray['local_js'] = array(
//                    'datepicker'
//                );
                $this->load->view('sub-category-form', $dataArray);
            }
            else
            {
                $sub_category_id = $this->input->post('sub_category_id');

                $dataValues = array(
                    'category_id' => $this->input->post('category_id'),
                    'sub_category_name' => $this->input->post('sub_category_name'),
                    'timed_access' => $this->input->post('timed_access'),
                    'time_duration_access_start_date' => getnDbDate($this->input->post('time_duration_access_start_date')),
                    'time_duration_access_end_date' => getnDbDate($this->input->post('time_duration_access_end_date')),
                    'time_duration_access_end_time' => $this->input->post('time_duration_access_end_time'),
                    'normal_timed_access_start_date' => getnDbDate($this->input->post('normal_timed_access_start_date')),
                    'normal_timed_access_hours_duration' => $this->input->post('normal_timed_access_hours_duration'),
                    'normal_timed_access_min_duration' => $this->input->post('normal_timed_access_min_duration'),
                );

                if (!empty($sub_category_id))
                {
                    $dataValues['sub_category_id'] = $sub_category_id;
                }
                else
                {
                    $dataValues['created_at'] = date('Y-m-d H:i:s');
                }

                $this->Sub_category_model->save_sub_category($dataValues);
                $this->session->set_flashdata('sub_category_operation_message', 'Sub Category Saved Successfully.');
                redirect('admin/sub_category/list_sub_category');
            }
        }

        public function list_sub_category_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_sub_category_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Sub_category_model->get_all_sub_category($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_sub_category_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_sub_category()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('sub_category_operation_message');
            $table_config = array(
                'source' => site_url('admin/sub_category/list_sub_category_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_sub_category_listing_headers, $table_config),
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

            $dataArray['table_heading'] = 'Sub Category List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_caption'] = "Add Sub Category";
            $this->load->view('sub-category-list', $dataArray);
        }

        function delete_sub_category($sub_category_id)
        {
            $status = $this->Sub_category_model->delete_sub_category_by_id($sub_category_id);
            if ($status == true)
            {
                $this->session->set_flashdata('sub_category_operation_message', 'Sub Category deleted successfully');
                redirect('admin/sub_category/list_sub_category');
            }
            else
            {
                show_error('The Sub Category Details you are trying to delete does not exist.');
            }
        }

        public function get_sub_category_by_category_id()
        {
            $category_id = $this->input->post('category_id');
            return get_sub_category($category_id);
    }
    }