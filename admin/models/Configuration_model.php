<?php

    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Configuration_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        function save_web_config($arr_user)
        {
            
            $return = null;
            if (isset($arr_user['id']))
            {
                $this->db->where('id', $arr_user['id']);
                $this->db->update('preferences', $arr_user);
                $return = $arr_user['id'];
                 
            }
            else
            {
                $this->db->insert('preferences', $arr_user);
                $return = $this->db->insert_id();
                 
            }

            return $return;
        }

        public function get_web_config()
        {
            $this->db->select('wc.*');
            $this->db->from('preferences as wc');           
            $return = $this->db->get()->row_array();
            return $return;
        }

    }
    