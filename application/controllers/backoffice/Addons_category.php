<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Addons_category extends CI_Controller { 
    public $controller_name = 'addons_category';
    public $prefix = 'acg';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/addons_category_model');
    }
    //view data
    public function view() {
        $data['meta_title'] = $this->lang->line('title_category').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();     
        $this->load->view(ADMIN_URL.'/addons_category',$data);
    }
    //add data
    public function add() {
        $data['meta_title'] = $this->lang->line('title_category_add').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name', 'Category Name', 'trim|required');
            if ($this->form_validation->run())
            {
                if(!$this->input->post('content_id')){
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                      'content_type'=>$this->uri->segment('2'),
                      'created_by'=>$this->session->userdata("UserID"),  
                      'created_date'=>date('Y-m-d H:i:s')                      
                    );
                    $ContentID = $this->addons_category_model->addData('content_general',$add_content);
                }else{                    
                    $ContentID = $this->input->post('content_id');
                }
                $add_data = array(                   
                    'name'=>$this->input->post('name'),
                    'content_id'=>$ContentID,
                    'language_slug'=>$this->uri->segment('4'),
                    'status'=>1,
                    'created_by' => $this->session->userdata('UserID')
                ); 
                $this->addons_category_model->addData('add_ons_category',$add_data); 
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
            }
        }
        $this->load->view(ADMIN_URL.'/addons_category_add',$data);
    }
    //edit data
    public function edit() {
        $data['meta_title'] = $this->lang->line('title_category_edit').' | '.$this->lang->line('site_title');
        //check add form is submit
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name', 'Category Name', 'trim|required');
            if ($this->form_validation->run())
            {
                $updateData = array(                   
                    'name'=>$this->input->post('name'),
                    'updated_date'=>date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID')
                ); 
                $this->addons_category_model->updateData($updateData,'add_ons_category','entity_id',$this->input->post('entity_id'));
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                  
            }
        }        
        $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
        $data['edit_records'] = $this->addons_category_model->getEditDetail($entity_id);
        $this->load->view(ADMIN_URL.'/addons_category_add',$data);
    }
    //ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'name','2'=>'status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->addons_category_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '<button onclick="deleteAll('.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>';
            $edit_active_access .='<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($value['status']?'times':'check').'"></i> '.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'</button>';
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>
                    <a style="cursor:pointer;" onclick="disable_record('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>
                    <a style="cursor:pointer;" onclick="deleteDetail('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'"><i class="fa fa-times"></i> </a>
                    ( '.$value['translations'][$lang->language_slug]['name'].' )';
                }else{
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }          
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method for deleting a category
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->addons_category_model->ajaxDelete('add_ons_category',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $this->addons_category_model->ajaxDeleteAll('add_ons_category',$content_id);
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->addons_category_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
        }
    }
    /*
     * Update status for All
     */
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->addons_category_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
        }
    }
}