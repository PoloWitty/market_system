<?php

function postmail_jiucool_com($to,$subject = "",$body = ""){

    //Author:Jiucool WebSite: http://www.jiucool.com 
    //$to ��ʾ�ռ��˵�ַ $subject ��ʾ�ʼ����� $body��ʾ�ʼ�����
    error_reporting(E_ALL);
    //error_reporting(E_STRICT);

//  date_default_timezone_set("Asia/Shanghai");//�趨ʱ��������

    require_once('class.phpmailer.php');
    include("class.smtp.php"); 
    $mail             = new PHPMailer(); //newһ��PHPMailer�������
    $body             = eregi_replace("[\]",'',$body); //���ʼ����ݽ��б�Ҫ�Ĺ���
    //$mail->CharSet ="UTF-8";//�趨�ʼ����룬Ĭ��ISO-8859-1����������Ĵ���������ã���������
    $mail->CharSet ="gb2312";//�趨�ʼ����룬Ĭ��ISO-8859-1����������Ĵ���������ã���������
    $mail->IsSMTP(); // �趨ʹ��SMTP����
    $mail->SMTPDebug  = 1;                     // ����SMTP���Թ���
                                           // 1 = errors and messages
                                           // 2 = messages only

    $mail->SMTPAuth   = true;                  // ���� SMTP ��֤����
    $mail->SMTPSecure = "ssl";                 // ��ȫЭ��
    $mail->Host       = "smtp.163.com";      // SMTP ������
    $mail->Port       = 465;                   // SMTP�������Ķ˿ں�
    $mail->Username   = "php_mailer@163.com";  // SMTP�������û���
    $mail->Password   = "123456";            // SMTP����������
    $mail->SetFrom('php_mailer@163.com', 'php_mailer');
    $mail->AddReplyTo("php_mailer@163.com","php_mailer");
    $mail->Subject    = $subject;
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer! - From www.jiucool.com"; // optional, comment out and test
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, "zhou_jianlan");
    //$mail->AddAttachment("images/phpmailer.gif");      // attachment 
    //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
    if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!��ϲ���ʼ����ͳɹ���";
        }
    }
    
postmail_jiucool_com( "zhoujl1999@163.com","����","�����ʼ�������");   
?>