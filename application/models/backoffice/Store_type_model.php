<?php
class Store_type_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    public function getAll() {
        return $this->db->get('store_type')->result();
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
        return $this->db->get_where('store_type',array('entity_id'=>$entity_id))->first_row();
    }

    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {           
        $this->db->where('entity_id',$content_id);
        $this->db->delete($tblname);    
    }

    public function getById($id) {
        $this->db->where('entity_id', $id);
        return $this->db->get('store_type')->first_row();
    }

     //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        /*if($this->input->post('page_title') != ''){
            $this->db->like('name_fr', $this->input->post('page_title'));
        }*/ 
        if($this->input->post('name_fr') != ''){
            $this->db->like('store_type.name_fr', $this->input->post('name_fr'));
        } 
        if($this->input->post('name_en') != ''){
            $this->db->like('store_type.name_en', $this->input->post('name_en'));
        } 
        /*if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
        }*/             
        $this->db->select('store_type.name_fr,store_type.name_en,store_type.entity_id, store_type.icon, store_type.banner');
        $result['total'] = $this->db->count_all_results('store_type');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if($displayLength>1)
        $this->db->limit($displayLength,$displayStart);
        if($this->input->post('name_fr') != ''){
            $this->db->like('store_type.name_fr', $this->input->post('name_fr'));
        } 
        if($this->input->post('name_en') != ''){
            $this->db->like('store_type.name_en', $this->input->post('name_en'));
        }  
        $this->db->select('store_type.name_fr,store_type.name_en,store_type.entity_id, store_type.icon, store_type.banner');
        $result['data'] = $this->db->get('store_type')->result();              
        return $result;
    } 
}
?>