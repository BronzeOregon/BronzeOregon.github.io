<?php

//include config
require_once 'config.php';
$siteURL = $_GET['siteURL'];
$siteEmail = $_GET['siteEmail'];
$siteName = $_GET['siteName'];


//include subscriber;
require_once 'subscriber.php';
$subscriber = new Subscriber();


if(isset($_POST['subscribe'])){
    $errorMsg = '';
    //default response
    $response = array(
        'status' => 0,
        'msg' => 'Something went wrong, please try again after a short time.'
    );

    //input field validation
    if(empty($_POST['name'])){
        $pre = !empty($msg)?'<br/>':'';
        $errorMsg .= $pre.'Please enter your name.';
    }
    if(empty($_POST['email']) && !filter_var(!$_POST['email'], FILTER_VALIDATE_EMAIL)){
        $pre = !empty($msg)?'<br/>':'';
        $errorMsg .= $pre.'Please enter a valid email.';
    }

    //if validation was successful
    if(empty($errorMsg)){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $verify_code = md5(uniqid(mt_rand()));

        $con = array(
            'where' => array(
                'email' => $email
            ),
            'return_type' => 'count'
        );
        $prevRow = $subscriber->getRows($con);

        if($prevRow > 0){
            $response['msg'] = 'Your email already exists in our subscriber list.';
        } else {
            $data = array(
                'name' => $name,
                'email' => $email,
                'verify_code' => $verify_code
            );
            $insert = $subscriber->insert($data);

            if($insert){
            //verification email config
            $verifyLink = $siteURL.'subscribe.php?email_verify='.$verify_code;
            $subject = 'Confirm Subscription';

            $message = '<h1 style="font-size:22px;margin18px 0 0;padding:0;text-align:center;color:#3c7bb6">Email Confirmation</h1>
            <p style="color:#616471;text-align:left;padding-top:15px;padding-right:40px;padding-bottom:30px;padding-left:40px;font-size:15px">Thank you for signing up to the '.$siteName.' Newsletter! Please confirm your email address by clicking the link below.</p>
            <p style="text-align:center;">
                <a href="'.$verifyLink.'" style="border-radius:.25em;background-color:#4582e8;font-weight:400;min-width:180px;font-size:16px;line-height:100%;padding-top:18px;padding-right:30px;padding-bottom:18px;padding-left:30px;color:#ffffff;text-decoration:none">Confirm Email</a>
            </p>
            <br><p><strong>'.$siteName.' Admin</strong></p>';

            $headers = "MIME-Version 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\r";
            $headers .= "From: $siteName"." <".$siteEmail.">";

            //send the verification Email
            $mail = @mail($email, $subject, $message, $headers);

            if($mail){
                $response = array(
                    'status' => 1,
                    'msg' => 'A verification link has been sent to your email address, please check your email and verify your subscription.'
                );
            }
            }
        }
    }else {
        $response['msg'] = $errorMsg;
    }

    //return response
    echo json_encode($response);
}
?>

<?php

//include config
require_once 'config.php';

//include subscriber
require_once 'subscriber.php';
$subscriber = new Subscriber();

if(!empty($_GET['email_verify'])){
    $verify_code = $_GET['email_verify'];
    $con = array(
        'where' => array(
            'verify_code' => $verify_code
        ),
        'return_type' => 'count'
    );
    $rowNum = $subscriber->getRows($con);
    if($rowNum > 0){
        $data = array(
            'is_verified' => 1
        );
        $con = array(
            'verify_code' => $verify_code
        );
        $update = $subscriber->update($data, $con);
        if($update){
            $statusMsg = '<p class="success">Your email address has been verified successfully.</p>';
        }else{
            $statusMsg = '<p class="error">Email verification unsuccessful. Please try again.</p>';
        }
    }else{
        $statusMsg = '<p class="error">You have clicked on the wrong link, please check your email and try again.</p>';
    }
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Email Verification</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- web-fonts -->
<link href="//fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext" rel="stylesheet">

<link rel="stylesheet" type="stylesheet" href="style.css" />
</head>
<body class="subs">
<div class="container">
    <div class="subscribe box-sizing">
        <div class="sloc-wrap box-sizing">
            <div class="sloc-content">
                <div class="sloc-text">
                    <div class="sloc-header"><?php echo $statusMsg; ?></div>
                </div>
                <a href="<?php echo $siteURL; ?>" class="cwlink">Go to Site</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
}
?>