<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Category extends MY_Controller
    {

        private $_category_listing_headers = 'category_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Category_model');
        }

        public function add_category($category_id = null)
        {
            $this->form_validation->set_rules('category_name', 'Category Name', 'required|trim|unique[category.category_name.category_id.' . $this->input->post('category_id') . ']');
//            $this->form_validation->set_rules('category_name', 'Category Name', 'required|trim');

            $dataArray = array();

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Category";
                $dataArray['form_action'] = current_url();
                if (!empty($category_id))
                {
                    $dataArray['form_caption'] = 'Edit Category';
                    $reader_data = $this->Category_model->get_category_by_id($category_id);

                    $dataArray['category_name'] = $reader_data['category_name'];
                    $dataArray['category_id'] = $category_id;
                }
                $this->load->view('category-form', $dataArray);
            }
            else
            {
                $category_id = $this->input->post('category_id');

                $dataValues = array(
                    'category_name' => $this->input->post('category_name'),
                );

                if (!empty($category_id))
                {
                    $dataValues['category_id'] = $category_id;
                }
                else
                {
                    $dataValues['created_at'] = date('Y-m-d H:i:s');
                }


                $this->Category_model->save_category($dataValues);
                $this->session->set_flashdata('category_operation_message', 'Category Saved Successfully.');
                redirect('admin/category/list_category');
            }
        }

        public function list_category_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_category_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Category_model->get_all_category($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_category_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_category()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('category_operation_message');
            $table_config = array(
                'source' => site_url('admin/category/list_category_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_category_listing_headers, $table_config),
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

            $dataArray['table_heading'] = 'Category List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_caption'] = "Add Category";
            $this->load->view('category-list', $dataArray);
        }

        function delete_category($category_id)
        {
            $status = $this->Category_model->delete_category_by_id($category_id);
            if ($status == true)
            {
                $this->session->set_flashdata('category_operation_message', 'Category deleted successfully');
                redirect('admin/category/list_category');
            }
            else
            {
                show_error('The Category Details you are trying to delete does not exist.');
            }
        }
        
        function get_category_by_name($category)
        {
            $category_id = $this->Category_model->get_category_by_name($category);
            return $category_id;
        }
    }
    