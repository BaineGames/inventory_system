<?php

require('./db.php');

function addItem($params){
    
    global $db;

    $data = array();
    
    $inventory_id = $params['inventory_id'];
    $item_name = $params['item_name'];
    $sellable = $params['sellable'];
    $price = $params['price'];
    
    try {
        
        $stmt = $db->prepare("SELECT item_id FROM items WHERE item_name = :item_name AND inventory_id = :inventory_id");
        $stmt->bindParam(':item_name',$item_name,PDO::PARAM_STR);
        $stmt->bindParam(':inventory_id',$inventory_id,PDO::PARAM_INT);
        $stmt->execute();    
        if($stmt->rowCount() != 0){

            $data['state'] = false;
            $data['errorNo'] = 107;
            $data['errorMsg'] = "Item Already Exists";

            return $data;
        }else{
            
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("INSERT INTO items (inventory_id,item_name,sellable,price) VALUES(:inventory_id,:item_name,:sellable,:price)");
                $stmt->bindParam(':inventory_id',$inventory_id,PDO::PARAM_INT);
                $stmt->bindParam(':item_name',$item_name,PDO::PARAM_STR);
                $stmt->bindParam(':sellable',$sellable,PDO::PARAM_BOOL);
                $stmt->bindParam(':price',$price,PDO::PARAM_STR);
                $stmt->execute();

                try {
                    $stmt = $db->prepare("UPDATE inventory SET item_count = item_count + 1 WHERE inventory_id = :inventory_id");
                    $stmt->bindParam(':inventory_id',$inventory_id,PDO::PARAM_INT);
                    $stmt->execute();   
                    $db->commit();
                    $data['state'] = true;
                    return $data;

                } catch(PDOException $e) {
                    $db->rollBack();
                    $data['state'] = false;
                    $data['errorMsg'] = $e->getMessage();
                    return $data;
                }     
            } catch(PDOException $e) {
                $db->rollBack();
                $data['state'] = false;
                $data['errorMsg'] = $e->getMessage();
                return $data;
            }               
        }
    } catch(PDOException $e) {
        $db->rollBack();
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }    
}

function deleteItem($params){
    
    global $db;

    $data = array();

    $item_id = $params['item_id'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE items SET status = 2 WHERE item_id = :item_id");
        $stmt->bindParam(':item_id',$item_id,PDO::PARAM_INT);
        $stmt->execute();   
        
        try {
            $stmt = $db->prepare("UPDATE inventory SET item_count = item_count - 1 WHERE inventory_id = (SELECT inventory_id FROM items WHERE item_id = :item_id)");
            $stmt->bindParam(':item_id',$item_id,PDO::PARAM_INT);
            $stmt->execute(); 
            $db->commit();
            $data['state'] = true;
            return $data;

        } catch(PDOException $e) {
            $db->rollBack();
            $data['state'] = false;
            $data['errorMsg'] = $e->getMessage();
            return $data;
        }     
    } catch(PDOException $e) {
        $db->rollBack();
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }    
}

function getItemDetail($params){
    
    global $db;

    $data = array();
    
    $item_id = $params['item_id'];
    
    try {
        $stmt = $db->prepare("SELECT item_name, sellable, price, users.user_id, display_name FROM items LEFT JOIN inventory ON items.inventory_id = inventory.inventory_id LEFT JOIN users ON inventory.user_id = users.user_id WHERE item_id = :item_id");
        $stmt->bindParam(':item_id',$item_id,PDO::PARAM_INT);
        $stmt->execute();        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function updateItemDetail($params){
    
    global $db;

    $data = array();
    
    $item_id= $params['item_id'];
    $item_name = $params['item_name'];
    $sellable = $params['sellable'];
    $price = $params['price'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE items LEFT JOIN inventory ON(items.inventory_id = inventory.inventory_id) SET item_name=:item_name, sellable=:sellable, price=:price WHERE item_id=:item_id AND inventory.user_id = :user_id");
        $stmt->bindParam(":item_name",$item_name,PDO::PARAM_STR);
        $stmt->bindParam(":sellable",$sellable,PDO::PARAM_INT);
        $stmt->bindParam(":price",$price,PDO::PARAM_STR);
        $stmt->bindParam(":item_id",$item_id,PDO::PARAM_INT);
        $stmt->bindParam(":user_id",$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() == 0){
            $data['state'] = false;
            $data['errorMsg'] = "This Item Is Not Yours"; 
            return $data;
        }
        $db->commit();
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        //Something went wrong rollback!
        $db->rollBack();
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function getSellableItems(){
    
    global $db;

    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT item_id,item_name,price FROM items LEFT JOIN inventory ON (items.inventory_id = inventory.inventory_id)  WHERE access_level = 1 AND sellable = 1 AND inventory.status = 1 AND items.status = 1 AND inventory.user_id!= :user_id");
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();        
        $data['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }    
}

function getItemList($params){
    
    global $db;
    
    $data = array();
    
    $inventory_id = $params['inventory_id'];
    
    try {
        $stmt = $db->prepare("SELECT inventory_name, access_level FROM inventory WHERE inventory_id = :inventory_id AND user_id= :user_id");
        $stmt->bindParam(':inventory_id',$inventory_id,PDO::PARAM_INT);
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        try {
            $stmt = $db->prepare("SELECT item_id, items.inventory_id, item_name, items.created_on, price FROM items LEFT JOIN inventory ON items.inventory_id = inventory.inventory_id  WHERE inventory.inventory_id = :inventory_id AND inventory.status = 1 AND items.status = 1 AND user_id = :user_id");
            $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindParam(':inventory_id',$inventory_id,PDO::PARAM_INT);
            $stmt->execute();        
            $data['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data['state'] = true;
            return $data;

        } catch(PDOException $e) {
            $data['state'] = false;
            $data['errorMsg'] = $e->getMessage();
            return $data;
        }     
    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }    

}

//addItem('DVDs','Deadpool');

?>