function api(lib,fn,data,callback){
    
      $.ajax({
        type: "POST",
        url: "../api/api.php?lib="+lib+"&fn="+fn,
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
         success: function(data){
                console.log("API CALL GOOD:",data);
                callback(data);
             
        },
         failure: function(data) {
             console.log("API CALL BAD:",data);
                callback(data);
        }
    });   
}