<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Command_library_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_command($paging_params = array())
        {

            $this->db->select('rcl.*');
            $this->db->from('reader_command_library as rcl');
            $this->db->order_by('rcl.command_id', 'ASC');

            if (!empty($paging_params['order_by']))
            {

                if (empty($paging_params['order_direction']))
                {
                    $paging_params['order_direction'] = '';
                }

                switch ($paging_params['order_by'])
                {
                    default:
                        $this->db->order_by($paging_params['order_by'], $paging_params['order_direction']);
                        break;
                }
            }
            $search = $paging_params['search'];
            if (!empty($search))
            {

                $this->db->like('command', $search);
            }


            $return = $this->get_with_count(null, $paging_params['records_per_page'], $paging_params['offset']);
            return $return;
        }

        public function get_command_by_id($command_id)
        {

            $return = NULL;
            if (!empty($command_id))
            {
                $this->db->select('r.*');
                $this->db->from('reader_command_library as r');
                $this->db->where('command_id', $command_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_command_library($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['command_id']))
                {
                    $this->db->where('command_id', $dataValue['command_id']);
                    $this->db->update('reader_command_library', $dataValue);
                    $return = $dataValue['command_id'];
                }
                else
                {
                    $this->db->insert('reader_command_library', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_command_by_id($command_id)
        {
            $this->db->where('command_id', $command_id);
            $res = $this->db->delete('reader_command_library');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_reader_all_command()
        {

            $data = array();
            $this->db->select('command,command_description');
            $this->db->from('reader_command_library');
            $return = $this->db->get()->result_array();

            return $return;
        }

        public function save_command_for_execute($dataValue)
        {
            $sourceinfo = get_custom_config_item('sourceinfo');
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['command']) && count($dataValue['reader']) > 0)
                {
                    foreach ($dataValue['reader'] as $reader)
                    {
                        $rdata = array(
                            'reader_id' => $reader,
                            'command' => $dataValue['command'],
                            'status' => 'Active',
                            'sourceinfo' => $sourceinfo,
                            'ip_address' => get_ip_address()
                        );
                        $return = $this->save_reader_command_by_web($rdata);
                    }
                }
            }
            return $return;
        }

        public function save_reader_command_by_web($dataValue)
        {
            if (!empty($dataValue))
            {
                save_reader_command($dataValue);
                return $this->db->insert_id();
            }
        }
	
	 public function manual_access_control_command_for_execute($dataValue) {
        $login_id = $this->session->userdata['user_id'];
        $sourceinfo = get_custom_config_item('sourceinfo');
      
        $return = null;
        if (!empty($dataValue)) {
            if (!empty($dataValue['command']) && count($dataValue['reader']) > 0) {
                foreach ($dataValue['reader'] as $reader) {
                    $rdata = array(
                        'reader_id' => $reader,
                        'command' => $dataValue['command'],
                        'status' => 'Active',
                        'sourceinfo' => $sourceinfo,
                        'ip_address' => get_ip_address()
                    );
                    
                    $rdata1 = array(
                        'reader_id' => $reader,
                        'command' => $dataValue['command'],
                        'login_id' => $login_id,
                        'sourceinfo' => $sourceinfo,
                        'ip_address' => get_ip_address()
                    );
                    save_manual_access_control_command($rdata1);
                    $return = $this->save_reader_command_by_web($rdata);
                    
                }
            }
        }
        return $return;
    }

    }
    