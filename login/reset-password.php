<?php
function show_table($link){
    // Attempt select query execution
    $sql = "SELECT * FROM users";
    if($result = mysqli_query($link, $sql)){
        if(mysqli_num_rows($result) > 0){
            echo "<table>";
                echo "<tr>";
                    echo "<th>id</th>";
                    echo "<th>username</th>";
                    echo "<th>password</th>";
                echo "</tr>";
            while($row = mysqli_fetch_array($result)){
                echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['password'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else{
            echo "No records matching your query were found.";
        }
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}

session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!=true)
{
    header('location: login.php');
    exit;
}

require_once 'config.php';

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

if($_SERVER['REQUEST_METHOD']=='POST')
{
    //validate new password
    if(empty(trim($_POST['new_password'])))
    {
        $new_password_err= "Please enter the new password.";
    }elseif(strlen(trim($_POST['new_password']))<6){
        $new_password_err = "Password must have at least 6 characters.";
    }else{
        $new_password = trim($_POST['new_password']);
    }

    //validate confirm password
    if(empty(trim($_POST['confirm_password'])))
    {
        $confirm_password_err = "Please confirm the password.";
    }else{
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($new_password_err)&&($new_password!=$confirm_password))
        {
            $confirm_password_err = "Password did not match.";
        }
    }

    //check input error before updating the database
    if(empty($new_password_err)&&empty($confirm_password_err))
    {
        $sql = "UPDATE qing_zhou.users SET password = ? WHERE id = ?";

        if($stmt = mysqli_prepare($link,$sql))
        {
            mysqli_stmt_bind_param($stmt,'si',$param_password,$param_id);

            $param_password = password_hash($new_password,PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            //attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
            {
                //password updated sucessfully.destroy the session, and redirect to login page
                session_destroy();
                header('location: login.php');
                exit;
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="../market/index.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>