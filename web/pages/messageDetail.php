<hr>

<div class='convo'></div>
<hr>
<form id='sendMessage'>
    <div class="form-group">
       <input type=hidden id='my_id' value='<?php echo $_SESSION['user_id']; ?>' />
       <input type=hidden id='message_counter' />
        <input type="text" placeholder="Send Message" class="form-control" id="message_text">
    </div>

</form>

<script>

    $(document).ready(function(){
        
        $('#message_text').focus();
        
        $('.convo').empty();

        var data = {};
        data.data = {
            "conversation_id" : <?php echo $_GET['id']; ?>
        }

            api('message','getMessageDetail',data,function(data){

            var created_on;
            var message_id;
            var message_text;
            var receiver_id;
            var sender_id;
            var receiver_display_name;
            var sender_display_name;
            var message_part;
            var direction;
            var mdy_date;
            var sent_time;
            var prev_date;
            
            var display_name_class;
            
            var my_display_name = '<?php echo $_SESSION['display_name']; ?>' ;
            
            if(data.result.data){

            for(var i=0;i<data.result.data.length;i++){
            message_part = '';
            
            created_on = moment(data.result.data[i].created_on);
            mdy_date = created_on.format("MM-DD-YYYY");
            sent_time = created_on.format("hh:mm A");
            
            message_id = data.result.data[i].message_id;
            message_text = data.result.data[i].message_text;
            receiver_id = data.result.data[i].receiver_id;
            sender_id = data.result.data[i].sender_id;
            direction = data.result.data[i].direction;
            sender_display_name = data.result.data[i].sender_display_name;
            receiver_display_name = data.result.data[i].receiver_display_name;
            
            display_name_class = (my_display_name == sender_display_name ?  'this_is_me' : 'this_is_not_me' );
            
            if(mdy_date != prev_date){
                message_part += "<h3><small>"+mdy_date+"</small></h3>";
        }
            prev_date = mdy_date;
            
            
        message_part += "<div class='row message " + direction + "'>";
            
            
        message_part += "<div class='col-sm-1'>"+sent_time+"</div><div class='"+display_name_class+" col-sm-2'>"+sender_display_name+"</div><div class='col-sm-8'>"+message_text+"</div>";
        message_part += "</div>";
            
            
            
            $('.convo').append(message_part);

        }
                      $('#message_counter').val(i);
    
    
    setInterval(function(){ 
    
        var data = {};
        data.data = {
            "conversation_id" : <?php echo $_GET['id']; ?>
        };

        console.log(data);
    
        api('message','getConversationMessageCount',data,function(data){
           
            if(data.result.message_count != $('#message_counter').val()){
            window.location.reload();
        }
            
        });
    
    }, 5000);
    
    
    } 
                      $('form').off().on('submit',function(e){
        e.preventDefault();

        var message = $('#message_text').val();

        if(message.length == 0){
            $('#message_text').notify("Cannot send empty message",'error');
            return false;
        }

        var my_id = $('#my_id').val();
        var other_id = (my_id == sender_id ? receiver_id : sender_id);
        console.log(other_id);    
        
        
        var data = {};
        data.data = {

            "conversation_id" :  <?php echo $_GET['id']; ?>,
            "sender_id" :  my_id,
            "receiver_id" :  other_id,
            "message_text" :  message

        }

        api('message','replyMessage',data,function(data){
            if(data.result.state){
                $('#sendMsgBtn').notify("Message Sent","success");
            }else{
                $('#sendMsgBtn').notify(data.result.errorMsg,'error');
            }
        });




        window.location.href = window.location.href;
    });

                      
                      
                      
                      });
    
    
    
   




    });


</script>


<style>

    .this_is_me {
        color:darkorchid;
        font-weight: 800;
    }
    
    .this_is_not_me {
        color:darkcyan;
        font-weight: 800;
    }


</style>