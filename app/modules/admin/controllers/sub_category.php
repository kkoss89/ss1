<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sub_category extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Loads the sub_category_model with alias "main_model"
        $this->load->model(get_class($this) . '_model', 'main_model');
    }

    // List subcategories for a given category (cate_id is passed as a parameter)
    public function index($cate_id = null) {
        $data['cate_id'] = $cate_id;
        $data['items'] = $this->main_model->list_items(['cate_id' => $cate_id], ['task' => 'list-items']);
        $this->render('sub_category/index', $data);
    }

    // Stores a new subcategory or updates an existing one.
    public function store() {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url('category'));
        }
        $id = post('id');
        $task = ($id) ? 'edit-item' : 'add-item';
        $response = $this->main_model->save_item(null, ['task' => $task]);
        ms($response); // Helper to return a JSON response
    }

    // Changes the sort order of a subcategory.
    public function change_sort($id = "") {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url('category'));
        }
        $params = [
            'id'   => $id,
            'sort' => (int) post('sort')
        ];
        $response = $this->main_model->save_item($params, ['task' => 'change-sort']);
        ms($response);
    }
    
    // Additional methods (such as delete) can be added as needed.
}


<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Sub_category extends Admin_Controller {

   public function __construct() {
    parent::__construct();
    // Load a dedicated model for subcategories
    $this->load->model('sub_category_model', 'main_model');
    // Optionally, check the logged-in staff permissions, etc.
   }

   public function index($cate_id = null) {
    // List subcategories of a given category
    $data['cate_id'] = $cate_id;
    $data['items'] = $this->main_model->list_items(['cate_id' => $cate_id], ['task' => 'list-items']);
    $this->render('sub_category/index', $data);
   }

   public function store() {
    if (!$this->input->is_ajax_request()) {
     redirect(admin_url('category'));
    }

    $id = post('id');
    $task = ($id) ? 'edit-item' : 'add-item';

    // You can add validations here as in your category controller
    $response = $this->main_model->save_item(null, ['task' => $task]);
    ms($response);
   }

   // Add similar methods for delete, change-sort, etc.
  }
  ?>
