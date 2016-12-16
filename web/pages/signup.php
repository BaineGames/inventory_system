<h3>Signup</h3>
<form id='signupForm'>
    <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" class="form-control api_data" id="email">
    </div>
    <div class="form-group">
        <label for="display_name">Display Name:</label>
        <input type="text" class="form-control api_data" id="display_name">
    </div>
    <div class="form-group">
        <label for="pwd">Password:</label>
        <input type="password" class="form-control api_data" id="password">
    </div>
    <button type="button" id='btnSignup' class="btn btn-default">Signup</button>   
</form>

<script>

    $(document).ready(function(){

        $('#btnSignup').off().on('click',function(){

            if(formFilled() && validateEmail($('#email').val())){
                var data = {};
                data.data = {
                    "email" : $('#email').val(),
                    "password" : $('#password').val(),
                    "display_name" : $('#display_name').val()
                };
                api('user','signup',data,function(data){
                    if(data.result.state){
                        $.notify("Account Created","success");
                    }else{
                        $.notify(data.result.errorMsg,"error"); 
                    }
                });  
            }
        });
    });

    function formFilled(){
        var ret = (($('#email').val().length > 0 && $('#password').val().length > 0 && $('#display_name').val().length > 0) ? true : false);
        if(!ret){
            $.notify("Form Cannot Be Blank","error");
        }
        return ret;
    }

</script>