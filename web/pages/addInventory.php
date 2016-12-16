<hr><form id='addInventory'>
    <div class="form-group">
        <label for="inv_name">Inventory Name:</label>
        <input type="text" class="form-control" id="inv_name">
    </div>
    <div class="form-group">
        <label for="inv_type">Type:</label>
        <select class="form-control" id='inv_type'>
            <option value=1>Private</option>
            <option value=0>Public</option> 
        </select>

    </div>
    <button type="button" id='addBtn' class="btn btn-default">Add</button>   
</form>




<script>

    $(document).ready(function(){
        
        
       $('#addBtn').off().on('click',function(){
           var inv_name = $('#inv_name').val();
           var access_level = $('#inv_type').val();
           if(inv_name.length > 0){
               var data = {};
               data.data = {
                   "inventory_name" : inv_name,
                   "access_level" : access_level
               };
               api('inventory','addInventory',data,function(data){
                   if(data.result.state){
                       window.location.href = '?page=inventoryDetail&id='+data.result.id;
                      
                   }else{
                       $.notify(data.result.errorMsg,"error"); 
                   }
               });                
               
           }else{
               $.notify("Inventory Name Cannot Be Empty","error");
           }
          
       }); 
        
        $('form input').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('#addBtn').click();
                $(this).val('');
                return false;
            }
        });
    });

</script>