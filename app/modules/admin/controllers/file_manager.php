<?php
defined('BASEPATH') or exit('No direct script access allowed');

class file_manager extends My_AdminController
{
    public $tb_file_manage;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->tb_file_manage = FILE_MANAGER;
		$this->controller_name   = strtolower(get_class($this));
    }

    public function index()
    {
        redirect(cn());
    }

    public function upload_files()
    {
		if (!$this->input->is_ajax_request()) redirect(cn());
        get_upload_folder();
        $path = './assets/uploads/user' . sha1(session("uid"));
        $allowed_types = 'jpg|png';
        $max_size = 5 * 1024;
        $width = 1024;
        $height = 768;
        // config
        $config = array(
            'upload_path'   => $path,
            'allowed_types' => $allowed_types,
            'max_size'      => $max_size,
            'width'         => $width,
            'encrypt_name'  => true,
        );

        if (!empty($_FILES)) {
            $files = $_FILES;
            for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
                $_FILES['files']['name']     = $files['files']['name'][$i];
                $_FILES['files']['type']     = $files['files']['type'][$i];
                $_FILES['files']['tmp_name'] = $files['files']['tmp_name'][$i];
                $_FILES['files']['error']    = $files['files']['error'][$i];
                $_FILES['files']['size']     = $files['files']['size'][$i];

                // load libary;
                $this->load->library('upload', $config);

                $this->upload->initialize($config);
                if (!$this->upload->do_upload('files')) {
                    ms(array(
                        "status" => "error",
                        "message" => $this->upload->display_errors(),
                    ));
                } else {
                    $file_info = (object) $this->upload->data();
                    $data = array(
                        "ids" => ids(),
                        "uid" => session("uid"),
                        "file_name" => $file_info->file_name,
                        "file_type" => $file_info->file_type,
                        "file_size" => $file_info->file_size,
                        "is_image" => $file_info->is_image,
                        "image_width" => $file_info->image_width,
                        "image_height" => $file_info->image_height,
                        "file_ext" => str_replace(".", "", strtolower($file_info->file_ext)),
                        "created" => NOW,
                    );
                    $this->db->insert($this->tb_file_manage, $data);
                    ms(array(
                        "status" => "success",
                        "link" => get_link_file($file_info->file_name),
                        "ids" => $data["ids"],
                        "message" => lang('Upload_media_successfully'),
                    ));
                }
            }
        } else {
            pr("Error", 1);
        }
    }

    public function elfinder_init()
    {
        $data['connector'] = admin_url('file_manager/elfinder_connector');
        $this->template->set_layout('blank_page');
        $this->template->build($this->controller_name . '/elfinder', $data);
    }

    public function elfinder_connector()
    {
        $upload_folder = 'blogs';

        if ($upload_folder != "") {
            get_upload_folder($upload_folder);
            $path = APPPATH . "../assets/uploads/" . $upload_folder . "/";
            $link = BASE . "assets/uploads/" . $upload_folder . "/";
        } else {
            get_upload_folder();
            $path = './assets/uploads/user' . sha1(session("uid")) . '/';
            $link = BASE . 'assets/uploads/user' . sha1(session("uid")) . '/';
        }
        $opts = array(
            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem',
                    'path' => $path,
                    'URL' => $link,
                    'uploadDeny' => array('all'), // All Mimetypes not allowed to upload
                    'uploadAllow' => array('image', 'text/plain', 'application/pdf'), // Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder' => array('deny', 'allow'), // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => array($this, 'elfinderAccess'), // disable and hide dot starting files (OPTIONAL)
                    // more elFinder options here
                ),
            ),
        );
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    public function elfinderAccess($attr, $path, $data, $volume, $isDir, $relpath)
    {
        $basename = basename($path);
        return $basename[0] === '.' // if file/folder begins with '.' (dot)
         && strlen($relpath) !== 1// but with out volume root
         ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
         : null; // else elFinder decide it itself
    }
}
