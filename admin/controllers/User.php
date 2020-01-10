<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class User extends MY_Controller
    {

        private $_users_listing_headers = 'users_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/User_model');
        }

        public function index()
        {
            redirect('/admin/user/validate/');
        }

        public function login()
        {
            $message = $this->session->flashdata('login_operation_message');
            $this->load->setTemplate('blank');
            $this->load->view('login-form', array("message" => $message));
        }

        public function logout()
        {

            $this->session->sess_destroy();
            redirect('admin/user/login', 'refresh');
        }

        public function validate()
        {
            $dataArray[] = array();
            $this->form_validation->set_rules('username', 'User name', 'required'); //|min_length[4]|max_length[50]
            $this->form_validation->set_rules('password', 'Password', 'required'); //|min_length[4]|max_length[50]

            if ($this->form_validation->run() == false)
            {
                $this->session->set_flashdata('login_operation_message', $this->lang->line('error_login_auth'));
                $this->load->view('login-form', $dataArray);
            }
            else
            {
                $encrypted_password = md5($this->input->post('password'));

                $userdata = $this->User_model->login($this->input->post('username'), $encrypted_password);

                $user_license_validity = $this->User_model->check_validity($userdata['site_id']);

                if ($userdata === false)
                {
                    $message = $this->lang->line('error_login_auth');
                    $this->session->set_flashdata('login_operation_message', $message);
                    $this->session->set_flashdata('my_op', $message);
                    redirect('admin/user/login');
                }
                else if ($userdata == 'disabled')
                {
                    $message = $this->lang->line('error_login_disabled_user');
                    $this->session->set_flashdata('login_operation_message', $message);
                    $this->session->set_flashdata('my_op', $message);
                    redirect('admin/user/login');
                }
                else if ($user_license_validity)
                {
                    $message = $this->lang->line('error_login_license_expired');
                    $this->session->set_flashdata('login_operation_message', $message);
                    redirect('admin/user/login');
                }
                else
                {

                    $session_user_data = array(
                        'user_id' => $userdata['admin_id'],
                        'user_name' => $userdata['name'],
//                       'email' => $userdata['email'],
                        'user_type' => $userdata['user_type'],
                        'department_id' => '',
                        'show_emp_type' => 'All',
                    );
                    if ($userdata['user_type'] == 'Super Admin')
                    {
                        $session_user_data['site_id'] = '';
                    }
                    else
                    {
                        $session_user_data['site_id'] = $userdata['site_id'];
                    }

                    $this->session->set_userdata($session_user_data);
                    if ($userdata['user_type'] == 'Report Admin')
                    {
                        redirect('/admin/live_clocking/');
                    }
                    else
                    {
                        redirect('/admin/dashboard/');
                    }
                }
            }
        }

        public function listusers_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_users_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->User_model->get_all_user($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_users_listing_headers);

            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_users()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('member_operation_message');
            $table_config = array(
                'source' => site_url('admin/user/listusers_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_users_listing_headers, $table_config),
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

            $dataArray['table_heading'] = mlLang('users');
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/user/add_user';
            $dataArray['new_entry_caption'] = "Add User";
            $this->load->view('users-list', $dataArray);
        }

        public function forgot_password()
        {
            $dataArrray = array();
            $email = $this->input->post('email');
            //get user data
            $user_data = $this->User_model->get_user_by_email($email);

            if (!empty($user_data))
            {
                //generate new password
                $new_password = generateRandomString(8);
                //update user password
                $dataValues = array(
                    'id' => $user_data['id'],
                    'password' => md5($new_password)
                );
                $this->User_model->save_user($dataValues);

                //send mail to photographer
                $this->load->model('Email_template_model');
                $this->load->library('commonlibrary');
                $email_details = $this->Email_template_model->get_email_template_by_key('forgot_password');

                if (!empty($email_details))
                {
                    $subject = stripcslashes($email_details['subject']);
                    $search = array("{{user_name}}", "{{name}}", "{{email}}", "{{password}}", "{{user_type}}");
                    $replace = array($user_data['username'], $user_data['name'], $user_data['email'], $new_password, mlLang($user_data['user_type']));
                    $content = str_replace($search, $replace, stripcslashes($email_details['email_body']));
                    $attachment = '';
                    $this->commonlibrary->sendmail($user_data['email'], $user_data['username'], $subject, $content, "html", 'AKEA', 'Admin', '', $attachment);
                }
                $return['status'] = "true";
            }
            else
            {
                $return['status'] = "false";
            }
            echo json_encode($return);
        }

        public function add_user($admin_id = null)
        {
            $this->load->model('admin/Site_model');
            $arr_sites = $this->Site_model->get_site_array('name');

            $this->form_validation->set_rules("admin_name", "admin name", 'required|trim|unique[admin.name.admin_id.' . $this->input->post('admin_id') . ']');
            $this->form_validation->set_rules("admin_type", "admin type", "required");
            $this->form_validation->set_rules("password", "password", "required");
            $this->form_validation->set_rules("admin_status", "admin status", "required");
            $dataArray = array();
            $dataArray["arr_admin_type"] = add_blank_option(get_custom_config_item('admin_types'), '-Select-');
            $dataArray["arr_admin_status"] = add_blank_option(get_custom_config_item('admin_status'), '-Select-');
            $dataArray['arr_sites'] = add_blank_option($arr_sites, '-Select-');
            $dataArray['arr_site_trans'] = $arr_sites;

            if ($this->form_validation->run() == false)
            {

                $dataArray['form_caption'] = "Add Admin";
                $dataArray['form_action'] = current_url();
                if (!empty($admin_id))
                {
                    $dataArray['form_caption'] = 'Edit Admin';
                    $admin_data = $this->User_model->get_admin_by_id($admin_id);
                    $site_data = $this->Site_model->get_sites_from_trans($admin_id);
//                    p($admin_data);
                    $dataArray['admin_name'] = $admin_data['name'];
                    $dataArray['admin_id'] = $admin_id;
                    $dataArray['admin_type'] = $admin_data['user_type'];
                    $dataArray['admin_status'] = $admin_data['status'];
                    $dataArray['sites'] = $admin_data['site_id'];

                    if (!empty($site_data))
                    {
                        foreach ($site_data as $site)
                        {
                            $site_id[] = $site['site_id'];
                        }
                        $dataArray['site_id_arr'] = $site_id;
                    }
                }

                $dataArray['local_css'] = array(
                    'select2'
                );
                $dataArray['local_js'] = array(
                    'select2'
                );

                $this->load->view('user-form', $dataArray);
            }
            else
            {
//                p($_POST);
                $dataValues = array(
                    'name' => $this->input->post('admin_name'),
                    'password' => MD5($this->input->post('password')),
                    'status' => $this->input->post('admin_status'),
                    'user_type' => $this->input->post('admin_type'),
                );

                if (!empty($this->input->post('sites')))
                {
                    $dataValues['site_id'] = $this->input->post('sites');
                }

                $site_trans = array();
                if (!empty($this->input->post('site_trans')))
                {
                    $site_trans = $this->input->post('site_trans');
                }

                if (!empty($admin_id))
                {
                    $dataValues['admin_id'] = $admin_id;
                }
                else
                {
                    $dataValues['created_at'] = date('Y-m-d H:i:s');
                }

                $this->User_model->save_admin($dataValues, $site_trans);
                $this->session->set_flashdata('member_operation_message', 'User saved successfully.');
                redirect('admin/user/list_users');
            }
        }

        function delete_user($user_id)
        {
            $status = $this->User_model->delete_user_by_id($user_id);
            if ($status == true)
            {
                $this->session->set_flashdata('member_operation_message', 'User deleted successfully');
                redirect('admin/user/list_users');
            }
            else
            {
                show_error('The User Details you are trying to delete does not exist.');
            }
        }

        public function change_user_status()
        {
            $dataValues = array();
            $id = $this->input->post("id");
            $status = $this->input->post("status");

            $dataArray = array(
                "id" => $id,
                "status" => $status == "Active" ? "Disabled" : "Active",
            );
            $user_id = $this->User_model->save_user($dataArray);
            $dataValues['active_id'] = "active_status_" . $user_id;
            $dataValues['add_class_name'] = $status == "active" ? "fa-toggle-off" : "fa-toggle-on";
            $dataValues['remove_class_name'] = $status == "active" ? "fa-toggle-on" : "fa-toggle-off";
            $dataValues['data_attr'] = ($status == "active") ? "disabled" : "active";
            if (!empty($user_id))
            {
                $dataValues['message'] = "<div class='alert alert-success'>Update Successfully</div>";
            }
            else
            {
                $dataValues['message'] = "<div class='alert alert-danger'>Cannot update please try again later</div>";
            }
            echo json_encode($dataValues);
        }

        function exportUserList()
        {
            $this->load->dbutil();
            $this->load->helper('file');
            $this->load->helper('download');
            $delimiter = ",";
            $newline = "\r\n";
            $filename = "AllUsersList.csv";

            $result = $this->User_model->prepareAllUsersCsv();
        }

        function check_email_remote($admin_id = Null)
        {
            $user['email'] = $this->input->post('email');
            $user['id'] = $admin_id;
            return check_email_exist($user);
        }

    }
    