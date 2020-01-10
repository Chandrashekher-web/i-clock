<?php
    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Live_clocking extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Live_clocking_model');
            $this->load->model('admin/Employee_model');
            $this->load->model('admin/Reader_model');
            $this->load->model('admin/Department_model');
        }

        function index()
        {
            $dataArray = array();
            $conditions_arr = array();
            if ($this->input->post('mode') && $this->input->post('mode') == 'filter')
            {
                $dataArray['from_date'] = $this->input->post('from_date');
                $dataArray['to_date'] = $this->input->post('to_date');
                $dataArray['employee_id'] = $this->input->post('employee');
                $dataArray['reader_id'] = $this->input->post('reader');
                $dataArray['department_id'] = $this->input->post('department');
            }
            $clocking_data = $this->Live_clocking_model->get_live_clocking($dataArray);
            
            $employee_arr = $this->Employee_model->get_employee_array('name');
            $dataArray['employee_arr'] = add_blank_option($employee_arr, "-Select Employee-");
            $reader_arr = $this->Reader_model->get_reader_array('name');
            $dataArray['reader_arr'] = add_blank_option($reader_arr, "-Select Reader-");
            $department_arr = $this->Department_model->get_department_array();
            $dataArray['department_arr'] = add_blank_option($department_arr,"-Select Department-");
         
            $clocking_arr = array();
            if (!empty($clocking_data))
            {
                foreach ($clocking_data as $key => $clocking)
                {
                    $clocking_arr[$key] = $clocking;
                    if (!empty($clocking['access_group']))
                    {
                        $reader = get_reader_by_access_groups($clocking['access_group'], 'IN');
                        if (in_array($clocking['reader_id'], $reader))
                        {
                            $clocking_arr[$key]['direction'] = 'IN';
                        }
                        else
                        {
                            $clocking_arr[$key]['direction'] = 'OUT';
                        }
                    }
                    else
                    {
                        $clocking_arr[$key]['direction'] = '';
                    }
                }
            }
            $dataArray['local_css'] = array(
                'datepicker'
            );
            $dataArray['local_js'] = array(
                'datepicker',
                //  'form-advanced'
            );
            $dataArray['form_caption'] = 'Live Clocking List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/reader/add_reader';
            $dataArray['new_entry_caption'] = "Live Clocking List";
            $dataArray['clocking_data'] = $clocking_arr;
            $this->load->view('live-clocking-list', $dataArray);
        }

        function get_latest_clocking()
        {
            $dataArray = array();
            $conditions_arr = array();
            $dataArray['from_date'] = $this->input->post('from_date');
            $dataArray['to_date'] = $this->input->post('to_date');
            $dataArray['employee_id'] = $this->input->post('employee');
            $dataArray['reader_id'] = $this->input->post('reader');
            $dataArray['attendance_id'] = $this->input->post('last_clocking_id');
            $dataArray['department_id'] = $this->input->post('department');
            $clocking_data = $this->Live_clocking_model->get_live_clocking($dataArray);


            $clocking_arr = array();
            if (!empty($clocking_data))
            {
                foreach ($clocking_data as $key => $clocking)
                {
                    $clocking_arr[$key] = $clocking;
                    if (!empty($clocking['access_group']))
                    {
                        $reader = get_reader_by_access_groups($clocking['access_group'], 'IN');
                        if (in_array($clocking['reader_id'], $reader))
                        {
                            $clocking_arr[$key]['direction'] = 'IN';
                        }
                        else
                        {
                            $clocking_arr[$key]['direction'] = 'OUT';
                        }
                    }
                    else
                    {
                        $clocking_arr[$key]['direction'] = '';
                    }
                }
            }
            $last_clocking_id = current($clocking_arr);
            $dataArray['clocking_data'] = $clocking_arr;

            $return_str = $this->load->viewPartial('live-clocking-list-ajex', $dataArray);

            echo $return_str;
            ?>
            <script>
                $('#last_clocking_id').val(<?php echo $last_clocking_id['attendance_id']; ?>);
            </script>
            <?php
        }

        function remove_clocking()
        {
            $clocking_id = $this->input->post('clocking_id');
            $this->Live_clocking_model->remove_clocking($clocking_id);
        }

    }
    