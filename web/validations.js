function validateEmail(address)   
{  

    var ret;
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(address))  
    {  
        ret = true; 
    }else{
        ret = false;
        $.notify("Email Not Valid","error");
    }  
    
    return ret;
    
}  