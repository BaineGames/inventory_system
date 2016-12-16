<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src='https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js'></script>
    <script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>
    <script src="http://momentjs.com/downloads/moment.js"></script>
    <script src="./api.js"></script>
    <script src="./validations.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>

<ul class="nav nav-tabs">
    <li role="presentation"><a href="./index.php">Home</a></li>   
<?php

//nav should go here based on auth or non auth
if($auth){
    
?>    
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            Inventory<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="index.php?page=inventory">Manage</a></li>
            <li><a href="index.php?page=addInventory">New</a></li>                        
        </ul>
    </li>
    <li role="presentation"><a href="index.php?page=account">Account</a></li>
    <li role="presentation"><a href="index.php?page=buy">Buy Stuff</a></li>
    <li role="presentation"><a href="index.php?page=friends">Friends <span id='friend_count' class="badge"></span></a></li>
    <li role="presentation"><a href="index.php?page=messages">Messages <span id='message_count' class="badge"></span></a></li>
    <li role="presentation"><a href="index.php?page=logout">Logout 
    <?php echo $_SESSION['email'];?></a></li>
   
<?php
    
}else{
    
?>
  
    <li role="presentation"><a href="index.php?page=login">Login</a></li>

<?php
    
}

?>

</ul>
<script>

    $(document).ready(function(){
        api('user','getFriendRequestsCount',{},function(data){
           if(data.result.friend_count){
               $('#friend_count').text(data.result.friend_count)
           }
        });
        
        api('message','getMessageCount',{},function(data){
            if(data.result.message_count){
                $('#message_count').text(data.result.message_count)
            }
        });
    });

</script>