<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class blog_posts extends My_AdminController
{

    private $tb_main = BLOG_POSTS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "blog_posts";
        $this->params = [];

        $this->columns = [];
        $this->load->model('blog_category_model', 'blog_category_model');
        $this->load->model('language_model', 'language_model');
    }

    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $page        = (int)get("p");
        $page        = ($page > 0) ? ($page - 1) : 0;
        $limit_per_page = 50;
        $this->params = [
            'sort_by' => ['cate_id' => (isset($_GET['sort_by'])) ? (int)get('sort_by') : ''],
            'search'  => ['query'   => get('query'), 'field' => get('field')],
        ];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $items_category = $this->blog_category_model->list_items($this->params, ['task' => 'list-items-in-blog-post']);
        $items_lang = $this->language_model->list_items(['status' => 1], ['task' => 'list-items-by-params']);
        $language_name = '';
        if ($items_lang) {
            foreach ($items_lang as $key => $item_lang) {
                $language_name .= '<span class="m-r-10 flag-icon flag-icon-' . strtolower($item_lang['country_code']) . '"></span>';
            }
        }
        $this->columns = array(
            "name"     => ['name' => 'Name', 'class' => ''],
            "status"   => ['name' => 'Status', 'class' => 'text-center'],
            "language" => ['name' => $language_name, 'class' => 'text-center'],
            "released" => ['name' => 'Released', 'class' => 'text-center'],
            "changed"  => ['name' => 'Last Changed', 'class' => 'text-center'],
            "created"  => ['name' => 'Created', 'class' => 'text-center'],
        );


        $data = array(
            "controller_name"     => $this->controller_name,
            "params"              => $this->params,
            "columns"             => $this->columns,
            "items"               => $items,
            "items_lang"          => $items_lang,
            "items_category"      => $items_category,
        );
        $this->template->build($this->path_views . '/index', $data);
    }

    public function update($id = null)
    {
        $item              = null;
        $items_lang        = null;
        $item_post_lang    = null;
        $item_default_lang = null;
        $task = 'add';
        $item = $this->main_model->get_item(['id' => (int)esc($id)], ['task' => 'get-item']);
        $items_category = $this->blog_category_model->list_items($this->params, ['task' => 'list-items-in-blog-post']);
        $items_lang = $this->language_model->list_items(['status' => 1], ['task' => 'list-items-by-params']);
        if ($items_lang) {
            foreach ($items_lang as $key => $item_lang) {
                if ($item_lang['is_default']) {
                    $item_default_lang = $item_lang;
                    break;
                }
            }
        }
        $lang_code = $item_default_lang['code'];
        if ($item) {
            staff_check_role_permission($this->controller_name, 'edit');
            $task = 'edit';
            $ref_lang = get('ref_lang');
            if ($ref_lang) {
                $lang_code = $ref_lang;
            }
            $item_post_lang  = $this->main_model->get_item(['post_id' => (int)esc($id), 'lang_code' => $lang_code], ['task' => 'get-item-post-lang']);
            if (!$item_post_lang) {
                $item_post_lang  = $this->main_model->get_item(['post_id' => (int)esc($id), 'lang_code' => $item_default_lang['code']], ['task' => 'get-item-post-lang']);
            }
        } else {
            staff_check_role_permission($this->controller_name, 'add');
        }
        $data = array(
            "item"                => $item,
            "item_post_lang"      => $item_post_lang,
            "items_category"      => $items_category,
            "lang_code"           => $lang_code,
            "item_default_lang"   => $item_default_lang,
            "items_lang"          => $items_lang,
            "task"                => $task,
            "lang_mode_note"      => sprintf('You are editing "<strong>%s</strong>" version', language_codes($lang_code)),
            "controller_name"     => $this->controller_name,
        );
        $this->template->build($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]|xss_clean');
        $this->form_validation->set_rules('image', 'Image thumbnail', 'trim|required');
        $this->form_validation->set_rules('category', 'Post ategory', 'trim|required');
        $this->form_validation->set_rules('content', 'Content', 'trim|required');
        $this->form_validation->set_rules('meta_keywords', 'Meta keywords', 'trim|xss_clean');
        $this->form_validation->set_rules('meta_description', 'Meta description', 'trim|xss_clean');
        $task = 'add-item';
        $id = post('id');
        $name_unique = "|is_unique[$this->tb_main.name]";
        if ($id) {
            staff_check_role_permission($this->controller_name, 'edit');
            $task = 'edit-item';
            $name_unique = "|edit_unique[$this->tb_main.name.$id]";
            $this->form_validation->set_rules('url_slug', 'Url slug', 'trim|required');
        } else {
            staff_check_role_permission($this->controller_name, 'add');
        }
        $this->form_validation->set_rules('name', 'name', 'trim|required|xss_clean' . $name_unique, [
            'is_unique' => 'The name already exists.',
        ]);
        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }
        $response = $this->main_model->save_item($this->params, ['task' => $task]);
        ms($response);
    }

    public function change_sort($id = "")
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }

        $params = [
            'id' => $id,
            'sort' => (int) post('sort'),
        ];
        $response = $this->main_model->save_item($params, ['task' => 'change-sort']);
        ms($response);
    }

}
