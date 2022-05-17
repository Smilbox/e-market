<?php
class Shop_model extends CI_Model {
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
        if($this->input->post('status') != ''){
            $this->db->like('shop.status', $this->input->post('status'));
        }
        $this->db->group_by('content_id');
        $this->db->where('shop.branch_entity_id','');
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
        }           
        $result['total'] = $this->db->count_all_results('shop');
        
        if($this->input->post('page_title')==""){ 
            if($this->input->post('status') != ''){
                $this->db->like('shop.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,shop.*');   
            $this->db->join('shop','shop.content_id = content_general.content_general_id','left');
            $this->db->group_by('shop.content_id');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where('shop.created_by',$this->session->userdata('UserID'));
            } 
            $this->db->where('content_type','shop');
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
            if($this->input->post('status') != ''){
                $this->db->like('shop.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,shop.*');   
            $this->db->join('content_general','shop.content_id = content_general.content_general_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where('shop.created_by',$this->session->userdata('UserID'));
            } 
            $this->db->where('content_type','shop');
            $this->db->group_by('shop.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('shop')->result();                      
            $ContentID = array();
            $OrderByID = "";               
            foreach ($cmsData as $key => $value) {
                $OrderByID .= ','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('name', trim($this->input->post('page_title')));
                } 
                if($this->input->post('status') != ''){
                    $this->db->like('shop.status', $this->input->post('status'));
                }
            }
        } 
        $this->db->where('shop.branch_entity_id',''); 
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
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
                        'status' => $value['status']           
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'status' => $value['status']   
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
        $this->db->select('shop.*,shop_addr.address,shop_addr.landmark,shop_addr.zipcode,shop_addr.country,shop_addr.state,shop_addr.city,shop_addr.latitude,shop_addr.longitude');
        $this->db->join('shop_address as shop_addr','shop.entity_id = shop_addr.shop_entity_id','left');
        $this->db->where('shop.entity_id',$entity_id);
        return $this->db->get(''.$tblname.' as shop')->first_row();
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
    //menu grid
    public function getMenuGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        if($this->input->post('page_title') != ''){
            $this->db->like('menu.name', $this->input->post('page_title'));
        }
        if($this->input->post('shop') != ''){
            $this->db->like('shop.name', $this->input->post('shop'));
        }
        if($this->input->post('price') != ''){
            $this->db->like('menu.price', $this->input->post('price'));
        } 
        $this->db->select('menu.name as mname,shop.name as rname,menu.entity_id,menu.status,shop.currency_id');
        $this->db->join('shop','menu.shop_id = shop.entity_id','left');
        if($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('shop'))){     
            $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
        } elseif($this->session->userdata('UserType') != 'MasterAdmin'){
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
        }
        $this->db->group_by('menu.content_id');
        $result['total'] = $this->db->count_all_results('shop_menu_item as menu');
        
        if($this->input->post('page_title')=="" && $this->input->post('shop') == '' && $this->input->post('price') == ''){
            $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('shop','menu.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('shop'))){     
                $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
            } elseif($this->session->userdata('UserType') != 'MasterAdmin'){
                $this->db->where('shop.created_by',$this->session->userdata('UserID'));
            }
            $this->db->where('content_type','menu');
            $this->db->group_by('menu.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result(); 

            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('menu.content_id',$content_general_id);    
            }            
        }else{          
            if($this->input->post('page_title') != ''){
                $this->db->like('menu.name', $this->input->post('page_title'));
            }   
            if($this->input->post('shop') != ''){
                $this->db->like('shop.name', $this->input->post('shop'));
            } 
            if($this->input->post('price') != ''){
                $this->db->like('menu.price', $this->input->post('price'));
            } 
            $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('shop','menu.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('shop'))){     
                $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
            } elseif($this->session->userdata('UserType') != 'MasterAdmin'){
                $this->db->where('shop.created_by',$this->session->userdata('UserID'));
            }
            $this->db->where('content_type','menu');
            $this->db->group_by('menu.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('content_general')->result(); 
            $ContentID = array();
            $OrderByID = "";            
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( menu.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('menu.content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('menu.name', trim($this->input->post('page_title')));
                } 
                if($this->input->post('shop') != ''){
                    $this->db->like('shop.name', $this->input->post('shop'));
                } 
                if($this->input->post('price') != ''){
                    $this->db->like('menu.price', $this->input->post('price'));
                } 
            }
        }  
        $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
        $this->db->join('content_general','menu.content_id = content_general.content_general_id','left');
        $this->db->join('shop','menu.shop_id = shop.entity_id','left');
        if($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('shop'))){     
            $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
        } elseif($this->session->userdata('UserType') != 'MasterAdmin'){
            $this->db->where('shop.created_by',$this->session->userdata('UserID'));
        }
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('shop_menu_item as menu')->result_array();           
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' =>$value['rname'],
                        'price' => $value['price'], 
                        'check_add_ons' => $value['check_add_ons'], 
                        'currency_id' =>$value['currency_id'],
                        'status' => $value['status']                    
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' =>$value['rname'],
                    'price' =>$value['price'],
                    'status' =>$value['status'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result; 
    }
    //package grid
    public function getPackageGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        if($this->input->post('page_title') != ''){
            $this->db->like('package.name', $this->input->post('page_title'));
        }
        if($this->input->post('shop') != ''){
            $this->db->like('shop.name', $this->input->post('shop'));
        }
        if($this->input->post('price') != ''){
            $this->db->like('package.price', $this->input->post('price'));
        } 
        $this->db->select('package.name as mname,shop.name as rname,package.entity_id,package.status,shop.currency_id');
        $this->db->join('shop','package.shop_id = shop.entity_id','left');
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where_in('package.shop_id',$this->session->userdata('shop'));
        } 
        $this->db->group_by('package.content_id');
        $result['total'] = $this->db->count_all_results('shop_package as package');
        
        if($this->input->post('page_title')=="" && $this->input->post('shop') == '' && $this->input->post('price') == ''){
            $this->db->select('content_general_id,package.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_package as package','package.content_id = content_general.content_general_id','left');
            $this->db->join('shop','package.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where_in('shop.created_by',$this->session->userdata('shop'));
            } 
            $this->db->where('content_type','package');
            $this->db->group_by('package.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('package.content_id',$content_general_id);    
            }            
        }else{          
            if($this->input->post('page_title') != ''){
                $this->db->like('package.name', $this->input->post('page_title'));
            }   
            if($this->input->post('shop') != ''){
                $this->db->like('shop.name', $this->input->post('shop'));
            }
            if($this->input->post('price') != ''){
                $this->db->like('package.price', $this->input->post('price'));
            }  
            $this->db->select('content_general_id,package.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_package as package','content_general.content_general_id = package.content_id','left');
            $this->db->join('shop','package.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where_in('package.shop_id',$this->session->userdata('shop'));
            } 
            $this->db->group_by('package.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('content_general')->result();                      
            $ContentID = array();  
            $OrderByID = "";             
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( package.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('package.content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('package.name', trim($this->input->post('page_title')));
                } 
                if($this->input->post('shop') != ''){
                    $this->db->like('shop.name', $this->input->post('shop'));
                } 
                if($this->input->post('price') != ''){
                    $this->db->like('package.price', $this->input->post('price'));
                } 
            }
        }  
        $this->db->select('content_general_id,package.*,shop.name as rname,shop.currency_id');   
        $this->db->join('content_general','package.content_id = content_general.content_general_id','left');
        $this->db->join('shop','package.shop_id = shop.entity_id','left');
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where_in('package.shop_id',$this->session->userdata('shop'));
        } 
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('shop_package as package')->result_array();           
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' =>$value['rname'],
                        'price' =>$value['price'],
                        'check_add_ons' =>$value['check_add_ons'], 
                        'created_by' => $value['created_by'], 
                        'currency_id' =>$value['currency_id'],    
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' =>$value['rname'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }
    public function checkExist($phone_number,$entity_id,$content_id){
        $this->db->where('phone_number',$phone_number);
        $this->db->where('entity_id !=',$entity_id);
        $this->db->where('content_id !=',$content_id);
        return $this->db->get('shop')->num_rows();
    }
    public function checkEmailExist($email,$entity_id,$content_id){
        $this->db->where('email',$email);
        $this->db->where('entity_id !=',$entity_id);
        $this->db->where('content_id !=',$content_id);
        return $this->db->get('shop')->num_rows();
    }
    //insert batch
    public function inserBatch($tblname,$data){
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
    //get add ons detail
    public function getAddonsDetail($tblname,$menu_id){
        $this->db->where('menu_id',$menu_id);
        $result = $this->db->get($tblname)->result();
        $addons = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($addons[$value->category_id])){
                    $addons[$value->category_id] = array();
                }
                if(isset($addons[$value->category_id])){
                    array_push($addons[$value->category_id], $value);
                }
            }
        }
        return $addons;
    }
    //delete insert data
    public function deleteinsertBatch($tblname,$data,$menu_id){
        $this->db->where('menu_id',$menu_id);
        $this->db->delete($tblname);
        if(!empty($data)){
            $this->db->insert_batch($tblname,$data);
            return $this->db->insert_id();
        }
    }
     //deal grid
    public function getDealGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        if($this->input->post('page_title') != ''){
            $this->db->like('menu.name', $this->input->post('page_title'));
        }
        if($this->input->post('shop') != ''){
            $this->db->like('shop.name', $this->input->post('shop'));
        }
        if($this->input->post('price') != ''){
            $this->db->like('menu.price', $this->input->post('price'));
        } 
        $this->db->select('menu.name as mname,shop.name as rname,menu.entity_id,menu.status,shop.currency_id');
        $this->db->join('shop','menu.shop_id = shop.entity_id','left');
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
        }
        $this->db->where('is_deal',1); 
        $this->db->group_by('menu.content_id');
        $result['total'] = $this->db->count_all_results('shop_menu_item as menu');
        
        if($this->input->post('page_title')=="" && $this->input->post('shop') == '' && $this->input->post('price') == ''){
            $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('shop','menu.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
            } 
            $this->db->where('content_type','menu');
            $this->db->where('is_deal',1); 
            $this->db->group_by('menu.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result(); 

            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('menu.content_id',$content_general_id);    
            }            
        }else{          
            if($this->input->post('page_title') != ''){
                $this->db->like('menu.name', $this->input->post('page_title'));
            }   
            if($this->input->post('shop') != ''){
                $this->db->like('shop.name', $this->input->post('shop'));
            } 
            if($this->input->post('price') != ''){
                $this->db->like('menu.price', $this->input->post('price'));
            } 
            $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
            $this->db->join('shop_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('shop','menu.shop_id = shop.entity_id','left');
            if($this->session->userdata('UserType') == 'Admin'){     
                $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
            } 
            $this->db->where('content_type','menu');
            $this->db->where('is_deal',1); 
            $this->db->group_by('menu.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('content_general')->result(); 
            $ContentID = array();
            $OrderByID = "";               
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( menu.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('menu.content_id',$ContentID);
            }else{              
                if($this->input->post('page_title') != ''){
                    $this->db->like('menu.name', trim($this->input->post('page_title')));
                } 
                if($this->input->post('shop') != ''){
                    $this->db->like('shop.name', $this->input->post('shop'));
                } 
                if($this->input->post('price') != ''){
                    $this->db->like('menu.price', $this->input->post('price'));
                } 
            }
        }  
        $this->db->select('content_general_id,menu.*,shop.name as rname,shop.currency_id');   
        $this->db->join('content_general','menu.content_id = content_general.content_general_id','left');
        $this->db->join('shop','menu.shop_id = shop.entity_id','left');
        $this->db->where('is_deal',1); 
        if($this->session->userdata('UserType') == 'Admin'){     
            $this->db->where_in('menu.shop_id',$this->session->userdata('shop'));
        }   
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('shop_menu_item as menu')->result_array();           
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' =>$value['rname'],
                        'price' => $value['price'], 
                        'check_add_ons' => $value['check_add_ons'], 
                        'created_by' => $value['created_by'],  
                        'currency_id' =>$value['currency_id'],                    
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' =>$value['rname'],
                    'price' =>$value['price'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result; 
    }
    //gewt deals details
    public function getDealDetail($menu_id){
        $this->db->select('deal_category.deal_category_name,add_ons_master.*');
        $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu_id',$menu_id);
        $result = $this->db->get('add_ons_master')->result();
        $items = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($items[$value->deal_category_id])){
                    $items[$value->deal_category_id] = array();
                }
                array_push($items[$value->deal_category_id], $value);
            }
        }
        return $items;
    }
    //delete category
    public function deleteDealCategory($category_id){
        $this->db->where('deal_category_id',$category_id);
        $this->db->delete('deal_category');
    }
    //get deal category
    public function dealCategory($tblname,$language_slug)
    {
        $this->db->where('language_slug',$language_slug);
        $this->db->where('deal_category',1);
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));  
        }
        return $this->db->get($tblname)->result();
    }
    // get shop slug
    public function getShopSlug($content_id){
        $this->db->select('shop_slug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('shop')->first_row();
    }
    // get item slug
    public function getItemSlug($content_id){
        $this->db->select('item_slug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('shop_menu_item')->first_row();
    }
    // get shops name
    public function getshopName($entity_id){
        $this->db->select('name');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('shop')->first_row();
    }
    // get content id
    public function getContentId($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    // get category id
    public function getCategoryId($name,$lang_slug){
        $this->db->select('entity_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        return $this->db->get('category')->first_row();
    }
    // get addons for language
    public function getAddons($lang_slug){
        $this->db->select('name');
        $this->db->where('language_slug',$lang_slug);
        $addons = $this->db->get('add_ons_category')->result_array();
        return array_column($addons, 'name');
    }
    // check addons category exist or not
    public function getAddonsId($name,$lang_slug){
        $this->db->select('entity_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        return $this->db->get('add_ons_category')->first_row();
    }
    // check addons category exist or not
    public function getshopId($name,$lang_slug){
        $this->db->select('entity_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        return $this->db->get('shop')->first_row();
    }
}
?>