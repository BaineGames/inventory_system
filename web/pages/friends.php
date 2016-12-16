<div class='pending_requests'></div>

<hr>
<form id='searchFriends'>
    <div class="form-group">
        <label for="search_name">Friends Display Name:</label>
        <input type="text" class="form-control" id="search_name">
    </div>
    
    <button type="button" id='searchFriendsBtn' class="btn btn-default">Search</button>   
</form>
<hr>

<div class='display'>
</div>
<script>
    
    

$(document).ready(function(){

    api('user','getFriendRequests',{},function(data){
        if(data.result.data){
            
            var display_name;
            var request_id;
            var actions;

            $('.pending_requests').empty();

            $('.pending_requests').append("<table id='incoming_requests_table' class='table table-hover'></table>");

            $('#incoming_requests_table').append("<thead><tr><th>Display Name</th><th>Actions</th></tr></thead><tbody></tbody>");
            
            for(var i=0;i<data.result.data.length;i++){
                display_name = data.result.data[i].display_name;
                request_id = data.result.data[i].request_id;
                
                actions = "<div class='row'><div class='col-md-1'><a href=# req_id="+request_id+" class='confirm_request'><span  class='glyphicon glyphicon-ok'></span></a></div>";
                
                actions += "<div class='col-md-1'><a href=# req_id="+request_id+"  class='delete_request'><span  class='glyphicon glyphicon-remove'></span></a></div></div>";

                $('#incoming_requests_table > tbody').append("<tr><td>"+display_name+"</td><td>"+actions+"</td></tr>");
            }
            
            $('#incoming_requests_table').dataTable({
                searching: false,
                paging:false
            } );
            
            $('.confirm_request').off().on('click',function(){
               var request_id = $(this).attr('req_id');
                var self = $(this).closest('tr');
                var data = {};
                data.data = {
                    "request_id" : request_id
                }
                api('user','confirmFriendRequest',data,function(data){
                    if(data.result.state){
                        $.notify("Success","success");
                        $('#incoming_requests_table').DataTable().row(self).remove().draw();
                    }else{
                        $.notify(data.result.errorMsg,'error');
                    }
                });
            });
            
            $('.delete_request').off().on('click',function(){
                var request_id = $(this).attr('req_id');
                var self = $(this).closest('tr');
                var data = {};
                data.data = {
                    "request_id" : request_id
                }
                api('user','deleteFriendRequest',data,function(data){
                    if(data.result.state){
                        $.notify("Success","success");
                        $('#incoming_requests_table').DataTable().row(self).remove().draw();
                    }else{
                        $.notify(data.result.errorMsg,'error');
                    }
                });
            });
        }

    });
    
    $('form input').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            $('#searchFriendsBtn').click();
            $(this).val('');
            return false;
        }
    });
   
    $('#searchFriendsBtn').off().on('click',function(){
        
        if(!$('#search_name').val()){
            $(this).notify("Search Cannot Be Blank","error");
            return false;
        }
       
        var data = {};
        
        data.data = {
            "display_name" : $('#search_name').val()
        }
        
        api('user','searchFriends',data,function(data){
           
            var display_name;
            var request_status;
            var actions;

            if(data.result.data){
                
                $('.display').empty();
                
                $('.display').append("<table id='friends_table' class='table table-hover'></table>");

                $('#friends_table').append("<thead><tr><th>Display Name</th><th>Actions</th></tr></thead><tbody></tbody>");

                for(var i=0;i<data.result.data.length;i++){
                    display_name = data.result.data[i].display_name;
                    request_status = data.result.data[i].request_status;
                    
                    if(request_status == 1){
                        actions = "<div class='row'><div class='col-md-1'><a href =# class='remove_friend'>Pending</a></div>";
                    }else if(request_status == 2){
                        actions = "<div class='row'><div class='col-md-1'><a class='remove_friend' href='#'><span  class='glyphicon glyphicon-minus'></span></a></div>";
                    }else{
                        actions = "<div class='row'><div class='col-md-1'><a class='add_friend' href='#'><span  class='glyphicon glyphicon-plus'></span></a></div>";
                    }
                   
                    $('#friends_table > tbody').append("<tr><td>"+display_name+"</td><td>"+actions+"</td></tr>");
                }

                $('#friends_table').dataTable({
                    searching: false
                } );
                
                $('.add_friend').off().on('click',function(){
                   
                    var data = {};
                    
                    data.data = {
                        "display_name" : $(this).closest('td').prev().text()
                    }
                    
                    api('user','addFriend',data,function(data){
                        if(data.result.state){
                            $.notify("Success","success");
                            $('#searchFriendsBtn').click();
                        }else{
                            $.notify(data.result.errorMsg,'error');
                        }
                    });
                    
                    
                });
                
                $('.remove_friend').off().on('click',function(){

                    var data = {};
                    var self = $(this).closest('tr');
                   
                    data.data = {
                        "display_name" : $(this).closest('td').prev().text()
                    }

                    api('user','removeFriend',data,function(data){
                        if(data.result.state){
                            $.notify("Success","success"); $('#friends_table').DataTable().row(self).remove().draw();
                            $('#searchFriendsBtn').click();
                        }else{
                            $.notify(data.result.errorMsg,'error');
                        }
                    });


                });


            }else{
                $('.display').empty().html("<p>No Friends With That Name</p>");
            }
        });
        
        
        
    });
    
    
    
    
});

</script>