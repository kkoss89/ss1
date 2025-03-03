<?php
defined('BASEPATH') or exit('No direct script access allowed');

class blacklist_email_model extends MY_Model
{

    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = BLACKLIST_EMAIL;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = app_config('config')['search']['blacklist_email'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'list-items') {
            $this->db->select('id, ids, email, description, created, status');
            $this->db->from($this->tb_main);
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $column = $params['search']['field'];
                $this->db->like($column, $params['search']['query']);
            }

            $this->db->order_by('id', 'DESC');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }

            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $this->db->select('ub.id');
            $this->db->from($this->tb_main . " ub");

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $pre_column = 'ub.';
                if (in_array($params['search']['field'], ['first_name', 'last_name', 'email'])) {
                    $pre_column = 'u.';
                }
                $column = $pre_column . $params['search']['field'];
                $this->db->like($column, $params['search']['query']);
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'get-item') {
            $result = $this->get("id, ids, email, description, status", $this->tb_main, ['ids' => $params['ids']], '', '', true);
        }

        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        if ($option['task'] == 'delete-item') {
            $item = $this->get("id, ids", $this->tb_main, ['ids' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["ids" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted successfully',
                    "ids" => $item->ids,
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        if (in_array($option['task'], ['add-item', 'edit-item'])) {
            $data = array(
                "email"       => post('email'),
                "description" => post('description'),
                "status"      => post('status'),
            );
        }
        
        switch ($option['task']) {

            case 'add-item':
                $data['ids'] = ids();
                $this->db->insert($this->tb_main, $data);
                return ["status" => "success", "message" => 'Added successfully'];
                break;

            case 'edit-item':
                $this->db->update($this->tb_main, $data, ["ids" => post('ids')]);
                return ["status" => "success", "message" => 'Updated successfully'];
                break;

            case 'bulk-action':
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status" => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->delete($this->tb_main);
                        return ["status" => "success", "message" => 'Deleted successfully'];
                        break;

                    case 'empty':
                        $this->db->from($this->tb_main);
                        $this->db->truncate();
                        return ["status" => "success", "message" => 'Empty all successfully'];
                        break;

                    case 'deactive':
                        // Category
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 0]);
                        return ["status"  => "success", "message" => 'Updated successfully'];
                        break;

                    case 'active':
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 1]);
                        return ["status"  => "success", "message" => 'Updated successfully'];
                        break;
                }
                break;
        }
    }

}
