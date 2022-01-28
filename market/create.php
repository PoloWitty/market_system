<?php

require_once "config.php";
$name = $file_address = $price = $stockNumber = $sellerCode = "";
$name_err = $address_err = $price_err = $stockNumber_err= $sellerCode_err= "";

function upload_file($file_widget_name)
{
    /*
    addslashes() 函数在指定的预定义字符前添加反斜杠。
    这些预定义字符是：   
    单引号 (') 
    双引号 (") 
    反斜杠 (\) 
    NULL 
    */
    //在程序中，有时我们会看到这样的路径写法，"D:\\Driver\\Lan" 也就是两个反斜杠来分隔路径。
    $path=AddSlashes(dirname(__FILE__))  . "\\\\upload\\\\";

    $files=$file_widget_name;     

    if ($_FILES[$files]["error"] > 0)
    {
        echo "Error: " . $_FILES[$files]["error"] . "<br />";
    }

    /*
    通过使用 PHP 的全局数组 $_FILES，你可以从客户计算机向远程服务器上传文件。

    第一个参数是表单的 input name，
    第二个下标可以是 "name", "type", "size", "tmp_name" 或 "error"。就像这样：

    $_FILES["file"]["name"] - 被上传文件的名称 
    $_FILES["file"]["type"] - 被上传文件的类型 
    $_FILES["file"]["size"] - 被上传文件的大小，以字节计 
    $_FILES["file"]["tmp_name"] - 存储在服务器的文件的临时副本的名称 
    $_FILES["file"]["error"] - 由文件上传导致的错误代码 
    */     	

    //is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的    	
    if (is_uploaded_file($_FILES[$files]['tmp_name'])) {
        $filename = $_FILES[$files]['name'];
        $localfile = $path . $filename;
        move_uploaded_file($_FILES[$files]['tmp_name'], $localfile);
    }
    return $path.$filename;
}

if($_SERVER['REQUEST_METHOD']=='POST')
{
    //validate name
    $input_name = trim($_POST['name']);
    if(empty($input_name))
    {
        $name_err = "Please enter a name.";
    }elseif(!filter_var($input_name,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name, no number is allowed.";
    }else{
        $name= $input_name;
    }

    //validate file address
    $file_widget_name='afile';
    $input_file = $_FILES[$file_widget_name]["name"];#不能用trim($_POST['afile']), 会出问题

    if(empty($input_file))
        $address_err = "Please select a file.";


    //validate price
    $input_price = trim($_POST['price']);
    if(empty($input_price))
    {
        $price_err = "Please enter the price. ";
    }elseif(!ctype_digit($input_price)){
        $price_err = "Please enter a positive integer value.";
    }else{
        $price = $input_price;
    }
    
    //validate stockNumber
    $input_stockNumber = trim($_POST['stockNumber']);
    if(empty($input_stockNumber))
    {
        $stockNumber_err = "Please enter the stockNumber. ";
    }elseif(!ctype_digit($input_stockNumber)){
        $stockNumber_err = "Please enter a positive integer value.";
    }else{
        $stockNumber = $input_stockNumber;
    }
    
    //validate sellerCode
    $input_sellerCode = trim($_POST['sellerCode']);
    if(empty($input_sellerCode))
    {
        $sellerCode_err = "Please enter the sellerCode. ";
    }elseif(!ctype_digit($input_sellerCode)){
        $sellerCode_err = "Please enter a positive integer value.";
    }else{
        $sellerCode = $input_sellerCode;
    }

    //check input err before inserting in database
    if(empty($name_err)&&empty($address_err)&&empty($price_err)&&empty($stockNumber_err)&&empty($sellerCode_err))
    {
        $sql=  "INSERT INTO product (name,address,price,stockNumber,sellerCode) VALUES (?,?,?,?,?)";

        if($stmt = mysqli_prepare($link_pd,$sql))
        {
            mysqli_stmt_bind_param($stmt,'ssiii',$param_name,$param_address,$param_price,$param_stockNumber,$param_sellerCode);

            $param_name= $name;
            $file_address = upload_file($file_widget_name);
            $param_address = $file_address;
            $param_price = $price;
            $param_stockNumber= $stockNumber;
            $param_sellerCode= $sellerCode;
            if(mysqli_stmt_execute($stmt))
            {
                //records create successfully. redirect to landing page
                header('location: index.php');
                exit;
            }else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link_pd);
}
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Address</label> <br/>
                                <input type="file" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" tabindex="1" name="afile" >
                            <span class="invalid-feedback"><?php echo $address_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                            <span class="invalid-feedback"><?php echo $price_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Stock Number</label>
                            <input type="text" name="stockNumber" class="form-control <?php echo (!empty($stockNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $stockNumber; ?>">
                            <span class="invalid-feedback"><?php echo $stockNumber_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Seller Code</label>
                            <input type="text" name="sellerCode" class="form-control <?php echo (!empty($sellerCode_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $sellerCode; ?>">
                            <span class="invalid-feedback"><?php echo $sellerCode_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>