<form id='addItem'>
    <div class="form-group">
        <label for="item_name">Item Name:</label>
        <input type="text" class="form-control" id="item_name">
    </div>
    <input type=hidden id=inventory_id value='<?php echo $_GET['id'];?>' />
    <div class="form-group">
        <label for="sellable">Sellable:</label>
        <select class="form-control" id='sellable'><option value=0>False</option><option value=1>True</option></select>
    </div>

    <div class="for_sellable_true form-group">
        <label for="price">Price:</label>
        <input type="number" min=0 step="0.01" class="form-control" id="price" value=0>
    </div>

    <button type="button" item_id='<?php echo $_GET['id'];?>' id='updateItemBtn' class="btn btn-default">Update</button>   
</form>


<script>

$(document).ready(function(){
   
    var item_id = <?php echo $_GET['id'];?>;
    
    var data = {};
    
    data.data = {
        "item_id" : item_id
    }
    
    api("items","getItemDetail",data,function(data){
       $('#item_name').val(data.result.item_name);
       $('#sellable').val(data.result.sellable);
       $('#price').val(data.result.price);
    });
    
    $('#updateItemBtn').off().on('click',function(){
       
        var data = {};
        
        data.data = {
          
            "item_id" : $(this).attr('item_id'),
            "item_name" : $('#item_name').val(),
            "sellable" : $('#sellable').val(),
            "price" : $('#price').val()
            
        };
        
        api("items","updateItemDetail",data,function(data){
            if(data.result.state){
                $.notify("Success","success");
            }else{
                $.notify(data.result.errorMsg,'error');
            }
        });
        
    });
    
    
});
    
    
</script>