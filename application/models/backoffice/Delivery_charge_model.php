<?php
class Delivery_charge_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		
    } 
    //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('area_name', $this->input->post('page_title'));
        } 
        if($this->input->post('delivery_type') != ''){
            $this->db->where_in('shop.delivery_type', $this->input->post('delivery_type'));
        } 
        if($this->input->post('res_name') != ''){
            $this->db->like('shop.name', $this->input->post('res_name'));
        } 
        if($this->input->post('price') != ''){
            $this->db->like('price_charge', $this->input->post('price'));
        }      
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
        }             
        $this->db->select('shop.name, shop.entity_id, delivery_charge.shop_id,delivery_charge.area_name,delivery_charge.price_charge,delivery_charge.charge_id,delivery_charge.delivery_type,shop.currency_id');
        $this->db->join('shop','delivery_charge.shop_id = shop.entity_id','left'); 
        $result['total'] = $this->db->count_all_results('delivery_charge');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);
        if($this->input->post('res_name') != ''){
            $this->db->like('shop.name', $this->input->post('res_name'));
        } 
        if($this->input->post('price') != ''){
            $this->db->like('price_charge', $this->input->post('price'));
        }   
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
        }             
        $this->db->select('shop.name, shop.entity_id, delivery_charge.shop_id,delivery_charge.area_name,delivery_charge.price_charge,delivery_charge.charge_id,delivery_charge.delivery_type,shop.currency_id');
        $this->db->join('shop','delivery_charge.shop_id = shop.entity_id','left'); 
        $result['data'] = $this->db->get('delivery_charge')->result();              
        return $result;
    }  
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('delivery_charge',array('charge_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    
    // update data common function
    public function updateDataWheres($Data,$tblName,$whereArray)
    {        
        $this->db->where($whereArray);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {           
        $this->db->where('charge_id',$content_id);
        $this->db->delete($tblname);    
    }
    //get list
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
    public function getResLatLong($shop_id){
        $this->db->select('latitude,longitude');
        $this->db->where('shop_entity_id',$shop_id);
        return $this->db->get('shop_address')->first_row();

    }
}
?>