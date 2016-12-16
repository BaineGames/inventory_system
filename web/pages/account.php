<hr>
<form id='updateAccountForm'>
    <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" class="form-control api_data" id="email">
    </div>
    <div class="form-group">
        <label for="display_name">Display Name:</label>
        <input type="text" class="form-control api_data" id="display_name">
    </div>
    <button type="button" id='btnUpdateAccount' class="btn btn-default">Update</button>   
</form>

<script>

    $(document).ready(function(){
        
        api('user','getAccount',{},function(data){
            $('#email').val(data.result.email);
            $('#display_name').val(data.result.display_name);
        
        });
        
        
        $('#btnUpdateAccount').off().on('click',function(){
           
            var data = {};
            
            data.data = {
              
                "email" : $('#email').val(),
                "display_name" : $('#display_name').val()
            };
            
            api('user','updateAccount',data,function(data){
               if(data.result.state){
                   $('#btnUpdateAccount').notify("Success",'success');
               }else{
                   $('#btnUpdateAccount').notify(data.result.errorMsg,'error');
               }
            });
            
        });
        
    }); 
    
</script>