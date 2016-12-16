<?php

require('./db.php');

function addInventory($params){
    
    global $db;
    
    $data = array();
    
    $inventory_name = $params['inventory_name'];
    $access_level = $params['access_level'];
    
    try {

        $stmt = $db->prepare("SELECT inventory_id FROM inventory WHERE status = 1 AND inventory_name = :inventory_name AND user_id = :user_id");
        $stmt->bindParam(':inventory_name',$inventory_name,PDO::PARAM_STR);
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount() == 0){            
            
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("INSERT INTO inventory (user_id,inventory_name,access_level) VALUES(:user_id,:inventory_name,:access_level)");
                $stmt->bindParam(":user_id",$_SESSION['user_id'],PDO::PARAM_INT);
                $stmt->bindParam(":inventory_name",$inventory_name,PDO::PARAM_STR);
                $stmt->bindParam(":access_level",$access_level,PDO::PARAM_INT);
                $stmt->execute();
                $data['id'] = $db->lastInsertId();
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
        }else{
            $data['state'] = false;
            $data['errorNo'] = 103;
            $data['errorMsg'] = "Inventory Exists";
            return $data;
        }
    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function deleteInventory($params){
    
    global $db;
    
    $data = array();
    
    $inventory_id = $params['inventory_id'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE inventory SET status = 2 WHERE inventory_id = :inventory_id AND user_id = :user_id AND status != 2");
        $stmt->bindParam(":inventory_id",$inventory_id,PDO::PARAM_INT);
        $stmt->bindParam(":user_id",$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();
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

function updateInventory($params){
    
    global $db;

    $data = array();

    $inventory_id = $params['inventory_id'];
    $inventory_name = $params['inventory_name'];
    $inventory_type = $params['inventory_type'];

    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE inventory SET inventory_name = :inventory_name, access_level = :inventory_type, updated_on = CURRENT_TIMESTAMP WHERE inventory_id = :inventory_id");
        $stmt->bindParam(":inventory_name",$inventory_name,PDO::PARAM_STR);
        $stmt->bindParam(":inventory_type",$inventory_type,PDO::PARAM_INT);
        $stmt->bindParam(":inventory_id",$inventory_id,PDO::PARAM_INT);
        $stmt->execute();
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

function toggleAccessLevel($params){
    
    global $db;

    $data = array();
    
    $inventory_id = $params['inventory_id'];
    $access_level = $params['access_level'];

    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE inventory SET access_level = :access_level, updated_on = CURRENT_TIMESTAMP WHERE inventory_id = :inventory_id");
        $stmt->bindParam(":access_level",$access_level,PDO::PARAM_INT);
        $stmt->bindParam(":inventory_id",$inventory_id,PDO::PARAM_INT);
        $stmt->execute();
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

function getInventoryList(){
    
    global $db;

    $data = array();

    try {
        $stmt = $db->prepare("SELECT inventory.inventory_id, inventory_name, inventory.created_on, item_count, access_level,TRUNCATE(IFNULL(total,0),2) AS total FROM inventory LEFT JOIN users ON (inventory.user_id = users.user_id) LEFT JOIN (SELECT inventory_id, SUM(price) AS total FROM items WHERE items.status = 1 GROUP BY items.inventory_id) inv_values ON(inventory.inventory_id = inv_values.inventory_id) WHERE users.user_id= :user_id AND inventory.status = 1");
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

?>