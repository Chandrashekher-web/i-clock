<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Employee_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        private $_batchImport;

        public function get_all_employee($pagingParams = array())
        {
            $session_site_id = get_session_site_id();

            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('e.*, count(face.fid) as fcount,rag.description as assessgroup');
//            $this->db->select('e.*, count(face.fid) as fcount, count(fp.fid) as fpcount');
            $this->db->from('employee as e');
            $this->db->join('employee_face as face', 'e.employee_id = face.employee_id', 'left');
//            $this->db->join('employee_fp as fp', 'e.employee_id = fp.employee_id', 'left');
            $this->db->join('reader_access_groups as rag', 'e.access_group = rag.code_id', 'left');

            $this->db->where('e.site_id', $session_site_id);
            $this->db->group_by('e.employee_id');

            if (!empty($pagingParams['order_by']))
            {
                if (empty($pagingParams['order_direction']))
                {
                    $pagingParams['order_direction'] = '';
                }
                switch ($pagingParams['order_by'])
                {
                    default:
                        $this->db->order_by($pagingParams['order_by'], $pagingParams['order_direction']);
                        break;
                }
            }
            $search = empty($pagingParams['search']) ? array() : $pagingParams['search'];
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('pin', $search);
                $this->db->or_like('name', $search);
                $this->db->or_like('card', $search);
                $this->db->or_like('property_number', $search);
                $this->db->or_like('rag.description', $search);
                $this->db->group_end();
            }
            $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            // p($this->db->last_query());
            return $return;
        }

        public function get_employee_by_id($employee_id)
        {
            $return = NULL;
            if (!empty($employee_id))
            {
                $this->db->select('e.*');
                $this->db->from('employee as e');
                $this->db->where('employee_id', $employee_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_employee($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['employee_id']))
                {
                    //p($dataValue);
                    $this->db->where('employee_id', $dataValue['employee_id']);
                    
                    $this->db->update('employee', $dataValue);
                 //  p(  $this->db->update('employee', $dataValue));
                    $return = $dataValue['employee_id'];
                }
                else
                {
                    $this->db->insert('employee', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_employee_by_id($employee_id)
        {
            $this->db->trans_start();

            //delete from employee_reader_trans
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee');

            //delete from employee_face
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee_face');

            //delete from employee_fp
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee_fp');

            //delete from employee_pic
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee_pic');

            //delete from employee
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee');
            $return = $this->db->trans_complete();
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_employee_array($fieldname = 'pin')
        {
            $data = array();
            $session_site_id = get_session_site_id();
            $this->db->select('*');
            $this->db->from('employee');
            $this->db->where('site_id', $session_site_id);
            $this->db->order_by($fieldname);
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['employee_id']] = $row[$fieldname];
            }
            return $data;
        }

        public function get_employee_reader_trans($pagingParams = array())
        {
            $arr_employee = array();

            $session_site_id = get_session_site_id();

            $this->db->select('e.*, ert.reader_id,(SELECT COUNT(*) FROM employee_fp as efp WHERE employee_id=e.employee_id) as fpcount');
            $this->db->from('employee as e');
            $this->db->join('employee_reader_trans as ert', 'e.employee_id = ert.employee_id', 'left');
            $this->db->where('e.site_id', $session_site_id);
            $this->db->where('e.status', 'Active');
            if (!empty($pagingParams['show_emp']))
            {
                if ($pagingParams['show_emp'] == 'Super admin')
                {
                    $this->db->where('e.priv', ADMIN_USER_PRIV);
                }
            }
            $this->db->order_by('e.name');
            $query = $this->db->get();

            foreach ($query->result_array() as $row)
            {
                if (array_key_exists($row['employee_id'], $arr_employee))
                {
//                    p("here",0);
//                    p($arr_employee[$row['employee_id']],0);
                    array_push($arr_employee[$row['employee_id']]["reader_trans"], $row['reader_id']);
//                    p($arr_employee[$row['employee_id']]);
                }
                else
                {
//                    p("there",0);
                    $arr_employee[$row['employee_id']] = array(
                        "employee_id" => $row["employee_id"],
                        "name" => $row["name"],
                        "pin" => $row["pin"],
                        "password" => $row["password"],
                        "card" => $row["card"],
                        "priv" => $row["priv"],
                        "fp" => $row["fpcount"],
                        "reader_trans" => empty($row['reader_id']) ? array() : array($row['reader_id'])
                    );
                }
            }

            return $arr_employee;
        }

        public function get_employee_reader_trans_by_reader($department_id = '')
        {
            $arr_employee = array();

            $session_site_id = get_session_site_id();

            $this->db->select('r.*, ert.employee_id');
            $this->db->from('reader as r');
            $this->db->join('employee_reader_trans as ert', 'r.reader_id = ert.reader_id', 'left');
            $this->db->where('r.site_id', $session_site_id);
            if (!empty($department_id))
            {
                $this->db->where('r.department_id', $department_id);
            }
            $this->db->order_by('r.name');
            $query = $this->db->get();

            foreach ($query->result_array() as $row)
            {
                if (array_key_exists($row['reader_id'], $arr_employee))
                {
//                    p("here",0);
//                    p($arr_employee[$row['employee_id']],0);
                    array_push($arr_employee[$row['reader_id']]["reader_trans"], $row['employee_id']);
//                    p($arr_employee[$row['employee_id']]);
                }
                else
                {
//                    p("there",0);
                    $arr_employee[$row['reader_id']] = array(
                        "reader_trans" => empty($row['employee_id']) ? array() : array($row['employee_id'])
                    );
                }
            }

            return $arr_employee;
        }

        public function get_total_employees($site_id, $employee_filter = '')
        {
            $return = NULL;
            if (!empty($site_id))
            {
                $this->db->select('e.*, count(face.fid) as fcount,(SELECT COUNT(*) FROM employee_fp as efp WHERE employee_id=e.employee_id) as fpcount');
                $this->db->from('employee as e');
                $this->db->join('employee_face as face', 'e.employee_id = face.employee_id', 'left');
                $this->db->where('e.site_id', $site_id);
                if ($employee_filter != 'All' && $employee_filter != "")
                {
                    $this->db->where('e.status', $employee_filter);
                }
                // $this->db->order_by('e.pin');
                $this->db->group_by('e.employee_id');
                $return = $this->db->get()->result_array();
            }

            return $return;
        }

        public function get_total_employees_with_0_fps($site_id, $employee_filter = '')
        {
            $return = NULL;
            if (!empty($site_id))
            {
                $this->db->select('e.*, count(fp.fid) as fpcount');
                $this->db->from('employee as e');
                $this->db->join('employee_fp as fp', 'e.employee_id = fp.employee_id', 'left');
                $this->db->where('e.site_id', $site_id);
                if ($employee_filter != 'All' && $employee_filter != "")
                {
                    $this->db->where('e.status', $employee_filter);
                }
                $this->db->group_by('e.employee_id');
                $this->db->having('count(fp.fid)', 0);
                $this->db->order_by('e.pin');
                $return = $this->db->get()->result_array();
//                p($this->db->last_query());
            }
            return $return;
        }

        public function get_total_employees_with_0_rfcard($site_id, $employee_filter = '')
        {
            $return = NULL;
            $rf_card_check = array('[0000000000]', '0', '');
            if (!empty($site_id))
            {
                $this->db->select('e.*');
                $this->db->from('employee as e');
                $this->db->where('e.site_id', $site_id);
                if ($employee_filter != 'All' && $employee_filter != "")
                {
                    $this->db->where('e.status', $employee_filter);
                }
                $this->db->where_in('e.card', $rf_card_check);
                $this->db->order_by('e.pin');
                $return = $this->db->get()->result_array();
//                p($this->db->last_query());
            }
            return $return;
        }

        public function import_employees($dataValue)
        {
            $return = null;

            if (!empty($dataValue))
            {
                //check if pin exists
                $this->db->select('e.*');
                $this->db->from('employee as e');
                $this->db->where('e.pin', $dataValue['pin']);
                $this->db->where('e.site_id', $dataValue['site_id']);
                $emp_data = $this->db->get()->row_array();

                if (!empty($emp_data))
                {
                    $this->db->where('pin', $dataValue['pin']);
                    $this->db->where('site_id', $dataValue['site_id']);
                    $this->db->update('employee', $dataValue);
                    $return = $emp_data['employee_id'];
                }
                else
                {
                    $this->db->insert('employee', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function get_employees_not_assoc_with_any_reader($site_id, $status = '')
        {

            $return = NULL;
            if (!empty($site_id))
            {
                $this->db->select('e.*,(SELECT COUNT(*) FROM employee_fp as efp WHERE employee_id=e.employee_id) as fpcount');
                $this->db->from('employee as e');
                $this->db->join('employee_reader_trans as rt', 'e.employee_id = rt.employee_id', 'left');
                $this->db->where('e.site_id', $site_id);
                if ($status != '')
                {
                    $this->db->where('e.status', $status);
                }
                $this->db->where('rt.employee_id IS NULL');
                $this->db->order_by('pin');

                $return = $this->db->get()->result_array();
            //    p($this->db->last_query());
            }
            return $return;
        }

        public function delete_employee_trans_by_id($employee_id)
        {
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee_reader_trans');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_total_employees_per_reader($reader_id, $employee_filter = '')
        {
            $return = NULL;
            if (!empty($reader_id))
            {
                $this->db->select('e.*');
                $this->db->from('employee_reader_trans as reader');
                $this->db->join('employee as e', 'e.employee_id = reader.employee_id','left');
                $this->db->where('reader.reader_id', $reader_id);
                if ($employee_filter != 'All' && $employee_filter != "")
                {
                    $this->db->where('e.status', $employee_filter);
                }
                $this->db->group_by('e.employee_id');
                $this->db->order_by('e.pin');
                $return = $this->db->get()->result_array();
            }
            
         //  p($this->db->last_query());
          //p($return);
            return $return;
        }

        public function get_fp_count_by_employee_id($employee_id)
        {
            $session_site_id = get_session_site_id();

            $return = NULL;
            if (!empty($employee_id))
            {
                $this->db->select('e.*, count(fp.fid) as fpcount');
                $this->db->from('employee as e');
                $this->db->join('employee_fp as fp', 'e.employee_id = fp.employee_id', 'left');
                $this->db->where('e.site_id', $session_site_id);
                $this->db->where('e.employee_id', $employee_id);
                $this->db->group_by('e.employee_id');
                $return = $this->db->get()->row_array();
//                p($this->db->last_query());
            }
            return $return['fpcount'];
        }

        public function get_duplicate_fp_templates()
        {
            $session_site_id = get_session_site_id();

            $return = NULL;
            $this->db->select('data');
            $this->db->from('employee_fp as e');
            $this->db->join('employee as em', 'e.employee_id = em.employee_id', 'left');
//            $this->db->where('em.site_id', $session_site_id);
            $this->db->group_by('e.data  ');
            $this->db->having('COUNT(*) >= 2 ');
            $return = $this->db->get()->result_array();
//                p($this->db->last_query());
            return $return;
        }

        public function get_employee_with_duplicate_fp_templates($template)
        {
            $return = NULL;
            $this->db->select('e.*, em.*');
            $this->db->from('employee_fp as e');
            $this->db->join('employee as em', 'e.employee_id = em.employee_id', 'inner');
            $this->db->where('e.data', $template);
            $return = $this->db->get()->result_array();
//                p($this->db->last_query());
            return $return;
        }

        public function employees_status_update($dataValue)
        {
            if (!empty($dataValue['employee_id']))
            {
                $this->db->where('employee_id', $dataValue['employee_id']);
                $this->db->update('employee', $dataValue);
                $return = $dataValue['employee_id'];
            }

            return $return;
        }

        public function check_employees_to_reader($employee_id)
        {
            $return = NULL;
            if (!empty($employee_id))
            {
                $this->db->select('e.*');
                $this->db->from('employee_reader_trans as e');
                $this->db->where('e.employee_id', $employee_id);

                $return = $this->db->get()->result_array();
            }
            // p($this->db->last_query());
            return $return;
        }

        public function delete_employee_fp_by_id($fp_id)
        {
            $fp_id = urldecode($fp_id);
            $fp_data = array();
            if (strpos($fp_id, '|') !== false)
            {
                $ids = explode('|', $fp_id);
                array_pop($ids);
                foreach ($ids as $key => $id)
                {
                    $data = $this->get_employee_fp_by_id($id);
                    $fp_data[$key]['pin'] = $data['pin'];
                    $fp_data[$key]['fid'] = $data['fid'];
                    $fp_data[$key]['employee_id'] = $data['employee_id'];
                }
                $this->db->where_in('id', $ids);
            }
            else
            {
                $data = $this->get_employee_fp_by_id($fp_id);
                $fp_data[0]['pin'] = $data['pin'];
                $fp_data[0]['fid'] = $data['fid'];
                $fp_data[0]['employee_id'] = $data['employee_id'];
                $this->db->where('id', $fp_id);
            }
            $res = $this->db->delete('employee_fp');

            //remove fps from reader
            remove_fps_from_reader($fp_data);

            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function delete_employee_fp_by_employee_id($employee_id)
        {
            //remove fps from reader
            $fp_data = $this->get_employee_fp_by_employee_id($employee_id);
            remove_fps_from_reader($fp_data);

            //remove from datatbase/server
            $this->db->where('employee_id', $employee_id);
            $res = $this->db->delete('employee_fp');

            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_employee_fp_by_id($id)
        {
            $return = NULL;
            if (!empty($id))
            {
                $this->db->select('fp.fid, e.*');
                $this->db->from('employee_fp as fp');
                $this->db->join('employee as e', 'e.employee_id = fp.employee_id', 'inner');
                $this->db->where('id', $id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function get_total_readers_per_employee($employee_id)
        {
            $return = NULL;
            if (!empty($employee_id))
            {
                $this->db->select('reader.reader_id');
                $this->db->from('employee_reader_trans as reader');
                $this->db->where('reader.employee_id', $employee_id);
                $this->db->group_by('reader.reader_id');
                $return = $this->db->get()->result_array();
            }
//          p($this->db->last_query());
            return $return;
        }

        public function get_employee_fp_by_employee_id($employee_id)
        {
            $return = NULL;
            if (!empty($employee_id))
            {
                $this->db->select('fp.fid, e.*');
                $this->db->from('employee_fp as fp');
                $this->db->join('employee as e', 'e.employee_id = fp.employee_id', 'inner');
                $this->db->where('fp.employee_id', $employee_id);
                $return = $this->db->get()->result_array();
            }
            return $return;
        }

        public function get_employees_by_site_id($site_id)
        {
            $data = array();
            $this->db->select('employee_id');
            $this->db->from('employee');
            $this->db->where('site_id', $site_id);
            $return = $this->db->get()->result_array();
            return $return;
        }

        public function save_employee_before_delete($employee_id)
        {
            $return = $this->db->query("insert into deleted_employee   select * from employee where employee_id='" . $employee_id . "'");
            return $return;
        }
        
    }
    