<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/phpMail/SendMail.php';
require APPPATH . '/libraries/shortenURL.php';

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
        if($id){
            $result=$this->Customer->get_customers($id);    
            if($result){
                $this->response(array($result),200);
            }
            else{
                $this->response(array("msg"=>"Customer Id ".$id." Does Not Exist!!","status"=>false),200);
            }
        }
        else
        {
            $result=$this->Customer->get_customers();    
            if($result){
                $this->response(array($result),200);
            }
            else{
                $this->response(array("msg"=>"Customers Do Not Exist!!","status"=>false),200);
            }
        }
    }
    public function customer_post(){
        if($this->input->post('Email_Id') && $this->input->post('Product_Url'))
        {   
            $data_to_store=array();
            $user_email=$this->post('Email_Id');
            $product_url=$this->post('Product_Url');
            $validmailId=$this->checkValidMailId($user_email);            
            if($validmailId){
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
                            $this->response(array("msg"=>"Current Page is not a valid product page!!","status"=>false),200);
                        break;
                }

                // $this->response(array("results"=>$data_to_store),200);
                $result=$this->Customer->addnew_customer($data_to_store);
                if($result){
                    $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
                }
                // else{
                //     $this->response(array("msg"=>"Unable to store Details","status"=>false),200);    
                // }
            }else{
                $this->response(array("msg"=>"Please Enter a valid Email Id!!","status"=>false),200);    
            }
        }
        else{
            $errormsg="";
            if(!$this->input->post('Email_Id')){
                $errormsg="Valid Email Id is Required !!!";
            }
            else if(!$this->input->post('Product_Url')){
                $errormsg="Valid Product URL is required !!!";
            }
            else{
                $errormsg="Invalid No of Parameters!!!";
            }
            $this->response(array("msg"=>$errormsg,"status"=>false),200);
        }
    }
    
    public function checkRates_get(){
        $result=$this->Customer->get_customers();    
        if($result){
            foreach ($result as $key => $value) {
                $checkData=array();
                switch($value['Product_Domain']){
                    case $this->sites[0] :
                    case $this->sites[1] :
                            $checkData=$this->update_Amazon_Product_Details($value);
                        break;
                    case $this->sites[2] :
                            $checkData=$this->update_Flipkart_Product_Details($value);
                        break;
                }
                if($checkData){
                    $result=$this->Customer->edit_customer($checkData);
                    if($result){
                        if($result['status']){
                            $Mail_status=SendMail($checkData,$value['Product_Price']);
                        }
                    }
                }
            }
            $this->response(array("status"=>true,"msg"=>"Completed Processing!! Mails are sent to Users whose product cost is reduced"),200);             
            // $this->response($Mail_status,200);
        }
        else{
            $this->response(array("msg"=>"No data to Update Request in the Database!!","status"=>false),404);
        }
    }
    
    // public function customer_put($id=''){
    //     if(!empty($id)){
    //         if(!empty($this->put('Customer_Name')) && !empty($this->put('Customer_Password')) && !empty($this->put('Customer_Phone_No')) && !empty($this->put('Customer_Email_ID')))
    //         {
    //             $customer=array(
    //                 "Customer_ID"=>$id,
    //                 "Customer_Name"=>trim($this->put('Customer_Name')),
    //                 "Customer_Password"=>trim(md5($this->put('Customer_Password'))),
    //                 "Customer_Phone_No"=>trim($this->put('Customer_Phone_No')),
    //                 "Customer_Email_ID"=>trim($this->put('Customer_Email_ID')),
    //                 "Customer_Joined_Date"=>trim($this->put('Customer_Joined_Date'))
    //             );
    //             if(!empty($result=$this->Customer->edit_customer($customer))){
    //                 $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
    //             }
    //             else{
    //                 $this->response(array("msg"=>"Failed To Store Customer","status"=>false),404);    
    //             }
    //         }
    //         else{
    //             $errormsg="";
    //             if(empty($this->put('Customer_Name'))){
    //                 $errormsg="Invalid Customer Name !!!";
    //             }
    //             else if(empty($this->put('Customer_Password'))){
    //                 $errormsg="Invalid Customer Password !!!";
    //             }
    //             else if(empty($this->put('Customer_Phone_No'))){
    //                 $errormsg="Invalid Customer Phone Number !!!";
    //             }
    //             else if(empty($this->put('Customer_Email_ID'))){
    //                 $errormsg="Invalid Customer Email ID !!!";
    //             }
    //             else{
    //                 $errormsg="Invalid No of Parameters!!!";
    //             }
    //             $this->response(array("msg"=>$errormsg,"status"=>false),404);
    //         }
    //     }
    //     else{
    //         $errormsg="Customer ID parameter is required!!!";
    //         $this->response(array("msg"=>$errormsg,"status"=>false),404);
    //     }
    // }

    // public function customer_delete($id=''){
    //     if(!empty($id)){
    //         $result=$this->Customer->delete_customers($id);    
    //         if(!empty($result)){
    //             $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
    //         }
    //     }
    //     else
    //     {
    //         $result=$this->Customer->delete_customers();    
    //         if(!empty($result)){
    //             $this->response(array("msg"=>$result['msg'],"status"=>$result['status']),200);
    //         }
    //     }
    // }

    public function amazon_products($email,$domain,$url,$processedURL){
        $path=$processedURL['path'];
        $splitURL  = explode('/', $path);
        $unique_Product_Path = implode('/', array_slice($splitURL, 0, 4));
        if((strpos($path, '/dp/') !== false) || (strpos($path, '/gp/product/') !== false)) {
            $this->website->loadHTMLFile($url);
            $Product_Image_URL_object=$this->website->getElementById('imgTagWrapperId')->getElementsByTagName('img');
            $Product_Image_URL_array=iterator_to_array($Product_Image_URL_object);
            $Product_Image_URL=strtok($Product_Image_URL_array[0]->getAttribute('src'),'?');
            if($this->website->getElementById('priceblock_ourprice')){
                $priceTag=$this->website->getElementById('priceblock_ourprice');
            }
            else{
                if($this->website->getElementById('priceblock_saleprice')){
                    $priceTag=$this->website->getElementById('priceblock_saleprice');
                }
            }
            if($priceTag){
                $finder = new DomXPath($this->website);
                $classname="currencyINR";
                $nodes = $finder->query("//td//span[contains(@class, '$classname')]");
                $length=$nodes->length;
                $nodes=iterator_to_array($nodes);
                if($length){
                    for($i=0;$i<$length;$i++){
                        $nodes[$i]->removeAttribute('class');
                    }
                    $currency="INR";
                }else{
                    $currency="USD";    
                }
                $product_price=trim($priceTag->nodeValue);
                if(strpos($product_price, '-') !== false){
                    $product_price=strtok($product_price, '-');
                }
            }else{
                $product_price="9999";
            }
            $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
            
            // Uncomment to enable shortenURL for images
            // if(updateShortURL($Product_Image_URL))
            // {
            //     $Product_Image_URL=updateShortURL($Product_Image_URL);
            // }
            ///////////////////////////////////////////////////////////////// -- After processing
            $customer=array(
                "Email_Id"=>$email,
                "Product_Domain"=>$domain,
                "Product_ID"=>$unique_Product_Path,
                "Product_Name"=>trim($this->website->getElementById('productTitle')->nodeValue),
                "Product_Price"=>$product_price,
                "Currency_Type"=>$currency,
                "Product_Image_URL"=>$Product_Image_URL
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
            $Product_Image_URL=strtok($finder->query("//div//div//img[contains(@class, 'sfescn')]")[0]->getAttribute('src'),'?');
            $product_Name=trim($Name[0]->nodeValue);
            $price = $finder->query("//div//div[contains(@class, '_1vC4OE _37U4_g')]");
            $priceTag=$price[0];
            if($priceTag){
                $product_price=trim($priceTag->nodeValue);
                if(strpos($product_price, '-') !== false){
                    $product_price=strtok($product_price, '-');
                }
            }else{
                $product_price="9999";
            }
            $product_Name = htmlentities($product_Name, null, 'utf-8');
            $product_Name = str_replace("&nbsp;", "", $product_Name);
            $currency="INR";
            $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
            
            
            // Uncomment to enable shortenURL for images
            // if(updateShortURL($Product_Image_URL))
            // {
            //     $Product_Image_URL=updateShortURL($Product_Image_URL);
            // }
            ///////////////////////////////////////////////////////////////// -- After processing
            $customer=array(
                "Email_Id"=>$email,
                "Product_Domain"=>$domain,
                "Product_ID"=>$unique_Product_Path,
                "Product_Name"=>$product_Name,
                "Product_Price"=>$product_price,
                "Currency_Type"=>$currency,
                "Product_Image_URL"=>$Product_Image_URL
            );
            return $customer;
        }
        else{
            return false;
        }
    }

    public function update_Amazon_Product_Details($product){
        $url="https://".$product['Product_Domain'].$product['Product_ID'];
        $LoadContent=$this->website->loadHTMLFile($url);
        if($LoadContent){
            if($this->website->getElementById('priceblock_ourprice')){
                $priceTag=$this->website->getElementById('priceblock_ourprice');
            }
            else
            {
                if($this->website->getElementById('priceblock_saleprice')){
                $priceTag=$this->website->getElementById('priceblock_saleprice');
                }
            }
            if($priceTag){
                $finder = new DomXPath($this->website);
                $classname="currencyINR";
                $nodes = $finder->query("//td//span[contains(@class, '$classname')]");
                $length=$nodes->length;
                $nodes=iterator_to_array($nodes);
                if($length){
                    for($i=0;$i<$length;$i++){
                        $nodes[$i]->removeAttribute('class');
                    }
                }
                $product_price=trim($priceTag->nodeValue);
                if(strpos($product_price, '-') !== false){
                    $product_price=strtok($product_price, '-');
                }
                $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
                $calculated_price=$this->Decide_LowPrice_Level($product['Product_Price']);
                if($product_price <= $calculated_price){
                    $product['Product_Price']=$product_price;
                    return $product;
                }
            }
        }
        
        return false;        
    }
    public function update_Flipkart_Product_Details($product){
        $url="https://".$product['Product_Domain'].$product['Product_ID'];
        $LoadContent=$this->website->loadHTMLFile($url);
        if($LoadContent){
            $finder = new DomXPath($this->website);
            $price = $finder->query("//div//div[contains(@class, '_1vC4OE _37U4_g')]");
            $price=iterator_to_array($price);
            $priceTag=$price[0];
            if($priceTag){
                $product_price=trim($priceTag->nodeValue);
                if(strpos($product_price, '-') !== false){
                    $product_price=strtok($product_price, '-');
                }
                $product_price = str_replace(' ', '', preg_replace('/[^A-Za-z0-9.\-]/', '', $product_price));
                $calculated_price=$this->Decide_LowPrice_Level($product['Product_Price']);
                if($product_price <= $calculated_price){
                    $product['Product_Price']=$product_price;
                    return $product;
                }
            }
        }

        return false;
    }
    public function Decide_LowPrice_Level($Price){
        switch($Price){
            case $Price < 1000:
                 $discount=0.1;                        // 10%
                 break;
            case $Price > 1000 && $Price < 10000:
                 $discount=0.05;                        // 5%
                 break;
            case $Price > 10000 && $Price < 15000:
                 $discount=0.04;                        // 4%
                 break;                 
            case $Price > 15000 && $Price < 20000:
                 $discount=0.03;                        // 3%
                 break;
            case $Price > 20000 && $Price < 25000:
                 $discount=0.02;                        // 2%
                 break;
            default:
                 $discount=0.01;                        // 1%
                 break;
        }
        $Price=$Price - ($Price * $discount);
        return $Price;
    }
    public function checkValidMailId($email){
        $domain = substr($email, strpos($email, '@') + 1);
        if(checkdnsrr($domain) !== FALSE) {
            return true;
        }
        return false;
    }
}
