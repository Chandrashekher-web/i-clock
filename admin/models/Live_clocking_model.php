<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Live_clocking_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_live_clocking($conditions_arr = array())
        {
            $return = NULL;

            if (!empty($conditions_arr))
            {
                if (!empty($conditions_arr['from_date']))
                {
                    $this->db->where("att.clock >='" . date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $conditions_arr['from_date']))) . "'");
                }
                if (!empty($conditions_arr['to_date']))
                {
                    $this->db->where("att.clock <='" . date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', $conditions_arr['to_date']))) . "'");
                }
                if (!empty($conditions_arr['employee_id']))
                {
                    $this->db->where("e.employee_id ='" . $conditions_arr['employee_id'] . "'");
                }
                if (!empty($conditions_arr['reader_id']))
                {
                    $this->db->where("att.reader_id ='" . $conditions_arr['reader_id'] . "'");
                }
                if (!empty($conditions_arr['attendance_id']))
                {
                    $this->db->where("att.attendance_id >'" . $conditions_arr['attendance_id'] . "'");
                }
                if (!empty($conditions_arr['department_id']))
                {
                    $this->db->where("r.department_id= '". $conditions_arr['department_id'] . "'");
                }
            }
            else
            {
                $this->db->like("att.clock", date('Y-m-d'));
            }
            $session_site_id = get_session_site_id();
            $this->db->select('att.attendance_id,att.reader_id,e.name as emp_name,e.employee_id,e.pin,date_format(att.clock,"%d/%m/%Y") as dt,date_format(att.clock,"%H:%i:%s") as t,r.sn as sn,r.name as reader,e.access_group');
            $this->db->from('attendance as att');
            $this->db->join('employee as e', 'att.employee_pin = e.pin', 'left');
            $this->db->join('reader as r', 'att.reader_id = r.reader_id', 'left');
            $this->db->where('att.is_remove', 'No');
            $this->db->where('e.site_id', $session_site_id);

            $this->db->order_by("att.attendance_id", "DESC");
            $return = $this->db->get()->result_array();
           // p($this->db->last_query());
            // p($return);
            return $return;
        }

        public function remove_clocking($clocking_id)
        {
            $return = null;
            if (!empty($clocking_id))
            {
                $this->db->where('attendance_id', $clocking_id);
                $this->db->update('attendance', array('is_remove' => 'Yes'));
                $return = $dataValue['$clocking_id'];
            }
            return $return;
        }

    }
    