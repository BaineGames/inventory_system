<hr><form id='addItem'>
    <div class="form-group">
        <label for="item_name">Item Name:</label>
        <input type="text" class="form-control" id="item_name">
    </div>
    <input type=hidden id=inventory_id value='<?php echo $_GET['id'];?>' />
    <div class="form-group">
        <label for="sellable">Sellable:</label>
        <select class="form-control" id='sellable'><option value=0>False</option><option value=1>True</option></select>
    </div>
    
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" min=0 step="0.01" class="form-control" id="price" value=0>
    </div>
    
    <button type="button" id='addItemBtn' class="btn btn-default">Add</button>   
</form>


<script>

$(document).ready(function(){
    
    $('.for_sellable_true').hide();
    
    $('form input').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            $('#addItemBtn').click();
            $(this).val('');
            return false;
        }
    });
   
    $('#addItemBtn').off().on('click',function(){
        
        var data = {};
        data.data = {
            "inventory_id" :   $('#inventory_id').val(),
            "item_name"    :   $('#item_name').val(),
            "sellable"     :   $('#sellable').val(),
            "price"        :   $('#price').val()
        };
        
        api('items','addItem',data,function(data){

            if(data.result.state){
                $.notify("Success","success");
            }else{
                $.notify(data.result.errorMsg,'error');
            }

        });
    });
    
    
    
});

</script>