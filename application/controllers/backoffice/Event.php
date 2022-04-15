<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');

class Event extends CI_Controller {

    public $controller_name = 'event';

    public $prefix = '_event'; 

    public function __construct() {

        parent::__construct();

        if (!$this->session->userdata('is_admin_login')) {

            redirect(ADMIN_URL.'/home');

        }

        $this->load->library('form_validation');

        $this->load->model(ADMIN_URL.'/event_model');

    }

    // view event

    public function view(){

    	$data['meta_title'] = $this->lang->line('title_admin_event').' | '.$this->lang->line('site_title');
        $data['restaurant'] = $this->event_model->getRestaurantList();
        $this->load->view(ADMIN_URL.'/event',$data);

    }

    //ajax view

    public function ajaxview() {

        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';

        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';

        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';

        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';

        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';

        

        $sortfields = array(1=>'u.first_name','2'=>'name','3'=>'res.name','4'=>'status','5'=>'end_date','6'=>'booking_date');

        $sortFieldName = '';

        if(array_key_exists($sortCol, $sortfields))

        {

            $sortFieldName = $sortfields[$sortCol];

        }

        //Get Recored from model

        $grid_data = $this->event_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);

        $totalRecords = $grid_data['total'];        

        $records = array();

        $records["aaData"] = array(); 

        $nCount = ($displayStart != '')?$displayStart+1:1;

        foreach ($grid_data['data'] as $key => $val) {
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);

            $coupon_type = ($val->coupon_type)?"'".$val->coupon_type."'":'0';

            $tax_rate = ($val->tax_rate)?$val->tax_rate:0;

            $tax_type = ($val->tax_type)? "'".$val->tax_type."'":0;

            $coupon_amount = ($val->coupon_amount)?$val->coupon_amount:'0';

            $entId = $val->entity_id;

            $disabled = ($val->event_status == 'cancel')?'disabled':'';
            $eventStatus = "'".$val->event_status."'";

            $records["aaData"][] = array(

                $nCount,

                $val->fname.' '.$val->lname,

                $val->rname,

                $val->no_of_people,

                date('Y-m-d H:i',strtotime($val->booking_date)),

                ($val->amount)?$currency_symbol->currency_symbol.number_format_unchanged_precision($val->amount,$currency_symbol->currency_code):'-',

                ($val->event_status)?$val->event_status:'-',

                //($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),

                '<button onclick="deleteDetail('.$val->entity_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button> 
                <button title="'.$this->lang->line('add').' '.$this->lang->line('amount').'" onclick="addAmount('.$entId.','.$tax_rate.','.$coupon_amount.','.$tax_type.','.$coupon_type.')" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-dollar"></i> '.$this->lang->line('add').' '.$this->lang->line('amount').'</button>
                <button onclick="updateStatus('.$val->entity_id.','.$eventStatus.')" '.$disabled.' title="Click here for update status" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-edit"></i> Change Status</button>
                <button onclick="getInvoice('.$val->entity_id.')"  title="'.$this->lang->line('download_invoice').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('invoice').'</button>'

            );

            $nCount++;

        }        

        $records["sEcho"] = $sEcho;

        $records["iTotalRecords"] = $totalRecords;

        $records["iTotalDisplayRecords"] = $totalRecords;

        echo json_encode($records);

    }

    //add status
    public function updateEventStatus(){
        $entity_id = ($this->input->post('event_entity_id'))?$this->input->post('event_entity_id'):''; 
        if($entity_id && $this->input->post('event_status') != ''){
            $update_status = array(
                'event_status' => $this->input->post('event_status'),
            );
            $data = $this->event_model->updateData($update_status,'event','entity_id',$entity_id); 
        }
    }

    // method to change status

    public function ajaxdisable() {

        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';

        if($entity_id != ''){

            $this->event_model->UpdatedStatus('event',$entity_id,$this->input->post('status'));

        }

    }

    // method for deleting

    public function ajaxDelete(){

        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';

        $this->event_model->ajaxDelete('event',$entity_id);

    }

    //get restaurant

    public function getRestuarantDetail(){

        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';

        $result = $this->event_model->getRestuarantDetail($entity_id);

        echo json_encode($result);

    }

    //add amount

    public function addAmount(){

        $this->form_validation->set_rules('amount','Amount', 'trim|required');

        //$this->form_validation->set_rules('event_status','Status', 'trim|required');

        if($this->form_validation->run())

        {

            $add_data = array(

                'subtotal'     =>$this->input->post('subtotal'),

                'amount'       =>$this->input->post('amount'),

                'event_status' =>$this->input->post('event_status'),

            );

            $data = $this->event_model->updateData($add_data,'event','entity_id',$this->input->post('entity_id')); 

            echo json_encode($data);

        }  

    }

    //create invoice

    public function getInvoice(){

        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):'';

        $data['event_records'] = $this->event_model->getEditDetail($entity_id);

        $html = $this->load->view('backoffice/event_invoice',$data,true);

        if (!@is_dir('uploads/event')) {

          @mkdir('./uploads/event', 0777, TRUE);

        } 
        $verificationCode = random_string('alnum',8);
        $filepath = 'uploads/event/'.$verificationCode.'.pdf';

        $this->load->library('M_pdf'); 

        $mpdf=new mPDF('','Letter'); 

        $mpdf->SetHTMLHeader('');

        $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="http://eatance.evincedev.com/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');

        $mpdf->AddPage('', // L - landscape, P - portrait 

            '', '', '', '',

            0, // margin_left

            0, // margin right

            10, // margin top

            23, // margin bottom

            0, // margin header

            0 //margin footer

        );

        $mpdf->WriteHTML($html);

        $mpdf->output($filepath,'F');

        echo $filepath;    

    }
    //generate report
    public function generate_report(){
        $restaurant_id = $this->input->post('restaurant_id');
        $booking_date = $this->input->post('booking_date_export');
        $data['report_data'] = $this->event_model->generate_report($restaurant_id,$booking_date); 
        
        if(!empty($data['report_data'])){
            $html = $this->load->view('backoffice/event_generate_report',$data,true);
            //print_r($html);exit;
            if (!@is_dir('uploads/invoice')) {
              @mkdir('./uploads/invoice', 0777, TRUE);
            } 
            $filepath = 'uploads/invoice/report_'.$restaurant_id.'.pdf';
            $file = 'report.pdf';
            $this->load->library('M_pdf'); 
            $mpdf=new mPDF('','Letter'); 
            $mpdf->SetHTMLHeader('');
            $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="http://eatance.evincedev.com/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
            $mpdf->AddPage('', // L - landscape, P - portrait 
                '', '', '', '',
                0, // margin_left
                0, // margin right
                10, // margin top
                23, // margin bottom
                0, // margin header
                0 //margin footer
            );
            $mpdf->WriteHTML($html);
            $mpdf->output($file,'D');
        }else{
            $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
        }
    }
}
?>