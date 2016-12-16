<hr><a href='http://localhost/inv_sys/web/index.php?page=addInventory'>Add Inventory</a>

<hr>


<table id='inv_table' class='table table-hover'></table>

<script>

$(document).ready(function(){
    
    api('inventory','getInventoryList',{},function(data){
        
        var inv_id;
        var inv_name;
        var inv_co;
        var inv_item_count;
        var inv_access_level;
        var inv_value;
        var actions;
        
        if(data.result.data){
            
            $('#inv_table').append("<thead><tr><th>Inventory Name</th><th>Created On</th><th>Number of Items</th><th>Value</th><th>Actions</th></tr></thead><tbody></tbody>");
            
        
        
        for(var i=0;i<data.result.data.length;i++){
            
            inv_id = data.result.data[i].inventory_id;
            inv_name = data.result.data[i].inventory_name;
            inv_co = data.result.data[i].created_on;
            inv_item_count = data.result.data[i].item_count;
            inv_access_level = data.result.data[i].access_level;
            inv_value = data.result.data[i].total;
            
            actions = "<div class='row'><div class='col-md-1'><a class='edit_item' inv_id="+inv_id+" href='./index.php?page=inventoryDetail&id="+inv_id+"'><span class='glyphicon glyphicon-pencil'></span></a></div>";
            
            if(inv_access_level == 0){
                actions += "<div class='col-md-1'><a class='toggle_access_level' access_level="+inv_access_level+" inv_id="+inv_id+" href='#'><span  class='glyphicon glyphicon-eye-open'></span></a></div>";
            }else{
                actions += "<div class='col-md-1'><a class='toggle_access_level' access_level="+inv_access_level+" inv_id="+inv_id+" href='#'><span  class='glyphicon glyphicon-eye-close'></span></a></div>";
            }
            
            
            if(inv_item_count == 0){
                actions += "<div class='col-md-1'><a class='icon delete_inventory' inv_id="+inv_id+" href='#'><span class='glyphicon glyphicon-trash'></span></a></div>";
            }else{
                actions += "<div class='col-md-1'></div>";
            }
            
            actions += "</div>";
            
            $('tbody').append("<tr inv_id="+inv_id+"><td>"+inv_name+"</td><td>"+inv_co+"</td><td>"+inv_item_count+"</td><td>$"+inv_value+"</td><td>"+actions+"</td></tr>");  
        }
        
            $('#inv_table').DataTable();
            
        }else{
            $('.container').append("<p>You do not have any inventories setup. Click here to create one.</p>");
            
        }
        
        $('.toggle_access_level').off().on('click',function(e){
            e.stopPropagation();
            var toggled_level = ($(this).attr('access_level') == "1") ? 0 : 1;
            var inv_id = $(this).attr('inv_id');
            var obj = $("span",this);
            var data = {};
            console.log(obj);
            data.data = {
                "inventory_id" : inv_id,
                "access_level" : toggled_level
            }
            
            api('inventory','toggleAccessLevel',data,function(data){
                if(data.result.state){
                    $.notify("Success","success");
                    obj.toggleClass("glyphicon-eye-open");
                    obj.toggleClass("glyphicon-eye-close");
                }else{
                    $.notify(data.result.errorMsg,'error');
                }
            });
            
        }); 
        
        $('.delete_inventory').off().on('click',function(e){
            e.stopPropagation(); 
            var self = $(this).closest('tr');
            var data = {};
            data.data = {
                "inventory_id" : $(this).attr('inv_id')    
            };
            api('inventory','deleteInventory',data,function(data){
                $('#inv_table').DataTable().row(self).remove().draw();
            });
        });

    });
   
});

</script>