<h3>Login</h3>
   <form id='loginForm'>
    <div class="form-group">
        <label for="email">Email address:</label>
        <input type="email" class="form-control api_data" id="email">
    </div>
    <div class="form-group">
        <label for="pwd">Password:</label>
        <input type="password" class="form-control api_data" id="password">
    </div>
       <button type="button" id='btnLogin' par='loginForm' lib='user' fn='login' class="btn btn-default">Login</button>
       <a id='btnSignup' par='loginForm' lib='user' fn='signup' href='?page=signup'>Or Signup</a>    
</form>

<script>

    $(document).ready(function(){
        
        
        $('form input').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('#btnLogin').click();
            }
        });
        
        $('#btnLogin').off().on('click',function(){
            if($('#password').val().length > 0 && validateEmail($('#email').val())){
                var data = {};
                data.data = {
                    "email" : $('#email').val(),
                    "password" : $('#password').val()
                };
                api('user','login',data,function(data){
                   
                    if(data.result.state){
                        window.location.href = './index.php';
                    }else{
                        $.notify(data.result.errorMsg,"error"); 
                    }
                });  
            }


        });
    });
    
    
    function formFilled(){
        var ret = (($('#email').val().length > 0 && $('#password').val().length > 0) ? true : false);
        if(!ret){
            $.notify("Form Cannot Be Blank","error");
        }
        return ret;
    }

</script>