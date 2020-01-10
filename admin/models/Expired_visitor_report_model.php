<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Expired_visitor_report_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_expired_visitor()
        {
            $return = NULL;
            $seconds = get_time_duration();


            $this->db->select('vac.visitors_name,vac.visitors_mobile_no,TIMESTAMPDIFF(SECOND,a.clock, NOW()) as time,(eva.name) as visitor_host,e.pin,vac.created_by as created_by,e.employee_id,e.property_number,date_format(vac.created_at ,"%d/%m/%Y") as dt,date_format(vac.created_at ,"%H:%i:%s") as t');
            $this->db->from('visitors_access_code as vac');
            $this->db->join('employee as e', 'vac.access_code = e.password', 'left');
            $this->db->join('attendance as a', 'a.employee_pin = e.pin', 'left');
            $this->db->join('employee as eva', 'vac.created_by = eva.employee_id', 'left');
            $this->db->where('vac.visit_multi_times', 'Yes');
            $this->db->where("a.clock <='" . date('Y-m-d H:i:s') . "'");
            $this->db->group_by('e.employee_id');
            $this->db->having("time > '" . $seconds['timed_access_duration'] . "'");
            $table1 = $this->db->get_compiled_select();
            $this->db->reset_query();


            $this->db->select('vac.visitors_name,vac.visitors_mobile_no,TIMESTAMPDIFF(SECOND,a.clock, NOW()) as time,(eva.name) as visitor_host,e.pin,vac.created_by as created_by,e.employee_id,e.property_number,date_format(vac.created_at ,"%d/%m/%Y") as dt,date_format(vac.created_at ,"%H:%i:%s") as t');
            $this->db->from('visitors_access_code as vac');
            $this->db->join('employee as e', 'vac.access_code = e.password', 'left');
            $this->db->join('attendance as a', 'a.employee_pin = e.pin', 'left');
            $this->db->join('employee as eva', 'vac.created_by = eva.employee_id', 'left');
            $this->db->where('vac.visit_multi_times', 'No');
            $this->db->where("a.clock <='" . date('Y-m-d H:i:s') . "'");
            $this->db->group_by('e.employee_id');
            $this->db->having("time > '" . $seconds['one_time_access_duration'] . "'");
            $table2 = $this->db->get_compiled_select();
            $this->db->reset_query();


            $return = $this->db->query("$table1 UNION $table2")->result_array();
            return $return;
        }

    }
    