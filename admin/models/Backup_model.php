<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Backup_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function downloadbackup()
        {
            $return = NULL;
            $truncate = $this->db->query("truncate employee_reader_trans_backup");
            $return = $this->db->query("insert into employee_reader_trans_backup (employee_id,reader_id,created_at) select employee_id,reader_id,created_at from employee_reader_trans");          
            return $return;
        }
        
        public function restorebackup()
        {
            $return = NULL;
            $truncate = $this->db->query("truncate employee_reader_trans");
            $return = $this->db->query("insert into employee_reader_trans (employee_id,reader_id,created_at) select employee_id,reader_id,created_at from employee_reader_trans_backup");          
            return $return;
        }

    }
    