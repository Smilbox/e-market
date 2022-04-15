<?php
class Addons_category_model extends CI_Model {
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
        $this->db->group_by('content_id');
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('add_ons_category.created_by',$this->session->userdata('UserID'));   
        }         
        $result['total'] = $this->db->count_all_results('add_ons_category');
        
        if($this->input->post('page_title')==""){
            $this->db->select('content_general_id,add_ons_category.*');   
            $this->db->join('add_ons_category','add_ons_category.content_id = content_general.content_general_id','left');
            $this->db->group_by('add_ons_category.content_id');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where('add_ons_category.created_by',$this->session->userdata('UserID'));
            } 
            $this->db->where('content_type','add_ons_category');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('content_id',$content_general_id);    
            }            
        }else{          
            if($this->input->post('page_title') != ''){
                $this->db->like('name', $this->input->post('page_title'));
            }    
            $this->db->select('content_general_id,add_ons_category.*');   
            $this->db->join('content_general','content_general.content_general_id = add_ons_category.content_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where('add_ons_category.created_by',$this->session->userdata('UserID'));
            } 
            $this->db->where('content_type','add_ons_category');
            $this->db->group_by('add_ons_category.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('add_ons_category')->result();                      
            $ContentID = array();               
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('name', trim($this->input->post('page_title')));
                } 
            }
        }  
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('add_ons_category.created_by',$this->session->userdata('UserID'));
        }   
        $cmdData = $this->db->get('add_ons_category')->result_array();         
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],               
                        'status' => $value['status'],                     
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],            
                    'status' => $value['status'],    
                );
            }
        }         
        $result['data'] = $cmsLang;        
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
        return $this->db->get_where('add_ons_category',array('entity_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
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

        $this->db->where('category_id',$entity_id);
        $this->db->delete('add_ons_master');     
    }
    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {   
        $this->db->select('entity_id');
        $this->db->where('content_id',$content_id);
        $result = $this->db->get($tblname)->result_array();
        $result = array_column($result, 'entity_id');

        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');                   

        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);

        /*$this->db->where_in('category_id',$result);
        $this->db->delete($tblname);*/

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
    // updating the changed status
    public function UpdatedStatusAll($tblname,$ContentID,$Status){
        if($Status==0){
            $Data = array('status' => 1);
        } else {
            $Data = array('status' => 0);
        }

        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
}
?>