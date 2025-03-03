<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class blog_model extends MY_Model {
    protected $tb_main;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main                = BLOG_POSTS;
        $this->tb_blog_post_lang      = BLOG_POSTS_LANG;
        $this->tb_blog_categories     = BLOG_CATEGORIES;
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $this->db->select('bp.id, bp.url_slug, bp.cate_id, bp.image, bp.released');
            $this->db->select('bpl.name, bpl.content, bpl.meta_keywords, , bpl.meta_description');
            $this->db->select('c.name as category_name, c.lang_name as category_lang_name');
            $this->db->select('c.url_slug as category_url_slug');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_categories . " c", "c.id = bp.cate_id", 'left');
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.status', 1);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            if (isset($params['category_url_slug'])) {
                $this->db->where('c.url_slug', $params['category_url_slug']);
            }
            // Sort By
            $this->db->order_by("bp.released", 'DESC');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-last-post') {
            $this->db->select('bp.id, bp.url_slug, bp.cate_id, bp.image, bp.released');
            $this->db->select('bpl.name, bpl.content, bpl.meta_keywords, , bpl.meta_description');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.id !=', $params['current_id_post']);
            $this->db->where('bp.status', 1);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            // Sort By
            $this->db->order_by("bp.released", 'DESC');
            if ($params['limit'] >= 0) {
                $this->db->limit($params['limit'], 0);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-related-post') {
            $this->db->select('bp.id, bp.url_slug, bp.cate_id, bp.image, bp.released');
            $this->db->select('bpl.name, bpl.content, bpl.meta_keywords, , bpl.meta_description');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.id !=', $params['current_id_post']);
            $this->db->where('bp.cate_id', $params['cate_id']);
            $this->db->where('bp.status', 1);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            // Sort By
            $this->db->order_by("bp.released", 'DESC');
            if ($params['limit'] >= 0) {
                $this->db->limit($params['limit'], 0);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }

        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item-in-new-order') {
            $result = $this->get('*', $this->tb_main, ['id' => $params['id'], 'status' => 1] ,'', '', true);
        }

        if ($option['task'] == 'item-detail') {
            $this->db->select('bp.id, bp.url_slug, bp.cate_id, bp.image, bp.released');
            $this->db->select('bpl.name, bpl.content, bpl.meta_keywords, , bpl.meta_description');
            $this->db->select('c.name as category_name, c.lang_name as category_lang_name');
            $this->db->select('c.url_slug as category_url_slug');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_categories . " c", "c.id = bp.cate_id", 'left');
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.status', 1);
            $this->db->where('bp.url_slug', $params['url_slug']);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            $this->db->order_by("bp.sort", 'ASC');
            $query = $this->db->get();
            $result = $query->row_array();
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $this->db->select('bp.id');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_categories . " c", "c.id = bp.cate_id", 'left');
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.status', 1);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            if (isset($params['category_name'])) {
                $this->db->where('c.name', $params['category_name']);
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }

        if ($option['task'] == 'count-items-by-category') {
            $this->db->select('bp.id, bp.cate_id');
            $this->db->select('c.name as category_name, c.lang_name as category_lang_name');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_categories . " c", "c.id = bp.cate_id", 'left');
            $this->db->join($this->tb_blog_post_lang . " bpl", "bp.id = bpl.post_id", 'left');
            $this->db->where('bp.status', 1);
            $this->db->where('bpl.lang_code', $params['lang_code']);
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result) {
                $result = group_by_criteria($result, 'category_name');
            }
        }
        return $result;
    }
   
}
