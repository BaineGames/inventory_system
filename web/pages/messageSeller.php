<hr><form id='messageSeller'>
    <div class="form-group">
        <label>From:</label><span id='msgFrom'> <?php echo $_SESSION['display_name'];?></span><br>
        <label>To:</label> <span id='msgTo'></span><br>
        <label>RE:</label> <span id='msgRE'></span> 
    </div>
    <div class="form-group">
        <label for="msgText">Message:</label>
        <textarea class="form-control" id='msgText'></textarea>

    </div>
    <input type=hidden id='item_id' value='<?php echo $_GET['item_id'];?>' />
    <input type=hidden id='item_owner_id' />
    <input type=hidden id='sender_id' value='<?php echo $_SESSION['user_id'];?>' />
    <button type="button" id='sendMsgBtn' class="btn btn-default">Send</button>   
</form>

<script>

$(document).ready(function(){
   
    var data = {};
    data.data = {
        "item_id" : $('#item_id').val()
    };
    
    api('items','getItemDetail',data,function(data){
        $('#msgTo').text(data.result.display_name);
        $('#msgRE').text(data.result.item_name);
        $('#item_owner_id').val(data.result.user_id);
    });
    
    $('#sendMsgBtn').off().on('click',function(){
       var data = {};
        data.data = {
            
            "item_id" :  $('#item_id').val(),
            "sender_id" :  $('#sender_id').val(),
            "receiver_id" :  $('#item_owner_id').val(),
            "message_text" :  $('#msgText').val()
            
        }
        
        api('message','sendMessage',data,function(data){
            if(data.result.state){
                $('#sendMsgBtn').notify("Message Sent","success");
            }else{
                $('#sendMsgBtn').notify(data.result.errorMsg,'error');
            }
        });
    });
    
});
    

</script>