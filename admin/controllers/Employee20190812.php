<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Employee extends MY_Controller {

    private $_employee_listing_headers = 'employee_listing_headers';

    public function __construct() {
        parent::__construct();
        $this->load->library("Upload");
        $this->load->model('admin/Employee_model');
        $this->load->model('admin/Site_model');
        $this->load->model('admin/Reader_model');
        $this->load->library('commonlibrary');
    }

    public function add_employee($employee_id = null, $page = null) {
        $session_site_id = get_session_site_id();
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        if (!empty($employee_id)) {
            $this->form_validation->set_rules('pin', 'Pin', 'required|trim|numeric|max_length[9]|unique[employee.pin.site_id.' . $session_site_id . '.employee_id]');
        } else {
            $this->form_validation->set_rules('pin', 'Pin', 'required|trim|numeric|max_length[9]|unique[employee.pin.site_id.' . $session_site_id . ']');
        }
        $dataArray = array();

        $arr_isadmin = get_custom_config_item('isadmin');

        $dataArray['arr_isadmin'] = $arr_isadmin;

        if ($this->form_validation->run() == false) {
            $dataArray['form_caption'] = "Add Employee";
            $dataArray['form_action'] = current_url();
            if (!empty($employee_id)) {
                $dataArray['form_caption'] = 'Edit Employee';
                $employee_data = $this->Employee_model->get_employee_by_id($employee_id);

                $dataArray['name'] = $employee_data['name'];
                $dataArray['pin'] = $employee_data['pin'];
                $dataArray['password'] = $employee_data['password'];
                $dataArray['card'] = $employee_data['card'];
                $dataArray['administrator'] = $employee_data['priv'] == ADMIN_USER_PRIV ? "Yes" : "No";
                $dataArray['employee_id'] = $employee_id;
            }

            $dataArray['local_css'] = array(
            );
            $dataArray['local_js'] = array(
            );
            $this->load->view('employee-form', $dataArray);
        } else {
            $employee_id = $this->input->post('employee_id');

            $priv = $this->input->post('administrator') == "Yes" ? ADMIN_USER_PRIV : NORMAL_USER_PRIV;

            $dataValues = array(
                'name' => $this->input->post('name'),
                'pin' => $this->input->post('pin'),
                'password' => $this->input->post('password'),
                'priv' => $priv,
                'card' => $this->input->post('card'),
                'site_id' => $session_site_id
            );

            if (!empty($employee_id)) {
                $dataValues['employee_id'] = $employee_id;
                $employee_data = $this->Employee_model->get_employee_by_id($employee_id);
            }

            $this->Employee_model->save_employee($dataValues);

//                if (!empty($employee_id))
//                {
//                    $sourceinfo = get_custom_config_item('sourceinfo');
//                    $emp_data[0] =  $employee_data;
//                  //  $is_emp_updated = is_emp_updated($dataValues, $emp_data);
////                    if ($employee_data['priv'] != $priv)
//                    $is_emp_updated == "Yes";
//                    if ($is_emp_updated == "Yes")
//                    {
//                        $arr_employee_readers = $this->iclock_model->get_employee_reader($employee_id);
//                    }
//                    if (!empty($arr_employee_readers))
//                    {
//                        $command1 = get_delete_user_command($employee_data['pin']);
//                        save_employee_reader_commands($arr_employee_readers, $command1, $sourceinfo);
//                        $command2 = get_add_user_command2($dataValues);
//                        save_employee_reader_commands($arr_employee_readers, $command2, $sourceinfo);
//                        
//                        // get employee fp count
//                        $employee_fp_data = $this->iclock_model->get_employee_fp_data($employee_id);
//                        if (!empty($employee_fp_data))
//                        {
//                            foreach ($employee_fp_data as $key => $employee_fp)
//                            {
//                                $command3 = get_add_finger_print_command($employee_fp, "UPDATE");
//                                save_employee_reader_commands($arr_employee_readers, $command3, $sourceinfo);
//                            }
//                        }
//                    }
//                }

            if (!empty($employee_id)) {
                $sourceinfo = get_custom_config_item('sourceinfo');
                $emp_data[0] = $employee_data;
                $is_emp_updated = 'Yes';
                // $is_emp_updated = is_emp_updated($dataValues, $emp_data);
//             if ($employee_data['priv'] != $priv)

                if ($is_emp_updated == "Yes") {
                    $arr_employee_readers = $this->iclock_model->get_employee_reader($employee_id);
                }
                if (!empty($arr_employee_readers)) {
                    $command1 = get_delete_user_command($employee_data['pin']);
                    save_employee_reader_commands($arr_employee_readers, $command1, $sourceinfo);

                    $group_code = $this->Time_zone_model->get_access_group_code_by_access_groups_id($this->input->post('access_group'));

                    $dataValues['grp_data'] = $group_code['code_id'];

                    $command2 = get_add_user_command2($dataValues);

                    save_employee_reader_commands($arr_employee_readers, $command2, $sourceinfo);

                    // get employee fp count
                    $employee_fp_data = $this->iclock_model->get_employee_fp_data($employee_id);
                    if (!empty($employee_fp_data)) {
                        foreach ($employee_fp_data as $key => $employee_fp) {
                            $command3 = get_add_finger_print_command($employee_fp, "UPDATE");
                            save_employee_reader_commands($arr_employee_readers, $command3, $sourceinfo);
                        }
                    }
                }
            }
            $this->session->set_flashdata('employee_operation_message', 'Employee saved successfully.');
            if (empty($page)) {
                redirect('admin/employee/list_employee');
            } else {
                redirect('admin/' . $page);
            }
        }
    }

    public function list_employee_data() {
        $this->load->library('Datatable');
        $arr = $this->config->config[$this->_employee_listing_headers];
        $cols = array_keys($arr);
        $pagingParams = $this->datatable->get_paging_params($cols);
        $resultdata = $this->Employee_model->get_all_employee($pagingParams);
//            p($resultdata['resultSet']);

        if (!empty($resultdata['resultSet'])) {
            foreach ($resultdata['resultSet'] as $key => $value) {
                $fp_count = $this->Employee_model->get_fp_count_by_employee_id($value->employee_id);
                //                p($fp_count);
                $resultdata['resultSet'][$key]->fpcount = $fp_count;
            }
        }
//            p($resultdata['resultSet']);

        $json_output = $this->datatable->get_json_output($resultdata, $this->_employee_listing_headers);
        $this->load->setTemplate('json');
        $this->load->view('json', $json_output);
    }

    function list_employee() {
        $this->load->library('Datatable');
        $message = $this->session->flashdata('employee_operation_message');
        $table_config = array(
            'source' => site_url('admin/employee/list_employee_data'),
            'datatable_class' => $this->config->config["datatable_class"],
        );
        $dataArray = array(
            'table' => $this->datatable->make_table($this->_employee_listing_headers, $table_config),
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

        $dataArray['table_heading'] = 'Employee List';
        $dataArray['form_action'] = current_url();
        $dataArray['new_entry_link'] = base_url() . 'admin/employee/add_employee';
        $dataArray['new_entry_caption'] = "Add Employee";
        $this->load->view('employee-list', $dataArray);
    }

    function delete_employee($employee_id) {
        $status = $this->Employee_model->delete_employee_by_id($employee_id);
        if ($status == true) {
            $this->session->set_flashdata('employee_operation_message', 'Employee deleted successfully');
            redirect('admin/employee/list_employee');
        } else {
            show_error('The Employee Details you are trying to delete does not exist.');
        }
    }

    public function show_total_employee_report($key) {
        $dataArray = array();
        $session_site_id = get_session_site_id();
        //$this->form_validation->set_rules('tender', 'Tender', 'required|trim');
        $arr_sites = $this->Site_model->get_site_array('name');
        $arr_reader = array();
        $dataArray['arr_sites'] = add_blank_option($arr_sites, "-- Select Site --");
        $dataArray['arr_reader'] = add_blank_option($arr_reader, "-- Select Reader --");
        $dataArray['key'] = $key;
        $dataArray['site_id'] = $session_site_id;
        if ($this->form_validation->run() == false) {
            if ($key == 'total') {
                $dataArray['form_caption'] = "Total Employees Report";
            } else if ($key == 'with_0_fps') {
                $dataArray['form_caption'] = "Employees with â0â fps Report";
            } else if ($key == 'with_0_rfcard') {
                $dataArray['form_caption'] = "Employees with â0â RF card Report";
            } else if ($key == 'per_reader') {
                $dataArray['form_caption'] = "Employees per reader";
            } else if ($key == 'offline_readers') {
                $dataArray['form_caption'] = "Offline readers";
            }

            $dataArray['form_action'] = current_url();
            $this->load->view('total-employee-report', $dataArray);
        }
    }

    public function get_employee_details() {
//            p($_POST);
        $output = '';
        $empdata = array();
        $site_id = $this->input->post('site_id');
        $reader_id = $this->input->post('reader_id');
        $key = $this->input->post('key');
        if ($key == 'total') {
            $empdata = $this->Employee_model->get_total_employees($site_id);
            // p($empdata);
        } else if ($key == 'with_0_fps') {
            $empdata = $this->Employee_model->get_total_employees_with_0_fps($site_id);
        } else if ($key == 'with_0_rfcard') {
            $empdata = $this->Employee_model->get_total_employees_with_0_rfcard($site_id);
        } else if ($key == 'per_reader') {
            $empdata = $this->Employee_model->get_total_employees_per_reader($reader_id);
        } else if ($key == 'offline_readers') {
            $empdata = $this->Reader_model->get_offline_readers($site_id);
        }
        $count = count($empdata);

        if ($key == 'offline_readers') {
            $output = '<p>Total reader count : ' . $count . '</p>';

            if (!empty($empdata)) {
                $output .= "<table id='myTable' class='table table-striped table-bordered'><tr><th>Site Name</th><th>SN</th><th>Name</th><th style='width:20%'>Seen</th><th>Stamp</th><th>Opstamp</th></tr>";
                foreach ($empdata as $key => $employee) {
                    $output .= "<tr><td>" . $employee['site_name'] . "</td>";
                    $output .= "<td>" . $employee['sn'] . "</td>";
                    $output .= "<td>" . $employee['name'] . "</td>";
                    $output .= "<td>" . $employee['seen'] . "</td>";
                    $output .= "<td>" . $employee['stamp'] . "</td>";
                    $output .= "<td>" . $employee['opstamp'] . "</td></tr>";
                }
            } else {
                $output = "<p>Total reader count : 0 </p>";
            }
        } else if ($key == 'total') {
            $totfp = 0;
            $totface = 0;
            $output = '<p>Total employee count : ' . $count . '</p>';
            $output .= '<p id="tot_fp">Total FP count : ' . $totfp . '</p>';
            $output .= '<p id="tot_face">Total Face count : ' . $totface . '</p>';

            if (!empty($empdata)) {
                $output .= "<table id='myTable' class='table table-striped table-bordered'><tr><th>Pin</th><th>Name</th><th>Password</th><th>RF Card</th><th>FP Count</th><th>Face Count</th></tr>";
                foreach ($empdata as $key => $employee) {
                    $output .= "<tr><td>" . $employee['pin'] . "</td>";
                    $output .= "<td>" . $employee['name'] . "</td>";
                    $output .= "<td>" . $employee['password'] . "</td>";
                    $output .= "<td>" . $employee['card'] . "</td>";
                    $output .= "<td>" . $employee['fpcount'] . "</td>";
                    $output .= "<td>" . $employee['fcount'] . "</td></tr>";

                    $totfp = $totfp + $employee['fpcount'];
                    $totface = $totface + $employee['fcount'];
                }
                ?> 
                <script>
                    $('#tot_fp').html("Total FP count : <?php echo $totfp; ?> ");
                    $('#tot_face').html("Total Face count : <?php echo $totface; ?>");
                </script>
                <?php
            } else {
                $output = "<p>Total employee count : 0 </p>";
            }
        } else {
            $output = '<p>Total employee count : ' . $count . '</p>';

            if (!empty($empdata)) {
                $output .= "<table id='myTable' class='table table-striped table-bordered'><tr><th>Pin</th><th>Name</th><th>Password</th><th>RF Card</th></tr>";
                foreach ($empdata as $key => $employee) {
                    $output .= "<tr><td>" . $employee['pin'] . "</td>";
                    $output .= "<td>" . $employee['name'] . "</td>";
                    $output .= "<td>" . $employee['password'] . "</td>";
                    $output .= "<td>" . $employee['card'] . "</td></tr>";
                }
            } else {
                $output = "<p>Total employee count : 0 </p>";
            }
        }
        echo $output;
    }

    public function import_employee_list() {
        if (!empty($this->input->post())) {

            $data = array();
            $fname = time() . ".csv";

            $import_config = get_custom_config_item('import_employee');

            if (!is_dir($import_config['upload_path'])) {
                mkdir('assets/uploads/' . $import_config['folder_name'], 0777, TRUE);
            }


            $file = $import_config['upload_path'] . $fname;
            if (!empty($_FILES['import_employee'])) {
                $site_id = get_session_site_id();
                if (is_file($file)) {
                    unlink($file);
                }


                $import_config['file_name'] = $fname;

                if ($this->commonlibrary->is_file_uploaded('import_employee')) {
                    $uploaddata = $this->upload->upload_file("import_employee", $import_config['upload_path'], $import_config);

                    $row = 1;
                    if (($handle = fopen($file, "r")) !== FALSE) {
                        $c = 0;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                            if ($row > 1) {
                                if (!empty($data[0])) {
                                    $pin = trim($data[0]);
                                    $name = trim($data[1]);

                                    $dataValue = array(
                                        "pin" => $pin,
                                        "name" => $name,
                                        "site_id" => $site_id,
                                    );
                                    $lastId = $this->Employee_model->import_employees($dataValue);
                                }
                            }

                            $row++;
                            $c++;
                        }

                        fclose($handle);
                    }

                    $data['message'] = "Csv File Imported Successfully!";
                    $this->session->set_flashdata('product_operation_message', 'Products Imported Successfully!');
                } else {
                    $data['message'] = $this->upload->display_errors();
                    $this->session->set_flashdata('product_operation_message', 'Error occured!');
                }
            }
        }
        $data['form_caption'] = "Import Employee";
        $data['form_action'] = current_url();
//            p($data);
        $this->load->view('import-employees', $data);
    }

    public function bulk_employee_delete() {
//            p($_POST);
        $dataArray = array();

        if (!empty($_POST)) {
            $selectchk = $this->input->post('employee_id');

            foreach ($selectchk as $employee_id) {
                $status = $this->Employee_model->delete_employee_by_id($employee_id);
            }
            $dataArray['message'] = "Successfully Deleted.";
        }

        $dataArray['form_caption'] = "Delete Bulk Employees";
        $site_id = get_session_site_id();
        $empdata = $this->Employee_model->get_employees_not_assoc_with_any_reader($site_id);
        $dataArray['empdata'] = $empdata;
//            p($empdata);
        if ($this->form_validation->run() == false) {
            $dataArray['form_action'] = current_url();
            $this->load->view('employee-delete-list', $dataArray);
        }
    }

    public function delete_bulk_employees() {
        $selectchk = $this->input->post('selectchk');
//            p($selectchk);
        if (empty($selectchk)) {
            echo json_encode(array("error" => 1));
            exit();
        }
        $chks = explode(',', $selectchk);
        foreach ($chks as $employee_id) {
            $status = $this->Employee_model->delete_employee_by_id($employee_id);
        }
        echo json_encode(array("error" => 0));
        exit();
    }

    public function get_readers() {
        $site_id = $this->input->post('site_id');

        $arr_readers = $this->Reader_model->get_readers_by_site_id($site_id);

        $html = "<option value=''>-- Select Reader --</option>";
        if (!empty($arr_readers)) {
            foreach ($arr_readers as $key => $reader) {
                $html .= '<option value="' . $key . '">' . $reader . '</option>';
            }
        }

        echo $html;
    }

    public function list_employee_not_allocated_to_reader() {
        $arr_sites = $this->Site_model->get_site_array('name');
        $session_site_id = get_session_site_id();
        $arr_employee_filter = get_custom_config_item('employee_filter');
        $arr_employee_status_update = get_custom_config_item('employee_status_update');

        $dataArray['arr_employee_filter'] = $arr_employee_filter;
        $dataArray['arr_employee_status_update'] = $arr_employee_status_update;
        $status = '';
        $update_mode = '';

        if (!empty($this->input->post('filtermode'))) {
            $status = $this->input->post('filtermode');
        } else if (!empty($this->input->post('mode'))) {
            if (!empty($this->input->post('employee_id'))) {
                $update_mode = $this->input->post('mode');
                foreach ($this->input->post('employee_id') as $key => $empid) {
                    $dataValue = array('employee_id' => $empid, 'status' => $update_mode);
                    $this->Employee_model->employees_status_update($dataValue);
                }

                $this->session->set_flashdata('employe_status_update_operation_message', 'Employee Selected Operation Updated successfully.');
            }
        }

        $arr_employee = $this->Employee_model->get_employees_not_assoc_with_any_reader($session_site_id, $status);

        $message = $this->session->flashdata('employe_status_update_operation_message');
        $dataArray['message'] = $message;
        $dataArray['status'] = $status;
        $dataArray['update_mode'] = $update_mode;
        $dataArray['form_caption'] = "Employee Not Allocated Any Reader List";

        $dataArray['session_site_id'] = $session_site_id;
        $dataArray['arr_employee'] = $arr_employee;

        $dataArray['form_action'] = current_url();

        $this->load->view('employee_not_allocated_to_reader_list', $dataArray);
    }

    public function list_employees_with_duplicate_fps() {
        $dataArray = array();
        $arr_sites = $this->Site_model->get_site_array('name');
        $arr_reader = array();
        $dataArray['arr_sites'] = add_blank_option($arr_sites, "-- Select Site --");
        $dataArray['arr_reader'] = add_blank_option($arr_reader, "-- Select Reader --");
        if ($this->form_validation->run() == false) {
            $dataArray['form_caption'] = "Employees with Duplicate FPs";
            $dataArray['form_action'] = current_url();
            $this->load->view('employees-with-duplicate-fps', $dataArray);
        }
    }

    public function get_employee_with_duplicate_fps() {
        $output = '';
        $empdata = array();
        $site_id = $this->input->post('site_id');
        $empdata = get_employees_with_duplicate_fps();
//            p($empdata);
        $count = count($empdata);
        $output = '<p>Total Duplicate FPs : ' . $count . '</p>';

        if (!empty($empdata)) {
            $count2 = 1;
            $output .= "<table id='myTable' class='table table-striped table-bordered'><tr><th>S.No.</th><th>Employee Info</th><th>Delete FPs</th></tr>";
            foreach ($empdata as $key => $employee) {
                $ids = '';
                $output .= "<tr><td>" . $count2 . "</td>";
                $output .= "<td>";
                $output .= "<table id='myTable2' class='table table-striped table-bordered'><tr><th>Pin</th><th>Name</th><th>Password</th><th>RF Card</th><th>Delete FP</th></tr>";
                foreach ($employee as $value) {
                    $output .= "<tr><td>" . $value['pin'] . "</td>";
                    $output .= "<td>" . $value['name'] . "</td>";
                    $output .= "<td>" . $value['password'] . "</td>";
                    $output .= "<td>" . $value['card'] . "</td>";
                    $output .= "<td style='text-align:center;'><a href='" . base_url() . "admin/employee/delete_employee_fp/" . $value['id'] . "' onclick=\"return confirm('Are you sure you want to delete the fingerprint for this employee?');\" class='cancel-status'><i class='fa fa-trash-o action-icon'></i></a></td></tr>";
                    $ids .= $value['id'] . '|';
                }

                $output .= "</table>";
                $output .= "</td>";
                $output .= "<td style='text-align:center;'><a href='" . base_url() . "admin/employee/delete_employee_fp/" . $ids . "' onclick=\"return confirm('Are you sure you want to delete this fingerprint for all the employees?');\" class='cancel-status'><i class='fa fa-trash-o action-icon'></i></a></td></tr>";
                $count2 ++;
            }
            $output .= "</table>";
        } else {
            $output = "<p>Total Duplicate FPs : 0 </p>";
        }
        echo $output;
    }

    function delete_employee_fp($fp_id) {
        $status = $this->Employee_model->delete_employee_fp_by_id($fp_id);
        if ($status == true) {
            $this->session->set_flashdata('employee_operation_message', 'FP deleted successfully');
            redirect('admin/employee/list_employees_with_duplicate_fps');
        } else {
            show_error('The FP Details you are trying to delete does not exist.');
        }
    }

    function delete_employee_fp_by_employee_id($employee_id) {
        $status = $this->Employee_model->delete_employee_fp_by_employee_id($employee_id);
        if ($status == true) {
            $this->session->set_flashdata('employee_operation_message', 'FP deleted successfully');
            redirect('admin/employee/list_employee');
        } else {
            show_error('The FP Details you are trying to delete does not exist.');
        }
    }

}
