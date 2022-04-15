<?php
class cms_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
     // method for getting all
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('name', $this->input->post('page_title'));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', $this->input->post('Status'));
        }
        $this->db->group_by('content_id');
        $result['total'] = $this->db->count_all_results('cms');
        
        if($this->input->post('page_title')=="" && $this->input->post('Status') == ''){
            $this->db->where('content_type','cms');
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
            if($this->input->post('Status') != ''){
                $this->db->like('status', $this->input->post('Status'));
            }   
            $this->db->select('content_general_id,cms.*');   
            $this->db->join('content_general','cms.content_id = content_general.content_general_id','left');
            $this->db->where('content_type','cms');
            $this->db->group_by('cms.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('cms')->result();                      
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
                if($this->input->post('Status') != ''){
                    $this->db->like('status', $this->input->post('Status'));
                } 
            }
        } 
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);        
        $cmdData = $this->db->get('cms')->result_array(); 

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
    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    // method to get details by id
    public function getEditDetail($entity_id)
    {
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('cms')->first_row();
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
    // get cms slug
    public function getCmsSlug($content_id){
        $this->db->select('CMSSlug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('cms')->first_row();
    }
}
?>