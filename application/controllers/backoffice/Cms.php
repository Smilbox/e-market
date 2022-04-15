<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Cms extends CI_Controller { 
    public $controller_name = 'cms';
    public $prefix = '_cms';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/cms_model');
    }
    public function view() {
        $data['meta_title'] = $this->lang->line('title_admin_cmspages').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();     
        $this->load->view(ADMIN_URL.'/cms',$data);
    }
    public function add() {
        $data['meta_title'] = $this->lang->line('title_admin_cmspagesadd').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {            
            $this->form_validation->set_rules('name', 'CMS Title', 'trim|required');
            $this->form_validation->set_rules('description', 'Content', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {
                if(!$this->input->post('content_id')){
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                      'content_type'=>$this->uri->segment('2'),
                      'created_by'=>$this->session->userdata("UserID"),  
                      'created_date'=>date('Y-m-d H:i:s')                      
                    );
                    $ContentID = $this->cms_model->addData('content_general',$add_content);
                    $CMSSlug = slugify($this->input->post('name'),'cms','CMSSlug');
                }else{                    
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->cms_model->getCmsSlug($this->input->post('content_id'));
                    $CMSSlug = $slug->CMSSlug;
                }
                $add_data = array(                   
                    'name'=>$this->input->post('name'),
                    'CMSSlug'=>$CMSSlug,
                    'description' =>$this->input->post('description'),
                    'content_id'=>$ContentID,
                    'language_slug'=>$this->uri->segment('4'),
                    'status'=>1,                  
                    'meta_title'=>$this->input->post('meta_title'),
                    'meta_keyword'=>$this->input->post('meta_keyword'),
                    'meta_description'=>$this->input->post('meta_description'),
                    'created_by'=>$this->session->userdata("UserID")
                );                
                if (!empty($_FILES['CMSImage']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/cms';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/cms')) {
                      @mkdir('./uploads/cms', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('CMSImage'))
                    {
                      $img = $this->upload->data();
                      $add_data['image'] = "cms/".$img['file_name'];    
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }   
                }                                               
                $this->cms_model->addData('cms',$add_data);
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');                 
            }
        }
        $this->load->view(ADMIN_URL.'/cms_add',$data);
    }
    public function edit() {
        $data['meta_title'] = $this->lang->line('title_admin_cmspagesedit').' | '.$this->lang->line('site_title');
        //check add role form is submit
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name', 'CMS Title', 'trim|required');
            $this->form_validation->set_rules('description', 'Content', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {
                $edit_data = array(
                    'name'=>$this->input->post('name'),
                    'description' =>$this->input->post('description'),           
                    'meta_title'=>$this->input->post('meta_title'),
                    'meta_keyword'=>$this->input->post('meta_keyword'),
                    'meta_description'=>$this->input->post('meta_description'),
                    'updated_by'=>$this->session->userdata("UserID"),
                    'updated_date'=>date('Y-m-d h:i:s')
                );
                if (!empty($_FILES['CMSImage']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/cms';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/cms')) {
                      @mkdir('./uploads/cms', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('CMSImage'))
                    {
                      $img = $this->upload->data();
                      $edit_data['image'] = "cms/".$img['file_name'];  
                      // code for delete existing image
                      if($this->input->post('uploadedCms_image')){
                        @unlink(FCPATH.'uploads/'.$this->input->post('uploadedCms_image'));
                      }  
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }   
                }                
                //$this->cms_model->editCMSPageModel($editCMSData,$this->input->post('CMSID'));
                $this->cms_model->updateData($edit_data,'cms','entity_id',$this->input->post('entity_id'));
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');             
            }
        }        
        $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
        $data['edit_records'] = $this->cms_model->getEditDetail($entity_id);
        $this->load->view(ADMIN_URL.'/cms_add',$data);
    }
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(4=>'status',5=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->cms_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '')?$displayStart+1:1;
        /*foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                ($val->status)?'Active':'Deactive',
                '<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i> Edit</a> <button onclick="deleteDetail('.$val->entity_id.')"  title="Click here for delete" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> Delete</button> <button onclick="disableDetail('.$val->entity_id.','.$val->status.')"  title="Click here for '.($val->status?'Deactivate':'Activate').' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($val->status?'times':'check').'"></i> '.($val->status?'Deactivate':'Activate').'</button>'
            );
            $nCount++;
        }  */ 
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '<button onclick="deleteAll('.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>';
            $edit_active_access .='<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($value['status']?'times':'check').'"></i> '.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'</button>';
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                ($value['status'])?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>
                    <a style="cursor:pointer;" onclick="disableDetail('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>
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
        $this->cms_model->ajaxDelete('cms',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $this->cms_model->ajaxDeleteAll('cms',$content_id);
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->cms_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
        }
    }
    /*
     * Update status for All
     */
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->cms_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
        }
    }
}