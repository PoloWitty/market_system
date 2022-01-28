<?php
require_once "config.php";

$input_name=$_GET['name'];
$result=array('error'=>'','name'=>'');
// header('Content-Type:application/json; charset=utf-8');

if(!preg_match('/^[a-zA-Z0-9_]+$/',trim($input_name))){
    // $username_err = "Username can only contain letters, numbers and underscores. ";
    $result['error']="Username can only contain letters, numbers and underscores. ";
}else{
    $sql= "SELECT id FROM users WHERE username = ?";

    if($stmt = mysqli_prepare($link,$sql))
    {
        //bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt,"s",$param_username);

        //set parameters
        $param_username = trim($input_name);

        //attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt))
        {
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt)==1)
            {
                // $username_err="This username is already taken. ";
                $result['error']="This username is already taken. ";
                $result['name']='';
            }else{
                // $username = trim($input_name);
                $result['name']=trim($input_name);
            }
        }else{
            // echo "Oops! Something went wrong. Please try again later. ";
            $result['error']="Oops! Something went wrong. Please try again later. ";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($link);

exit(json_encode($result));

?>