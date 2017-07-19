<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'mail_lib/PHPMailerAutoload.php';

function SendMail($updated_Product,$old_price){
    $productPage_URL="https://".$updated_Product['Product_Domain'].$updated_Product['Product_ID'];
    $toMailID=$updated_Product['Email_Id'];
    $productName=$updated_Product['Product_Name'];
    $new_price=$updated_Product['Product_Price'];
    $productImageURL=$updated_Product['Product_Image_URL'];
    ////////////////////////////////////////////////////////////////////////////////////////////////

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                                                               // Enable verbose debug output

    $mail->isSMTP();                                                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                                                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                                                               // Enable SMTP authentication
    $mail->Username = 'senthurathibanprofile@gmail.com';                                  // SMTP username
    $mail->Password = 'Senthur@profile';                                                  // SMTP password
    $mail->SMTPSecure = 'tls';                                                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                                                    // TCP port to connect to

    $mail->From = "senthurathibanprofile@gmail.com";
    $mail->FromName = "LowPrice Shopping";
    // $mail->AddAddress("josh@example.net", "Josh Adams");
    $mail->AddAddress($toMailID);                  // name is optional
    $mail->AddReplyTo("senthurathibanprofile@gmail.com", "Information");

    
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');
    // $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    // $mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
    // $mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
    
    $mail->IsHTML(true);                                  // set email format to HTML

//https://raw.githubusercontent.com/senthurathiban94/Find_Low_Price/master/images/Icon.png
    $mail->Subject = "Hurry! Your Product ".$productName." Cost is Reduced";
    $mail->Body    = '<html>
                    <head>
                    </head>
                    <body style="background-color:#e1e8f4;">
                    <a href='.$productPage_URL.' style="text-decoration:none;width:100%;height:100%;">
                        <h1 style="text-align:center;font-size:26px;padding-top:30px;">
                            <div style="color:darkslategrey;font-family: Cooper;padding-bottom:30px;">Cheers!!! Your Product\'s Price has been Reduced<br>Grab it soon before it Increases</div>
                            <img src='.$productImageURL.' style="height:250px;width:250px;"/>
                        </h1>
                        <div>
                            <h2 style="text-align:center;">
                                <span style="font-size:26px;color:darkslateblue;"><u>'.$productName.'</u></span>
                                <div style="padding:20px;color:darkslategrey;">
                                <span style="text-decoration: line-through;color:black;"><span style="min-width:500px;padding-left:20px;padding-right:20px;color:red;font-size:42px;">'.$old_price.' &#x20b9;</span></span>
                                Now At <span style="min-width:500px;padding-left:20px;padding-right:20px;color:limegreen;font-size:62px;">'.$new_price.' &#x20b9;</span>
                                </div>
                                <div style="padding:10px;padding-top:30px;padding-bottom:50px;">
                                    <input type="button" style="font-size:32px;background-color:#4286f4;color:white;width:200px;height:50px;" value="Buy Now"/>
                                </div>
                            </h2>
                        </div>
                        </a>
                    </body>
                </html>';
    $mail->AltBody = "This is the body in plain text for non-HTML mail clients";

    // $mail->setFrom($sender_Mail, 'Messages from CheapAss '.$sender_Name);
    // $mail->addReplyTo($sender_Mail , $sender_Name);
    // $mail->addAddress('senthurathiban@gmail.com', 'Senthur Athiban');                     // Add a recipient
    // $mail->addAddress('ellen@example.com');                                            // Name is optional
    
    if(!$mail->Send())
    {
        // echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    return true;

}