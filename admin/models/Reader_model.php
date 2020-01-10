<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Reader_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_reader($pagingParams = array())
        {
            $session_site_id = get_session_site_id();
            $seconds = get_reader_offline_timeout();
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('r.*, d.name as department');
            //$this->db->select('IF(TIME_TO_SEC(TIMEDIFF(NOW(), seen)) >  900, "Offline", "Online") AS onlinestatus ');
            $this->db->select('IF(TIMESTAMPDIFF(SECOND, r.seen, NOW()) > ' . $seconds . " OR (r.seen IS NULL AND r.`site_id` IS NOT NULL )" . ' , "Offline", "Online") AS onlinestatus ');


            $this->db->from('reader as r');
            $this->db->join('department as d', 'r.department_id = d.department_id', 'left');
            $this->db->where('r.site_id', $session_site_id);
            if (!empty($pagingParams['department_id']))
            {
                $this->db->where('r.department_id', $pagingParams['department_id']);
            }
//            $this->db->where('r.department_id', $session_data['department_id']);

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
                $this->db->like('sn', $search);
                $this->db->or_like('r.name', $search);
                $this->db->or_like('d.name', $search);
            }
            $return = $this->get_with_count(NULL, empty($pagingParams['records_per_page']) ? null : $pagingParams['records_per_page'], empty($pagingParams['offset']) ? null : $pagingParams['offset']);
            return $return;
        }

        public function get_all_reader_by_department($pagingParams = array())
        {
            $session_site_id = get_session_site_id();
            //$session_data = get_loggedin_admin_user_data();
            // p($session_data);
            $seconds = get_reader_offline_timeout();
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('r.*, d.name as department');
            //$this->db->select('IF(TIME_TO_SEC(TIMEDIFF(NOW(), seen)) >  900, "Offline", "Online") AS onlinestatus ');
            $this->db->select('IF(TIMESTAMPDIFF(SECOND, r.seen, NOW()) > ' . $seconds . " OR (r.seen IS NULL AND r.`site_id` IS NOT NULL )" . ' , "Offline", "Online") AS onlinestatus ');


            $this->db->from('reader as r');
            $this->db->join('department as d', 'r.department_id = d.department_id', 'left');
            $this->db->where('r.site_id', $session_site_id);
            if (!empty($pagingParams['department_id']))
            {
                $this->db->where('r.department_id', $pagingParams['department_id']);
            }

            //$this->db->where('r.department_id', $session_data['department_id']);

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
                $this->db->like('sn', $search);
                $this->db->or_like('r.name', $search);
                $this->db->or_like('d.name', $search);
            }
            $return = $this->get_with_count(NULL, empty($pagingParams['records_per_page']) ? null : $pagingParams['records_per_page'], empty($pagingParams['offset']) ? null : $pagingParams['offset']);
            //p($this->db->last_query());
            return $return;
        }

        public function get_reader_by_id($reader_id)
        {
            $return = NULL;
            if (!empty($reader_id))
            {
                $this->db->select('r.*');
                $this->db->from('reader as r');
                $this->db->where('reader_id', $reader_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_reader($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['reader_id']))
                {
                    $this->db->where('reader_id', $dataValue['reader_id']);
                    $this->db->update('reader', $dataValue);
                    $return = $dataValue['reader_id'];
                }
                else
                {
                    $this->db->insert('reader', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_reader_by_id($reader_id)
        {
            $this->db->trans_start();

            //delete from reader_commands
            $this->db->where('reader_id', $reader_id);
            $res = $this->db->delete('reader_command');

            //delete from reader_commands_history
            $this->db->where('reader_id', $reader_id);
            $res = $this->db->delete('reader_command_history');

            //delete from reader_commands_history_unsuccessful
            $this->db->where('reader_id', $reader_id);
            $res = $this->db->delete('reader_command_history_unsuccessful');

            //delete from reader
            $this->db->where('reader_id', $reader_id);
            $res = $this->db->delete('reader');
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

        public function get_reader_array($fieldname = 'sn')
        {
            $session_site_id = get_session_site_id();

            $data = array();
            $this->db->select('*');
            $this->db->from('reader');
//            $this->db->order_by($fieldname);
            $this->db->where('site_id', $session_site_id);
            $this->db->order_by('order_id');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['reader_id']] = $row[$fieldname];
            }
            return $data;
        }

        public function save_reader_command($reader_control, $command)
        {
            $return = null;
            if (!empty($command))
            {
                //save command
                $sourceinfo = get_custom_config_item('sourceinfo');
                $data_arr = array(
                    'reader_id' => $reader_control['reader_id'],
                    'command' => $command,
                    'status' => 'Active',
                    'sourceinfo' => $sourceinfo,
                    'ip_address' => get_ip_address()
                );
                $return = save_reader_command($data_arr);
            }
//            p($this->db->last_query());
            return $return;
        }

        public function get_readers_by_site_id($site_id)
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('reader');
            $this->db->where('site_id', $site_id);
            $this->db->order_by('order_id');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['reader_id']] = $row['name'];
            }
//            p($this->db->last_query());
            return $data;
        }

        public function get_offline_readers($site_id)
        {
            $return = NULL;
            $seconds = get_reader_offline_timeout();
            $this->db->select('r.*, s.name as site_name');
            $this->db->from('reader as r');
            $this->db->join('site as s', 's.site_id = r.site_id', 'left');
            if (!empty($site_id))
            {
                $this->db->where('r.site_id', $site_id);
            }
            $this->db->where('TIMESTAMPDIFF(SECOND, r.seen, NOW()) > ' . $seconds);
            $this->db->order_by('order_id');
            $return = $this->db->get()->result_array();
            return $return;
        }

        public function setorder($id, $order)
        {
            $arr = array(
                "order_id" => $order,
            );
            $this->db->where('reader_id', $id);
            $this->db->update('reader', $arr);
        }

        public function get_reader_array_by_order_id()
        {
            $session_site_id = get_session_site_id();

            $data = array();
            $this->db->select('*');
            $this->db->from('reader');
            $this->db->where('site_id', $session_site_id);
            $this->db->order_by('order_id');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['reader_id']] = $row['name'];
            }
            return $data;
        }

        public function online_offline_reader()
        {
            $return = NULL;
            $seconds = get_reader_offline_timeout();

            $this->db->select('s.*, count(r.reader_id) as total_reader,
                SUM(CASE  WHEN TIMESTAMPDIFF(SECOND,r.seen, NOW()) > ' . $seconds . ' OR (r.seen IS NULL AND r.`site_id` IS NOT NULL ) THEN 1  ELSE 0  END) AS offline,
                SUM(CASE  WHEN TIMESTAMPDIFF(SECOND,r.seen, NOW()) <= ' . $seconds . ' THEN 1  ELSE 0  END) AS online');
            $this->db->from('site as s');
            $this->db->join('reader as r', 's.site_id = r.site_id', 'left');
            $this->db->order_by('s.site_id');
            $this->db->group_by('s.site_id');
            $return = $this->db->get()->result_array();
//            p($this->db->last_query());
            return $return;
        }

        public function get_all_reader_data($site_id)
        {


            $this->db->select('reader_id,sn,name');
            $this->db->from('reader');
            if (!empty($site_id))
            {
                $this->db->where('site_id', $site_id);
            }
            $this->db->order_by('order_id');
            $return = $this->db->get()->result_array();
            return $return;
        }

        public function get_reader_search_details($name, $sn)
        {
            $this->db->select('r.*, s.name as site_name');
            $this->db->from('reader as r');
            $this->db->join('site as s', 's.site_id = r.site_id', 'left');
            if (!empty($name))
            {
                $this->db->where('r.name', $name);
                if (!empty($sn))
                {
                    $this->db->or_where('r.sn', $sn);
                }
            }
            if (!empty($sn))
            {
                $this->db->where('r.sn', $sn);
                if (!empty($name))
                {
                    $this->db->or_where('r.name', $name);
                }
            }
            $this->db->order_by('order_id');
            $return = $this->db->get()->result_array();
            return $return;
        }

        public function get_readers_by_site_id_new($site_id)
        {
            $data = array();
            $this->db->select('reader_id');
            $this->db->from('reader');
            $this->db->where('site_id', $site_id);
            $return = $this->db->get()->result_array();
            return $return;
        }

        public function remote_reader_unlock($site_id)
        {
            $return = NULL;
            $seconds = get_reader_offline_timeout();
            $this->db->select('a_m.*, r.name as reader_name, r.sn as r_sno,a.name as login_user');
            $this->db->from('access_manual_control_command as a_m');
            $this->db->join('reader as r', 'r.reader_id = a_m.reader_id', 'left');
            $this->db->join('admin as a', 'a.admin_id = a_m.login_id', 'left');
            if (!empty($site_id))
            {
                $this->db->where('r.site_id', $site_id);
            }
            $this->db->order_by('command_id');
            $return = $this->db->get()->result_array();
            return $return;
        }

    }
    