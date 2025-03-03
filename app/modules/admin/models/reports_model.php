<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class reports_model extends MY_Model 
{

    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main     = ORDER;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = [];
    }

    public function get_data_analytic($params = null, $option = null)
    {
        $result = null;
        $where = null;
        if ($option['task'] == 'payments') {
            $table = $this->tb_transaction_logs;
            $select = 'SUM(amount)';
            $where = ['status' => 1];
        }
        if ($option['task'] == 'orders') {
            $table = $this->tb_order;
            $select = 'count(id)';
        }
        if ($option['task'] == 'tickets') {
            $table = $this->tb_tickets;
            $select = 'count(id)';
        }
        if ($option['task'] == 'profits') {
            $table = $this->tb_order;
            $select = 'SUM(profit)';
            $where = ['is_drip_feed !=' => 1];
            $this->db->where_in('status', ['completed', 'partial']);
        }
        $this->db->select($select . ' as total');
        $this->db->select('DAY(created) as _day, MONTH(created) as _month, YEAR(created) as _year');
        $this->db->from($table);
        $this->db->where('YEAR(created) >= YEAR(CURRENT_TIMESTAMP)');
        if ($where) {
            $this->db->where($where);
        }
        $this->db->group_by('YEAR(created), MONTH(created), DAY(created)');
        $query  = $this->db->get();
        $result_array = $query->result_array();
        if ($result_array) {
            foreach ($result_array as $key => $item) {
                $datetime = $item['_year']. "-" . $item['_month'] . "-" . $item['_day'];
                $result[$datetime] = $item['total'];
            }
        }
        return $result; 
    }
}
