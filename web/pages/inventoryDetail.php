<hr><a href='?page=addItem&id=<?php echo $_GET['id']; ?>'>Add Item</a><hr><form id='inventoryDetail'>
    <div class="form-group">
        <label for="inv_name">Inventory Name:</label>
        <input type="text" class="form-control" id="inv_name">
    </div>
    <div class="form-group">
        <label for="inv_type">Type:</label>
        <select class="form-control" id='inv_type'>
           <option value=0>Private</option>
            <option value=1>Public</option> 
        </select>
       
    </div>
    <button type="button" id='updateBtn' class="btn btn-default">Update</button>   
</form>
<hr>
<table id='item_table' class='table table-hover'></table>




<script>

    $(document).ready(function(){
        
        var get_inv_id = <?php echo $_GET['id'];?>;
        
        var data = {};
        data.data = {};
        data.data.inventory_id = get_inv_id;

        api('items','getItemList',data,function(data){

            var item_id;
            var item_name;
            var item_co;
            var actions = '';
            var inv_name;
            var price;
            var inv_access = data.result.access_level;
            
            $('#inv_type option:eq('+inv_access+')').prop('selected', true)
            
            $('#inv_name').val(data.result.inventory_name);

            if(data.result.data){

                $('#item_table').append("<thead><tr><th>Item</th><th>Created On</th><th>Price</th><th>Actions</th></tr></thead><tbody></tbody>");

                for(var i=0;i<data.result.data.length;i++){

                    item_id = data.result.data[i].item_id;
                    item_name = data.result.data[i].item_name;
                    item_co = data.result.data[i].created_on;
                    price = data.result.data[i].price;
                    
                    actions = "<div class=row><div class='col-md-1'><a class='edit_item' item_id="+item_id+" href='./index.php?page=itemDetail&id="+item_id+"'><span class='glyphicon glyphicon-pencil'></span></a></div><div class='col-md-1'><a class='delete_item' item_id="+item_id+" href='#'><span class='glyphicon glyphicon-trash'></span></a></div></div>"; 
                    

                $('tbody').append("<tr item_id="+item_id+"><td>"+item_name+"</td><td>"+item_co+"</td><td>$"+price+"</td><td>"+actions+"</td></tr>");  
                }
                

            $('#item_table').DataTable();

            }else{
                $('.container').append("<p>You do not have any Items in this inventory. Click here to create one.</p>");

            }

            $('.delete_item').off().on('click',function(e){
                e.stopPropagation(); 
                var self = $(this).closest('tr');
                var data = {};
                data.data = {
                    "item_id" : $(this).attr('item_id')    
                };
                api('items','deleteItem',data,function(data){
                    $('#item_table').DataTable().row(self).remove().draw();
                });
            });
            
            $('#updateBtn').off().on('click',function(){
                
                var data = {};
                data.data = {
                    "inventory_id"  :   get_inv_id,
                    "inventory_name" : $('#inv_name').val(),
                    "inventory_type" : $('#inv_type').val()
                };
                api('inventory','updateInventory',data,function(data){
                   
                    if(data.result.state){
                        $.notify("Success","success");
                    }else{
                        $.notify(data.result.errorMsg,'error');
                    }
                    
                });
               
            });

        });




    });

</script>