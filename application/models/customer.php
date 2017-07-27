<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->tablename="customer_details";
    } 

    public function get_customers($id=null){
        if(!is_null($id)){
            $query=$this->db->select('*')->from($this->tablename)->where('ID',$id)->get();
            
            if($query->num_rows() === 1){
                return $query->row_array();
            }
            return false;
        }
        //Query can also be written as 
        // $query='Select * from hf_customer_details';
        // $this->db->query($query);
        $query=$this->db->select('*')->from($this->tablename)->get();   
        if($query->num_rows() > 0){
            return $query->result_array();
        }
        return false;
    }
    


    public function addnew_customer($customerdata){ 
        if(count($customerdata)==7){
            $check_if_exist_array=array('Email_Id'=>$customerdata['Email_Id'],"Product_ID"=>$customerdata['Product_ID']);
            $result=$this->db->select('*')->from($this->tablename)->where($check_if_exist_array)->get();
            if($result->num_rows() > 0){
                $response=array("msg"=>"Alert is already set to this Product!!","status"=>false);
                return $response;
            }
            else{
                $this->db->set($customerdata);
                $this->db->insert($this->tablename);
                if($this->db->affected_rows() == 1 )
                {
                    $response=array("msg"=>"Successfully Stored with ID ".$this->db->insert_id(),"status"=>true);
                    return $response;
                }
            }
        }
        return false;
    }



    public function edit_customer($customerdata){
        if(!empty($customerdata['ID'])){
            $result=$this->db->select('*')->from($this->tablename)->where('ID',$customerdata['ID'])->get();
            if($result->num_rows()=== 1){
                $this->db->where('ID',$customerdata['ID']);
                $this->db->update($this->tablename,$customerdata);
                if($this->db->affected_rows() == 1 )
                {
                    $response=array("msg"=>"Customer ".$customerdata['ID']." with Customer ID ".$customerdata['Product_Image_URL']." is Edited Successfully","status"=>true);
                    return $response;
                }
                // $checkresult=$result->result_array();
                // if(($checkresult[0]['Email_Id'] != $customerdata['Email_Id']) || ($checkresult[0]['Product_Domain'] != $customerdata['Product_Domain']) || ($checkresult[0]['Product_ID'] != $customerdata['Product_ID']) || ($checkresult[0]['Product_Name'] != $customerdata['Product_Name']))
                // {
                //     if(($checkresult[0]['Customer_Email_ID'] != $customerdata['Customer_Email_ID'])){
                //         $check_if_exist_array=array('Customer_Email_ID'=>$customerdata['Customer_Email_ID']);
                //         $result=$this->db->select('*')->from($this->tablename)->or_where($check_if_exist_array)->get();
                //         if($result->num_rows() > 0){
                //             $response=array("msg"=>"Updating Customer Details of Customer ID ".$customerdata['Customer_ID']." Failed, User Mail ID Already Registered!!","status"=>false);
                //             return $response;
                //         }
                //     } 
                //     $this->db->where('Customer_ID',$customerdata['Customer_ID']);
                //     $this->db->update($this->tablename,$customerdata);
                //     if($this->db->affected_rows() == 1 )
                //     {
                //         $response=array("msg"=>"Customer ".$customerdata['Customer_ID']." with Customer ID ".$customerdata['Customer_ID']." is Edited Successfully","status"=>true);
                //         return $response;
                //     }
                // }
                // else{
                //     $response=array("msg"=>"No change in Customer ID ".$customerdata['Customer_ID'],"status"=>false);
                //     return $response;
                // }
            }
            else{
                $response=array("msg"=>"Customer ".$customerdata['Customer_ID']." does not exist, Please Signup!!","status"=>false);
                return $response;
            }
        }
        return false;
    }



    public function delete_customers($id=null){
        if(!is_null($id)){
            $result=$this->db->select('*')->from($this->tablename)->where('Customer_ID',$id)->get();
            if($result->num_rows() > 0){
                $this->db->where('Customer_ID',$id);
                $deleteresult=$this->db->delete($this->tablename);   
                if(!empty($deleteresult)){
                    $response=array("msg"=>"Customer with Customer ID ".$id." is Deleted Successfully!!","status"=>true);
                    return $response;
                }
                else{
                    $response=array("msg"=>"Failed to Delete!!","status"=>false);
                    return $response;    
                }
            }
            else{
                $response=array("msg"=>"Failed to Delete, Customer ID ".$id." does not exist!!","status"=>false);
                return $response;
            }
        }
        $result=$this->db->get($this->tablename);
        if($result->num_rows() > 0){
            $deleteresult=$this->db->truncate($this->tablename);
            if(!empty($deleteresult)){
                $response=array("msg"=>"All Customers are Deleted Successfully!!","status"=>true);
                return $response;
            }else{
                $response=array("msg"=>"Failed to Delete!!","status"=>false);
                return $response;
            }
        }
        else{
            $response=array("msg"=>"Failed to Delete, Customers does not exist!!","status"=>false);
            return $response;
        }
    }
}