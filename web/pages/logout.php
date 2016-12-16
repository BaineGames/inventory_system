<input type=hidden id='logout' lib='user' fn='logout'>
<script>

$(document).ready(function(){
   api('user','logout',null,function(data){
       if(data.result.state){
           window.location.href = './index.php';
       }else{
           $.notify(data.result.errorMsg,"error"); 
       }
   });
});


</script>