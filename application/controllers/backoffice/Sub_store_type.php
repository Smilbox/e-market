<?php 
if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Sub_store_type extends CI_Controller {
    public $controller_name = 'sub_store_type';
    public function __construct() {
        parent::__construct();
            if (!$this->session->userdata('is_admin_login')) 
            {
                redirect(ADMIN_URL.'/home');
            }
            $this->load->library('form_validation');
            $this->load->model(ADMIN_URL.'/sub_store_type_model');
            $this->load->model(ADMIN_URL.'/store_type_model');
            $this->load->model(ADMIN_URL.'/common_model');
    }

    //add data
    public function add() {
        $data['meta_title'] = $this->lang->line('sub-store-type').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('store_type_id', 'Store type ID', 'trim|required');
            $this->form_validation->set_rules('name_en', 'Name En', 'trim|required');
            $this->form_validation->set_rules('name_fr', 'Nom Fr', 'trim|required');
            if ($this->form_validation->run())
            {
                $add_data = array(                   
                    'store_type_id'=>$this->input->post('store_type_id'),
                    'name_en'=>$this->input->post('name_en'),
                    'name_fr'=>$this->input->post('name_fr'),
                    'created_by' => $this->session->userdata('UserID')
                ); 
                if(empty($data['Error'])){
                    $this->sub_store_type_model->addData('sub_store_type',$add_data); 
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');  
                }         
            }
        }
        $data['store_types'] = $this->store_type_model->getAll();
        $this->load->view(ADMIN_URL.'/sub_store_type_add',$data);
    }

    //edit data
    public function edit() {
        $data['meta_title'] = $this->lang->line('sub-store-type').' | '.$this->lang->line('site_title');
        //check add form is submit
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name_en', 'Name En', 'trim|required');
            $this->form_validation->set_rules('name_fr', 'Nom Fr', 'trim|required');
            $this->form_validation->set_rules('store_type_id', 'Store type ID', 'trim|required');
            if ($this->form_validation->run())
            {
                $updateData = array(                   
                    'store_type_id'=>$this->input->post('store_type_id'),
                    'name_en'=>$this->input->post('name_en'),
                    'name_fr'=>$this->input->post('name_fr'),
                    'updated_date'=>date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID')
                ); 
                
                if(empty($data['Error'])){
                    $this->sub_store_type_model->updateData($updateData,'sub_store_type','entity_id',$this->input->post('entity_id'));
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                }
                  
            }
        }        
        $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['edit_records'] = $this->sub_store_type_model->getEditDetail($entity_id);
        $data['store_types'] = $this->store_type_model->getAll();
        $this->load->view(ADMIN_URL.'/sub_store_type_add',$data);
    }
    
    // view restaurant menu
    public function view(){
        $data['meta_title'] = $this->lang->line('sub-store-type').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL.'/sub_store_type',$data);
    }

    //ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'name_en',2=>'name_fr');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->sub_store_type_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->store_type,
                $val->name_en,
                $val->name_fr,
                '<a class="btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a> <button onclick="deleteDetail('.$val->entity_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>'
            );
            $nCount++;
        }   
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }

    // method for delete
    public function ajaxDeleteAll(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->sub_store_type_model->ajaxDeleteAll('sub_store_type',$entity_id);
    }
}
?>