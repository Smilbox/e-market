<?php
class Coupon_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		        
    }	
      //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('name', $this->input->post('page_title'));
        }
        if($this->input->post('amount') != ''){
            $this->db->like('amount', $this->input->post('amount'));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', $this->input->post('Status'));
        }
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));
        } 
        $this->db->select('coupon.*,shop.currency_id');
        $this->db->join('coupon_shop_map','coupon.entity_id = coupon_shop_map.coupon_id','left');
        $this->db->join('shop','coupon_shop_map.shop_id = shop.entity_id','left');
        $this->db->group_by('coupon.entity_id');
        $result['total'] = $this->db->count_all_results('coupon');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
            $this->db->like('name', $this->input->post('page_title'));
        }
        if($this->input->post('amount') != ''){
            $this->db->like('amount', $this->input->post('amount'));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', $this->input->post('Status'));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);     
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));  
        }  
        $this->db->select('coupon.*,shop.currency_id');
        $this->db->join('coupon_shop_map','coupon.entity_id = coupon_shop_map.coupon_id','left');
        $this->db->join('shop','coupon_shop_map.shop_id = shop.entity_id','left');
        $this->db->group_by('coupon.entity_id');
        $result['data'] = $this->db->get('coupon')->result();       
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
        $this->db->select('c.*');
        return $this->db->get_where('coupon as c',array('c.entity_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatus($tblname,$entity_id,$status){
        if($status==0){
            $userData = array('status' => 1);
        } else {
            $userData = array('status' => 0);
        }        
        $this->db->where('entity_id',$entity_id);
        $this->db->update($tblname,$userData);
        return $this->db->affected_rows();
    }
    // delete user
    public function deleteUser($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
    //get list
    public function getListData($tblname,$where){
        $this->db->where($where);
        return $this->db->get($tblname)->result_array();
    }
    public function checkExist($coupon,$entity_id){
        $this->db->where('name',$coupon);
        $this->db->where('entity_id !=',$entity_id);
        return $this->db->get('coupon')->num_rows();
    }
    //insert batch 
    public function insertBatch($tblname,$data,$id){
        if($id){
            $this->db->where('coupon_id',$id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname,$data);           
        return $this->db->insert_id();
    }
    //get items
    public function getItem($entity_id,$coupon_type){
        $this->db->select('shop_menu_item.entity_id,shop_menu_item.name,shop_menu_item.price,shop.name as shop_name,shop_menu_item.shop_id');
        $this->db->join('shop','shop_menu_item.shop_id = shop.entity_id','left');
        $this->db->where_in('shop_menu_item.shop_id',$entity_id);
        $this->db->where('shop_menu_item.status',1);
        if($coupon_type == 'discount_on_combo'){
            $this->db->where('shop_menu_item.is_deal',1);
        }
        $result =  $this->db->get('shop_menu_item')->result();
        $res = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($res[$value->shop_id])){
                    $res[$value->shop_id] = array();
                }
                array_push($res[$value->shop_id], $value);
            }
        }
        return $res;
    }
}
?>