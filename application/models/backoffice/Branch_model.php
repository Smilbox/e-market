<?php
class Branch_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		        
    }	
    // method for getting all
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('shop.name', $this->input->post('page_title'));
        }
        if($this->input->post('shop') != ''){
            $this->db->like('shop.name', $this->input->post('shop'));
        }
        $this->db->select('shop.name,shop.name as sname');
        $this->db->join('shop','shop.branch_entity_id = shop.entity_id','left');
        $this->db->where('shop.branch_entity_id !=','');
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where_in('shop.entity_id',$this->session->userdata('shop'));
        }
        $this->db->group_by('shop.content_id');
        $result['total'] = $this->db->count_all_results('shop');
        if($this->input->post('page_title')=="" && $this->input->post('shop') == ''){
            $this->db->select('content_general.content_general_id,shop.name,shop.name as sname');
            $this->db->join('shop','content_general.content_general_id = shop.content_id','left');
            $this->db->join('shop','shop.branch_entity_id = resta.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin'){
                $this->db->where_in('resta.entity_id',$this->session->userdata('shop'));
            }
            $this->db->where('content_type','branch');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('shop.content_id',$content_general_id);    
            }          
        }else{
            if($this->input->post('page_title') != ''){
            $this->db->like('shop.name', $this->input->post('page_title'));
            }
            if($this->input->post('shop') != ''){
                $this->db->like('resta.name', $this->input->post('shop'));
            }
            $this->db->select('shop.*,resta.name as sname');
            $this->db->join('shop as resta','shop.branch_entity_id = resta.entity_id','left');
            $this->db->where('shop.branch_entity_id !=','');
            if($this->session->userdata('UserType') == 'Admin'){
                $this->db->where_in('resta.entity_id',$this->session->userdata('shop'));
            }
            $this->db->group_by('shop.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('shop')->result();
            $ContentID = array();      
            $OrderByID = "";         
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( shop.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('shop.content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('shop.name', trim($this->input->post('page_title')));
                }
                if($this->input->post('shop') != ''){
                    $this->db->like('resta.name', $this->input->post('shop'));
                } 
            }           
        }
        $this->db->select('shop.*,resta.name as sname');
        $this->db->join('shop as resta','shop.branch_entity_id = resta.entity_id','left');
        $this->db->where('shop.branch_entity_id !=','');
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where_in('resta.entity_id',$this->session->userdata('shop'));
        }
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('shop')->result_array();
         
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'], 
                        'sname'=> $value['sname']                
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }   	
    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    // method to get details by id
    public function getEditDetail($tblname,$entity_id)
    {
        $this->db->select('res.*,res_add.*');
        $this->db->join('shop_address as res_add','res.entity_id = res_add.shop_entity_id','left');
        $this->db->where('res.entity_id',$entity_id);
        return $this->db->get(''.$tblname.' as res')->first_row();
    }
    // delete 
    public function ajaxDelete($tblname,$content_id,$entity_id)
    {
        // check  if last record
        if($content_id){
            $vals = $this->db->get_where($tblname,array('content_id'=>$content_id))->num_rows();    
            if($vals==1){
                $this->db->where(array('content_general_id' => $content_id));
                $this->db->delete('content_general');        
            }            
        } 
        $this->db->where('entity_id',$entity_id);
        $this->db->delete($tblname);    
    }
    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');                   

        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);    
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
     //get list
    public function getListData($tblname,$language_slug){
        $this->db->select('name,entity_id');
        $this->db->where('status',1);
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));  
        } 
        $this->db->where('language_slug',$language_slug);
        return $this->db->get($tblname)->result();
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
}
?>