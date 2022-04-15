<?php 
if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Promotion_settings extends CI_Controller {
    public $controller_name = 'promotion_settings';
    public function __construct() {
        parent::__construct();
            if (!$this->session->userdata('is_admin_login')) 
            {
                redirect(ADMIN_URL.'/home');
            }
            $this->load->library('form_validation');
            $this->load->model(ADMIN_URL.'/promotion_settings_model');
            $this->load->model(ADMIN_URL.'/common_model');
    }

    public function view(){
      $data['meta_title'] = $this->lang->line('promotion_settings').' | '.$this->lang->line('site_title');
      $data['Languages'] = $this->common_model->getLanguages();
      $data['banner_settings'] = $this->promotion_settings_model->getBannerSettingsById(1);
      $this->load->view(ADMIN_URL.'/promotion_settings',$data);
    }

    public function ajaxview() {
      $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
      $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
      $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
      $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
      $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
      
      $sortfields = array(1=>'res.name');
      $sortFieldName = '';
      if(array_key_exists($sortCol, $sortfields))
      {
          $sortFieldName = $sortfields[$sortCol];
      }
      //Get Recored from model
      $grid_data = $this->promotion_settings_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
      $totalRecords = $grid_data['total'];        
      $records = array();
      $records["aaData"] = array(); 
      $nCount = ($displayStart != '')?$displayStart+1:1;
      $cnt = 0;
      foreach ($grid_data['data'] as $key => $val) {
          $records["aaData"][] = array(
              $nCount,
              $val->name,
              $val->shown_promotion,
              $val->priority_order,
              $val->image,
              '<a class="btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a> <button onclick="deleteDetail('.$val->entity_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>'
          );
          $nCount++;
      }   
      $records["sEcho"] = $sEcho;
      $records["iTotalRecords"] = $totalRecords;
      $records["iTotalDisplayRecords"] = $totalRecords;
      echo json_encode($records);
  }

   //add data
   public function add() {
    $data['meta_title'] = $this->lang->line('promotion_settings').' | '.$this->lang->line('site_title');
    if($this->input->post('submit_page') == "Submit")
    {
      $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
      $this->form_validation->set_rules('shown_promotion', 'Display promotion', 'trim|required');
      $this->form_validation->set_rules('priority_order', 'Priority order', 'trim|required');
        if ($this->form_validation->run())
        {
            $add_data = array(                   
                'restaurant_id'=>$this->input->post('restaurant_id'),
                'shown_promotion'=>$this->input->post('shown_promotion'),
                'priority_order'=> intval($this->input->post('priority_order')),
                'updated_date'=>date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('UserID')
            ); 
            if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/promotion';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/')) {
                      @mkdir('./uploads/promotion', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $add_data['image'] = "promotion/".$img['file_name'];    
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
            if(empty($data['Error'])){
                $this->promotion_settings_model->addData('promotion_settings',$add_data); 
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');  
            }         
        }
    }
    $data['restaurants'] = $this->promotion_settings_model->getListData('restaurant',null);
    $this->load->view(ADMIN_URL.'/promotion_settings_add',$data);
}

    //edit data
    public function edit() {
        $data['meta_title'] = $this->lang->line('promotion_settings').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('shown_promotion', 'Display promotion', 'trim|required');
            $this->form_validation->set_rules('priority_order', 'Priority order', 'trim|required');
            if ($this->form_validation->run())
            {
                $updateData = array(                   
                    'restaurant_id'=>$this->input->post('restaurant_id'),
                    'shown_promotion'=>$this->input->post('shown_promotion'),
                    'priority_order'=> intval($this->input->post('priority_order')),
                    'updated_date'=>date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID')
                ); 
                
                if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/promotion';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/')) {
                      @mkdir('./uploads/promotion', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $updateData['image'] = "promotion/".$img['file_name'];    
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                
                if(empty($data['Error'])){
                    $this->promotion_settings_model->updateData($updateData,'promotion_settings','entity_id', $this->input->post('entity_id'));
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                }
                  
            }
        }        
        $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['edit_records'] = $this->promotion_settings_model->getEditDetail($entity_id);
        $data['restaurants'] = $this->promotion_settings_model->getListData('restaurant',null);
        $this->load->view(ADMIN_URL.'/promotion_settings_add',$data);
    }
   
    //edit data
    public function editbanner() {
        $this->form_validation->set_rules('show_banner', 'Display banner', 'trim|required');
        $this->form_validation->set_rules('text_banner_en', 'Text (EN)', 'trim|required');
        $this->form_validation->set_rules('text_banner_fr', 'Text (FR)', 'trim|required');
        if ($this->form_validation->run())
            {
                $updateData = array(                   
                    'show_banner'=>$this->input->post('show_banner'),
                    'text_en'=>$this->input->post('text_banner_en'),
                    'text_fr'=>$this->input->post('text_banner_fr'),
                    'link'=> $this->input->post('link_banner'),
                ); 
                
                if(empty($data['Error'])){
                    $this->promotion_settings_model->updateData($updateData,'banner_settings','entity_id',1);
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                }
                  
            }
    }

    // method for delete
    public function ajaxDeleteAll(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->promotion_settings_model->ajaxDelete('promotion_settings',$entity_id);
    }
}
?>