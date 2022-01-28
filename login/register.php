<?php
require_once "config.php";
require_once "../sendemail/attachmentmail.php";
session_start();
$username = $password = $confirm_password = $email = $phone = $vCode = "";
$username_err = $password_err = $confirm_password_err = $email_err = $phone_err = $vCode_err = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    //validate username
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Please enter a username.";
    }else{
        $username = trim($_POST['username']);
    }
    
    //validate password
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Please enter a password.";
    }elseif(strlen(trim($_POST['password']))<6){
        $password_err = "Password must have at least 6 characters.";
    }else{
        $password = trim($_POST['password']);
    }

    //validate email 
    function CheckEmail($email)
    {
        $dArr = array(
        '163.com','126.com','sina.com','yahoo.com.cn','yahoo.com','sohu.com','yeah.net','139.com',
        'tom.com','21cn.com','qq.com','foxmail.com','gmail.com','hotmail.com','263.net',
        'vip.qq.com','vip.163.com','vip.sina.com','vip.sina.com.cn','vip.foxmail.com',
        );
        if(empty($email)) return FALSE;
        list($e,$d) = explode('@', $email);
        if(!empty($e) && !empty($d))
        {
            $d = strtolower($d);
            if(!in_array($d,$dArr)) return FALSE;
            return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*/i', $e);
        }
        return FALSE;
    }
    if(empty(trim($_POST['email'])))
    {
        $email_err = "Please enter a email.";
    }
    elseif(CheckEmail(trim($_POST['email']))){
        $email = trim($_POST['email']);
    }else{
        $email_err = "Invalid email, check it.";
    }
    
    //validate phone
    if(empty(trim($_POST['phone'])))
    {
        $phone_err = "Please enter a phone number. ";
    }
    elseif(!preg_match('/^[1][3,4,5,7,8,9][0-9]{9}$/',trim($_POST['phone']))){
        $phone_err = "Invalid phone number, check it. ";
    }else{
        $phone = trim($_POST['phone']);
    }

    //validate confirm password
    if(empty(trim($_POST["confirm_password"])))
    {
        $confirm_password_err = "Please confirm password.";
    }else{
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($password_err)&&($password !=$confirm_password))
        {
            $confirm_password_err = "Password did not match.";
        }elseif(empty($password))
        {
            $confirm_password_err = "Enter a password first.";
        }
    }

    //validate vCode
    if(empty(trim($_POST["vCode"])))
    {
        $vCode_err = "Please enter the verification code. ";
    }else{
        $vCode = trim($_POST['vCode']);
        $gen_vCode = $_SESSION['vCode'];
        if(empty($vCode_err)&&($vCode!=$gen_vCode))
        {
            $vCode_err = "verification code did not match.";
        }
    }

    // check input errors before inserting in database
    if(empty($username_err)&&empty($password_err)&&empty($confirm_password_err)&&empty($email_err)&&empty($phone_err)&&empty($vCode_err))
    {
        $sql = "insert into users (usertype,username,password,email,phone) values (0,?,?,?,?)";

        if($stmt = mysqli_prepare($link,$sql))
        {
            mysqli_stmt_bind_param($stmt,'ssss',$param_username,$param_password,$param_email,$param_phone);

            //set parameters
            $param_username = $username;
            $param_password = password_hash($password,PASSWORD_DEFAULT);
            $param_email = $email ;
            $param_phone = $phone ;

            //attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
            {
                //send a email to prove registering successfully
                postmail_jiucool_com($email,"Congratulation!", $body = "祝贺您, 您已成功完成注册 (¬‿¬) ヾ(•ω•`)o");
                //redirect to login page
                header("location: login.php");
            }else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="check.js"></script> 

</head>
<body>
    <center>
    <div class="container">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id='form1'>
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>Username</label>
                <div class='col-sm-3'>
                    <input type="text" id='username' name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"  onkeyup='checkUserName(this.value)' value="<?php echo $username; ?>">
                    <span class="invalid-feedback" id='username_err'><?php echo $username_err; ?></span>
                </div>
            </div>    
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>Password</label>
                <div class='col-sm-3'>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
            </div>
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>Confirm Password</label>
                <div class='col-sm-3'>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
            </div>
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>Email</label>
                <div class='col-sm-3'>
                    <input type = "email" name='email' class='form-control <?php echo (!empty($email_err)) ? "is-invalid":'';?>' value="<?php echo $email; ?>" onkeyup='checkEmail(this.value)' id='email'>
                    <span class="invalid-feedback" id='email_err'><?php echo $email_err;?> </span>
                </div>
            </div>
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>Phone</label>
                <div class='col-sm-3'>
                    <input type = "phone" name='phone' class='form-control <?php echo (!empty($phone_err)) ? "is-invalid":'';?>' id='phone' onkeyup='checkPhone(this.value)' value="<?php echo $phone; ?>">
                    <span class="invalid-feedback" id='phone_err'><?php echo $phone_err;?></span>
                </div>
            </div>
            <div class="form-group row justify-content-center mb-3">
                <label class='col-form-label col-sm-2'>验证码</label>
                <div class='col-sm-3'>
                    <input type = "text" name='vCode' class='form-control <?php echo (!empty($vCode_err)) ? "is-invalid":'';?>' value="<?php echo $vCode; ?>">
                    <span class="invalid-feedback"><?php echo $vCode_err;?></span>
                </div>
            </div>
            <div class='py-3'>
                <img src="gen_code.php" alt="" id='AuthCode'>
                <a class='col-form-label' href="javascript:void(0)" onclick="document.getElementById('AuthCode').src='gen_code.php'"> 换一个? </a><br/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
    </center>
</body>
</html>