<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Customer_api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Customer');
        $this->sites=array('www.amazon.in','www.amazon.com','www.flipkart.com');
        $this->website = new DOMDocument;
        libxml_use_internal_errors(true);
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        // $this->methods['customers_get']['limit'] = 500; // 500 requests per hour per user/key
        // $this->methods['customer_post']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['customer_put']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['customer_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function customer_get($id=''){
        if(!empty($id)){
            $result=$this->Customer->get_customers($id);    
            if(!empty($result)){
                $this->response(array($result),200);
            }
            else{
                $this->response(array("msg"=>"Customer Id ".$id." Does Not Exist!!","status"=>false),200);
            }
        }
        else
        {
            $result=$this->Customer->get_customers();    
            if(!empty($result)){
                $this->response(array($result),200);
            }
            else{
                $this->response(array("msg"=>"Customers Do Not Exist!!","status"=>false),404);
            }
        }
    }
    public function customer_post(){
        if(!empty($this->post('Email_Id')) && !empty($this->post('Product_Url')))
        {   
            $data_to_store=array();
            $user_email=$this->post('Email_Id');
            $product_url=$this->post('Product_Url');
            $processedURL=parse_url($product_url);
            $hostname=$processedURL['host'];
            switch($hostname){
                case $this->sites[0] :
                case $this->sites[1] :
                        $data_to_store=$this->amazon_products($user_email,$hostname,$product_url,$processedURL);
                    break;
                case $this->sites[2] :
                        $data_to_store=$this->flipkart_products($user_email,$hostname,$product_url,$processedURL);
                    break;
                default  :
                        $this->response(array("msg"=>"Product site is not an amazon or flipkart website","status"=>false),200);
                    break;
            }

            // $this->response(array("result"=>$data_to_store),200);
            if(!empty($result=$this->Customer->addnew_customer($data_to_store))){
                $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
            }
            else{
                $this->response(array("msg"=>"Unable to store Details","status"=>false),404);    
            }
        }
        else{
            $errormsg="";
            if(empty($this->post('Email_Id'))){
                $errormsg="Invalid Email Id !!!";
            }
            else if(empty($this->post('Product_Url'))){
                $errormsg="Valid Product URL is required !!!";
            }
            else{
                $errormsg="Invalid No of Parameters!!!";
            }
            $this->response(array("msg"=>$errormsg,"status"=>false),404);
        }
    }
    
    
    
    public function customer_put($id=''){
        if(!empty($id)){
            if(!empty($this->put('Customer_Name')) && !empty($this->put('Customer_Password')) && !empty($this->put('Customer_Phone_No')) && !empty($this->put('Customer_Email_ID')))
            {
                $customer=array(
                    "Customer_ID"=>$id,
                    "Customer_Name"=>trim($this->put('Customer_Name')),
                    "Customer_Password"=>trim(md5($this->put('Customer_Password'))),
                    "Customer_Phone_No"=>trim($this->put('Customer_Phone_No')),
                    "Customer_Email_ID"=>trim($this->put('Customer_Email_ID')),
                    "Customer_Joined_Date"=>trim($this->put('Customer_Joined_Date'))
                );
                if(!empty($result=$this->Customer->edit_customer($customer))){
                    $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
                }
                else{
                    $this->response(array("msg"=>"Failed To Store Customer","status"=>false),404);    
                }
            }
            else{
                $errormsg="";
                if(empty($this->put('Customer_Name'))){
                    $errormsg="Invalid Customer Name !!!";
                }
                else if(empty($this->put('Customer_Password'))){
                    $errormsg="Invalid Customer Password !!!";
                }
                else if(empty($this->put('Customer_Phone_No'))){
                    $errormsg="Invalid Customer Phone Number !!!";
                }
                else if(empty($this->put('Customer_Email_ID'))){
                    $errormsg="Invalid Customer Email ID !!!";
                }
                else{
                    $errormsg="Invalid No of Parameters!!!";
                }
                $this->response(array("msg"=>$errormsg,"status"=>false),404);
            }
        }
        else{
            $errormsg="Customer ID parameter is required!!!";
            $this->response(array("msg"=>$errormsg,"status"=>false),404);
        }
    }

    public function customer_delete($id=''){
        if(!empty($id)){
            $result=$this->Customer->delete_customers($id);    
            if(!empty($result)){
                $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
            }
        }
        else
        {
            $result=$this->Customer->delete_customers();    
            if(!empty($result)){
                $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
            }
        }
    }

    public function amazon_products($email,$domain,$url,$processedURL){
        $path=$processedURL['path'];
        $splitURL  = explode('/', $path);
        $unique_Product_Path = implode('/', array_slice($splitURL, 0, 4));
        if(strpos($path, '/dp/') !== false) {
            $this->website->loadHTMLFile($url);
            if($this->website->getElementById('priceblock_ourprice')){
                $priceTag=$this->website->getElementById('priceblock_ourprice');
            }
            if($this->website->getElementById('priceblock_saleprice')){
                $priceTag=$this->website->getElementById('priceblock_saleprice');
            }
            if($priceTag){
                $finder = new DomXPath($this->website);
                $classname="currencyINR";
                $nodes = $finder->query("//td//span[contains(@class, '$classname')]");
                $length=$nodes->length;
                if($length){
                    for($i=0;$i<$length;$i++){
                        $nodes[$i]->removeAttribute('class');
                    }
                    $currency="INR";
                }else{
                    $currency="USD";    
                }
                $product_price=trim($priceTag->nodeValue);
            }else{
                $product_price="9999";
            }
            $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
            ///////////////////////////////////////////////////////////////// -- After processing
            $customer=array(
                "Email_Id"=>$email,
                "Product_Domain"=>$domain,
                "Product_ID"=>$unique_Product_Path,
                "Product_Name"=>trim($this->website->getElementById('productTitle')->nodeValue),
                "Product_Price"=>$product_price,
                "Currency_Type"=>$currency
            );
            return $customer;
        }
        else{
            return false;
        }
    }

    public function flipkart_products($email,$domain,$url,$processedURL){
        $path=$processedURL['path'];
        $splitURL  = explode('/', $path);
        $unique_Product_Path = implode('/', array_slice($splitURL, 0, 4));
        if(strpos($path, '/p/') !== false) {
            $this->website->loadHTMLFile($url);
            $finder = new DomXPath($this->website);
            $Name = $finder->query("//div//div[contains(@class, '_2UDlNd')]");
            $product_Name=trim($Name[0]->nodeValue);
            $price = $finder->query("//div//div[contains(@class, '_1vC4OE _37U4_g')]");
            $priceTag=$price[0];
            if($priceTag){
                $product_price=trim($priceTag->nodeValue);
            }else{
                $product_price="9999";
            }
            $product_Name = htmlentities($product_Name, null, 'utf-8');
            $product_Name = str_replace("&nbsp;", "", $product_Name);
            $currency="INR";
            $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
            ///////////////////////////////////////////////////////////////// -- After processing
            $customer=array(
                "Email_Id"=>$email,
                "Product_Domain"=>$domain,
                "Product_ID"=>$unique_Product_Path,
                "Product_Name"=>$product_Name,
                "Product_Price"=>$product_price,
                "Currency_Type"=>$currency
            );
            return $customer;
        }
        else{
            return false;
        }
    }
}
