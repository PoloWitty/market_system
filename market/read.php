<?php

//check existence of id param before processing further
if(isset($_GET['id'])&&!empty(trim($_GET['id'])))
{
    require_once "config.php";
    $sql = "SELECT * FROM product WHERE id = ?";

    if($stmt=mysqli_prepare($link_pd,$sql))
    {
        mysqli_stmt_bind_param($stmt,'i',$param_id);

        $param_id=trim($_GET['id']);

        if(mysqli_stmt_execute($stmt))
        {
            $result=mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result)==1)
            {
                //fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop
                $row=mysqli_fetch_array($result,MYSQLI_ASSOC);

                //retrieve individual field value
                $name=$row['name'];
                $address=$row['address'];
                $price=$row['price'];
                $stockNumber=$row['stockNumber'];
                $sellerCode=$row['sellerCode'];
            }else{
                //url doesn't contain valid id parameter. redirect to error page
                header('location: error.php');
                eixt;
            }
        }else{
            echo "Oops! Something went wrong. Please try again.";
        }
    }
    mysqli_stmt_close($stmt);

    mysqli_close($link_pd);
}else{
    //url doesn't contain id parameter. redirect to error page
    header('location: error.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
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
                    <h1 class="mt-5 mb-3">View Record</h1>
                    <div class="form-group">
                        <label>Name</label>
                        <p><b><?php echo $row["name"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <p><b><?php echo $row["address"]; ?></b></p>
                        <img src="<?php 
                        $dir_name=Addslashes(dirname(dirname(dirname(__FILE__))));
                        echo(substr($row['address'],strlen($dir_name)+1));
                        //FIXME:此处被写死了
                        ?>"  alt="pic"  height="200" style='rounded'/>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <p><b><?php echo $row["price"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Stock Number</label>
                        <p><b><?php echo $stockNumber; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Seller Code</label>
                        <p><b><?php echo $sellerCode; ?></b></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
