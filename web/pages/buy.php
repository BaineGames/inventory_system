<hr><table id='buy_table' class='table table-hover'></table>






<script>

$(document).ready(function(){
    
    api('items','getSellableItems',{},function(data){
        
        var item_id;
        var item_name;
        var price;
        var actions;
        
        if(data.result.data){

            $('#buy_table').append("<thead><tr><th>Item Name</th><th>Price</th><th>Action</th></tr></thead><tbody></tbody>");

            for(var i=0;i<data.result.data.length;i++){
                item_id = data.result.data[i].item_id;
                item_name = data.result.data[i].item_name;
                price = data.result.data[i].price;
                 
                actions = "<div class='row'><div class='col-md-1'><a class='icon message_seller' href='./index.php?page=messageSeller&item_id="+item_id+"'><span class='glyphicon glyphicon-envelope'></span></a></div>";
                
                
                $('tbody').append("<tr item_id="+item_id+"><td>"+item_name+"</td><td>$"+price+"</td><td>"+actions+"</td></tr>");
            }
            
            $('#buy_table').dataTable();
            
            
        }else{
            $('.container').append("<p>There is nothing to buy :(</p>");
        }
    });
    
    
    
});





















</script>