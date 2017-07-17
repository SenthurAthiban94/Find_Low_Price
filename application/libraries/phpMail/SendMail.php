<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'mail_lib/PHPMailerAutoload.php';

function SendMail($emailId,$hostname,$productURL,$price){
    $productPage_URL="https://".$hostname.$productURL;
    $toMailID=$emailId;
    $sender_Subject=$subject;
    $sender_Message=$message;
    
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
    $mail->FromName = "Mailer";
    // $mail->AddAddress("josh@example.net", "Josh Adams");
    $mail->AddAddress($toMailID);                  // name is optional
    $mail->AddReplyTo("senthurathibanprofile@gmail.com", "Information");

    
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');
    // $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    // $mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
    // $mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
    
    $mail->IsHTML(true);                                  // set email format to HTML

    $mail->Subject = "Cheers! Your Product Rate is Reduced";
    $mail->Body    = "This is the HTML message body <b>in bold!</b>";
    $mail->AltBody = "This is the body in plain text for non-HTML mail clients";

    // $mail->setFrom($sender_Mail, 'Messages from CheapAss '.$sender_Name);
    // $mail->addReplyTo($sender_Mail , $sender_Name);
    // $mail->addAddress('senthurathiban@gmail.com', 'Senthur Athiban');                     // Add a recipient
    // $mail->addAddress('ellen@example.com');                                            // Name is optional
    
    if(!$mail->Send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
        // return false;
        exit;
    }
    // return true;
    echo "Message Sent!";
}







// $return=json_encode(array("response"=>"error","status"=>"Unable to Send Message!"));

// if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])){
//     $sender_Name=$_POST['name'];
//     $sender_Mail=$_POST['email'];
//     $sender_Subject=$_POST['subject'];
//     $sender_Message=$_POST['message'];
        
//     $mail = new PHPMailer;

//     //$mail->SMTPDebug = 3;                                                               // Enable verbose debug output

//     $mail->isSMTP();                                                                      // Set mailer to use SMTP
//     $mail->Host = 'smtp.gmail.com';                                                       // Specify main and backup SMTP servers
//     $mail->SMTPAuth = true;                                                               // Enable SMTP authentication
//     $mail->Username = 'senthurathibanprofile@gmail.com';                                  // SMTP username
//     $mail->Password = 'Senthur@profile';                                                  // SMTP password
//     $mail->SMTPSecure = 'tls';                                                            // Enable TLS encryption, `ssl` also accepted
//     $mail->Port = 587;                                                                    // TCP port to connect to

//     $mail->setFrom($sender_Mail, 'PortFolio Message From '.$sender_Name);
//     $mail->addReplyTo($sender_Mail , $sender_Name);
//     $mail->addAddress('senthurathiban@gmail.com', 'Senthur Athiban');                     // Add a recipient
//     // $mail->addAddress('ellen@example.com');                                            // Name is optional
    
//     // $mail->addCC('cc@example.com');
//     // $mail->addBCC('bcc@example.com');

//     // $mail->addAttachment('/var/tmp/file.tar.gz');                                      // Add attachments
//     // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');                                 // Optional name
//     $mail->isHTML(true);                                                                  // Set email format to HTML

//     $mail->Subject = $sender_Subject;
//     $mail->Body    = "
//         <div>
//             Hi <b>Senthur</b>,
//         <div>
//         <div>
//             <p>
//             ".$sender_Message."
//             </p>
//         </div>
//     ";
//     $mail->AltBody = 'Hi Senthur, '.$sender_Message;

//     if(!$mail->send()) {
//         echo $return;
//         die;
//         // echo 'Mailer Error: ' . $mail->ErrorInfo;
//     } else {
//         $return=json_encode(array("response"=>"success","status"=>"Message Sent"));
//         echo $return;
//         die;
//     }
// }
// echo $return;
// die;