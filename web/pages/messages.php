<hr><table id='messages_table' class='table table-hover'></table>


<script>

$(document).ready(function(){
   
    
    api('message','getConversations',{},function(data){
        
        console.log(data);
       if(data.result.data){
           
           var conversation_id;
           var item_name;
           var display_name;
           var last_updated_on;
           var new_messages;
           var actions;
           var new_message_icon;
           
           $('#messages_table').append("<thead><tr><th>Item</th><th>Talking With</th><th>Most Recent</th><th>Actions</th></tr></thead><tbody></tbody>");
           
           for(var i=0;i<data.result.data.length;i++){
               conversation_id = data.result.data[i].conversation_id;
               item_name = data.result.data[i].item_name;
               display_name = data.result.data[i].display_name;
               last_updated_on = data.result.data[i].last_updated_on;
               new_messages = data.result.data[i].new_messages;
               
               if(new_messages == 0){
                   new_message_icon = "<a href=#><span  class='glyphicon glyphicon-exclamation-sign'></span></a>";
               }else{
                   new_message_icon = "";
               }
               
               actions ="<div class='row'><div class='col-md-1'><a class='delete_convo' conversation_id="+conversation_id+" href='#'><span  class='glyphicon glyphicon-trash'></span></a></div><div class='col-md-1'><a href='./index.php?page=messageDetail&id="+conversation_id+"'><span  class='glyphicon glyphicon-envelope'></span></a></div>";
               
               $('#messages_table > tbody').append("<tr><td>"+new_message_icon+" "+item_name+"</td><td>"+display_name+"</td><td>"+last_updated_on+"</td><td>"+actions+"</td></tr>");
           }
           
           $('#messages_table').dataTable();
           
           $('.delete_convo').off().on('click',function(){
               var data = {};
               var self = $(this).closest('tr');
               data.data = {
                   "conversation_id" : $(this).attr('conversation_id') 
               };
               
               api('message','deleteConversation',data,function(data){
                   $('#messages_table').DataTable().row(self).remove().draw();
               });
           });
           
           
           
       }
    });
    
    
    
});
    
    
    
</script>