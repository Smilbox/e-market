<?php
class Promotion_settings_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    public function getAll($tableName) {
        return $this->db->get($tableName)->result();
    }

    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    }

    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }

    //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('promotion_settings',array('entity_id'=>$entity_id))->first_row();
    }

    // delete all records
    public function ajaxDelete($tblname,$content_id)
    {           
        $this->db->where('entity_id',$content_id);
        $this->db->delete($tblname);    
    }

    public function getById($id) {
        $this->db->where('entity_id', $id);
        return $this->db->get('promotion_settings')->first_row();
    }
    
    public function getBannerSettingsById($id) {
        $this->db->where('entity_id', $id);
        return $this->db->get('banner_settings')->first_row();
    }

    public function getAllPromotionWithResto()
    {
        $this->db->select('promotion_settings.entity_id,promotion_settings.restaurant_id,promotion_settings.shown_promotion,promotion_settings.priority_order, promotion_settings.image, res.restaurant_slug');
        $this->db->join('restaurant as res','promotion_settings.restaurant_id = res.entity_id','left');
        return $this->db->get('promotion_settings')->result();  
    }

     //ajax view      
     public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
     {
         if($this->input->post('restaurant') != ''){
             $this->db->like('res.name', $this->input->post('restaurant'));
         }        
         $this->db->select('promotion_settings.entity_id,promotion_settings.restaurant_id,promotion_settings.shown_promotion,promotion_settings.priority_order, promotion_settings.image');
         $this->db->join('restaurant as res','promotion_settings.restaurant_id = res.entity_id','left');
         $result['total'] = $this->db->count_all_results('promotion_settings');
         if($sortFieldName != '')
             $this->db->order_by($sortFieldName, $sortOrder);
 
         if($displayLength>1)
         $this->db->limit($displayLength,$displayStart);
         if($this->input->post('restaurant') != ''){
             $this->db->like('res.name', $this->input->post('restaurant'));
         }  
         $this->db->select('promotion_settings.entity_id,promotion_settings.restaurant_id,promotion_settings.shown_promotion,promotion_settings.priority_order, promotion_settings.image, res.name');
         $this->db->join('restaurant as res','promotion_settings.restaurant_id = res.entity_id','left');
         $result['data'] = $this->db->get('promotion_settings')->result();              
         return $result;
     } 

     public function getListData($tblname,$language_slug=NULL){
        $this->db->select('name,entity_id');
        $this->db->where('status',1);
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));  
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        return $this->db->get($tblname)->result();
    }
}
?>