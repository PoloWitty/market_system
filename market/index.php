<?php
#Macro Param
$pagesize=5;

session_start();

if(!isset($_SESSION['loggedin']) || ($_SESSION['loggedin']!==true))
{
    header('location: ../login/login.php');
    exit;
}elseif(isset($_SESSION['user_type']) && ($_SESSION['user_type']!==1)){
    //unset all of the session variables
    $_SESSION = array();
    
    //destory the session
    session_destroy();
    ?><script>
        alert('请使用商家账户登录');
        window.location='../login/login.php'
    </script><?php
    // header('location: ../login/login.php');//不能这样写, 这样写的话还没alert出来就会直接跳转回login.php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();//用来显示tooltip
        });
        function Overturn(p){
            var Str = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>';
            window.location=Str + "?currentpage=" + p;
        }
        function goPage(){
            var vtf = true;
            input_widget = document.getElementById('goPageNo')
            if(input_widget.value == ""){
                alert("请输入要跳转的页码！");
                input_widget.focus();
                vtf = false;
            }
            if(isNaN(input_widget.value)){
                alert("要跳转的页码必须为数字！");
                input_widget.select();
                vtf = false;
            }
            if(vtf) Overturn(input_widget.value);
        }
    </script>
</head>
<body>
        <!-- <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
            <p>
                </p> -->
                <div class="m-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                            <div class="navbar-nav">
                                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="nav-item nav-link active">Home</a>
                                <!-- <a href="#" class="nav-item nav-link">Profile</a> -->
                                <a href="../login/reset-password.php" class="nav-item nav-link">Reset Password</a>
                            </div>
                            <form class="d-flex" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id='search_form'>
                                <div class="input-group search-box">
                                    <input type="text" id='name' name="name" autocomplete="off" placeholder="Search product by name" class='form-control' value='<?php echo($name); ?>' />
                                    <button type="submit" class="btn btn-secondary "><i class="fa fa-search bi-search"></i></button>
                                </div>
                            </form>
                            <div class="navbar-nav">
                                <!-- <a href="#" class="nav-item nav-link">Login</a> -->
                                <a href="#" class="navbar-brand">Hello, <?php echo($_SESSION['username']);?>!</a>
                                <a href="../login/logout.php" class="nav-item nav-link ">Sign Out</a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

        <div class="wrapper">
            <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <a href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' ><h2 class="pull-left">Product Details</h2></a>
                        <a href="create.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add New Product</a>
                    </div>
                    <!-- <div class="mt-5 mb-3 clearfix search-box">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id='search_form'>
                            <input type="text" id='name' name="name" autocomplete="off" placeholder="Search product by name" class='pull-left' value='<?php echo($name); ?>' />
                            <a href="search.php" class="btn btn-secondary pull-right"><i class="fa fa-search"></i> Search Product</a>
                            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-search"></span> Search</button>
                        </form>
                    </div> -->
                    <?php
                    $name='';
                    // Include config file
                    require_once "config.php";
                    $currentpage=$_GET["currentpage"];
                    $sql = "SELECT * FROM product";
                    $result = mysqli_query($link_pd, $sql);
                    $row_num=mysqli_num_rows($result);

                    if ($row_num%$pagesize==0)
                        $totalpage=intval($row_num/$pagesize);
                    else
                        $totalpage=intval($row_num/$pagesize)+1;
                        
                    if ($currentpage=="") { 
                        $currentpage=1;
                    }
                    else
                    {
                        if (intval($currentpage)>intval($totalpage)){ 
                            $currentpage=intval($totalpage);
                        }
                        if (intval($currentpage<1)) { 
                            $currentpage=1;
                        }
                    }
                    $count = $row_num - (intval($currentpage)-1)*$pagesize;
                    if ($count > $pagesize) 
                        $count=$pagesize;

                    if($_SERVER['REQUEST_METHOD']=='POST')
                    {
                        //查找功能
                        if(!empty($_POST['name']))
                        {
                            $name=trim($_POST['name']);
                            // Attempt select query execution
                            $sql = "SELECT * FROM product WHERE name LIKE ?";
                            if($stmt = mysqli_prepare($link_pd,$sql))
                            {
                                mysqli_stmt_bind_param($stmt,'s',$param_term);

                                $param_term = $name.'%';

                                if(mysqli_stmt_execute($stmt))
                                {
                                    $result = mysqli_stmt_get_result($stmt);

                                    if(mysqli_num_rows($result)>0)
                                    {
                                        echo '<table class="table table-hover ">';
                                            echo "<thead class='table-light'>";
                                                echo "<tr>";
                                                    echo "<th>#</th>";
                                                    echo "<th>Name</th>";
                                                    echo "<th>Address</th>";
                                                    echo "<th>Price</th>";
                                                    echo "<th>Stock Number</th>";
                                                    echo "<th>Seller Code</th>";                                            
                                                    echo "<th>Action</th>";
                                                echo "</tr>";
                                            echo "</thead>";
                                            echo "<tbody>";
                                            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                                                echo "<tr>";
                                                    echo "<td>" . $row['id'] . "</td>";
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    $tmp=explode('\\\\',$row['address']);
                                                    echo "<td>" . $tmp[count($tmp)-1] . "</td>";
                                                    echo "<td>" . $row['price'] . "</td>";
                                                    echo "<td>" . $row['stockNumber'] . "</td>";
                                                    echo "<td>" . $row['sellerCode'] . "</td>";
                                                    echo "<td>";
                                                        echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                                        echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                                        echo '<a href="delete.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                                    echo "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</tbody>";                            
                                        echo "</table>";
                                    }else{
                                        echo '<p>No matches found</p>';
                                    }
                                    mysqli_free_result($result);
                                }else{
                                    echo 'error: could not able to execute $sql'.mysqli_error($link_pd);
                                }
                            }
                            mysqli_stmt_close($stmt);
                        }else{
                            echo '<p>Please enter a name to search</p>';
                        }
                    }else{
                        //正常显示所有记录
                        // Attempt select query execution
                        $sql = "SELECT * FROM product" . " ORDER BY id LIMIT ".($currentpage-1)*$pagesize.",".$pagesize; 
                        if($result = mysqli_query($link_pd, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo '<table class="table table-hover ">';
                                    echo "<thead class='table-light'>";
                                        echo "<tr>";
                                            echo "<th>#</th>";
                                            echo "<th>Name</th>";
                                            echo "<th>Address</th>";
                                            echo "<th>Price</th>";
                                            echo "<th>Stock Number</th>";
                                            echo "<th>Seller Code</th>";
                                            echo "<th>Action</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        $tmp=explode('\\\\',$row['address']);
                                        echo "<td>" . $tmp[count($tmp)-1] . "</td>";
                                        echo "<td>" . $row['price'] . "</td>";
                                        echo "<td>" . $row['stockNumber'] . "</td>";
                                        echo "<td>" . $row['sellerCode'] . "</td>";
                                        echo "<td>";
                                            echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                            echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                            echo '<a href="delete.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                            echo "</td>";
                                            echo "</tr>";
                                    }
                                    echo "</tbody>";
                                    echo "</table>"; ?>
                                    <table width=100% border=0 cellpadding=0 cellspacing=0 id='pageControlForm'>
                                    <tr>
                                    <td height=30>|
                                        <?php 
                                        if (($currentpage=="1") || ($totalpage==0))
                                        {
                                            ?><font color=Silver> 首 页 </font><?php 
                                        }else 
                                        {
                                            ?><a href="javascript:Overturn('1');"><font color=#006699> 首 页 </font></a><?php 
                                        }?>
                                    |<?php 
                                    if (($currentpage=="1") || ($totalpage==0))
                                    {
                                        ?><font color=Silver> 上 页 </font><?php 
                                        }else{
                                            ?><a href="javascript:Overturn('<?php echo strval(intval($currentpage)-1) ?>');"><font color=#006699> 上 页 </font></a><?php
                                        }?>
                                    |<?php 
                                    if ((intval($currentpage)==intval($totalpage)) || ($totalpage==0))
                                    {
                                        ?><font color=Silver> 下 页 </font><?php 
                                        }else{
                                            ?><a href="javascript:Overturn('<?php echo strval(intval($currentpage)+1) ?>');"><font color=#006699> 下 页 </font></a><?php 
                                        }?>
                                    |<?php 
                                    if ((intval($currentpage)==intval($totalpage)) || ($totalpage==0))
                                    {
                                        ?><font color=Silver> 末 页 </font><?php 
                                        }else{
                                            ?><a href="javascript:Overturn('<?php echo strval($totalpage) ?>');"><font color=#006699> 末 页 </font></a><?php 
                                        }?>
                                    |<A HREF="javascript:goPage();">跳转</A> 到<INPUT type="text" id="goPageNo" size=2 maxlength=6>页</td>
                    
                                    <td align=right>共<font color=red><?php echo $row_num?></font>条记录<br/>
                                    第<font color=red><?php echo $currentpage?></font>页/共<font color=red><?php echo $totalpage?></font>页</td>
                                    </tr>
                                    </table> <?php
                                // Free result set
                                mysqli_free_result($result);
                            } else{
                                echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                            }
                        } else{
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                    // Close connection
                    mysqli_close($link_pd);
                    ?>

                </div>
            </div>        
        </div>
    </div>
</body>
</html>