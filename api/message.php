<?php

require('./db.php');

function getMessageDetail($params){
    
    global $db;

    $data = array();
    
    $conversation_id = $params['conversation_id'];
    
    try {
        $stmt = $db->prepare("SELECT created_on, message_id, message_text, sender_id, receiver_id, CASE WHEN sender_id = :user_id THEN 'outgoing' ELSE 'incoming' END AS direction, (SELECT display_name FROM users WHERE user_id = messages.sender_id) AS sender_display_name, (SELECT display_name FROM users WHERE user_id = messages.receiver_id) AS receiver_display_name FROM messages WHERE conversation_id = :conversation_id ORDER BY message_id ASC");
        $stmt->bindParam(':conversation_id',$conversation_id,PDO::PARAM_INT);
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();
        $data['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);        

            try {
                $db->beginTransaction();
                $stmt = $db->prepare("UPDATE messages SET message_status = 1 WHERE conversation_id = :conversation_id AND receiver_id = :user_id");
                $stmt->bindParam(':conversation_id',$conversation_id,PDO::PARAM_INT);
                $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
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
        
    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }    
}

function replyMessage($params){
    
    global $db;

    $data = array();
    
    $conversation_id = $params['conversation_id'];
    $sender_id = $params['sender_id'];
    $receiver_id = $params['receiver_id'];
    $message_text = $params['message_text'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO messages (conversation_id,sender_id,receiver_id,message_text) VALUES(:conversation_id,:sender_id,:receiver_id,:message_text)");
        $stmt->bindParam(":conversation_id",$conversation_id,PDO::PARAM_INT);
        $stmt->bindParam(":sender_id",$sender_id,PDO::PARAM_INT);
        $stmt->bindParam(":receiver_id",$receiver_id,PDO::PARAM_INT);
        $stmt->bindParam(":message_text",$message_text,PDO::PARAM_STR);
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

function sendMessage($params){
    
    global $db;

    $data = array();
    
    $item_id = $params['item_id'];
    $sender_id = $params['sender_id'];
    $receiver_id = $params['receiver_id'];
    $message_text = $params['message_text'];
    
    
    try {

        $stmt = $db->prepare("SELECT conversation_id FROM conversations WHERE item_id = :item_id AND ((sender_id=:sender_id AND receiver_id=:receiver_id) OR (sender_id=:receiver_id1 AND receiver_id=:sender_id1))");
        $stmt->bindParam(':item_id',$item_id,PDO::PARAM_INT);
        $stmt->bindParam(':sender_id',$sender_id,PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id',$receiver_id,PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id1',$receiver_id,PDO::PARAM_INT);
        $stmt->bindParam(':sender_id1',$sender_id,PDO::PARAM_INT);
        $stmt->execute();
        $conversation_id = $stmt->fetch(PDO::FETCH_ASSOC)['conversation_id'];
        if(!$conversation_id){            

            try {
                $db->beginTransaction();
                $stmt = $db->prepare("INSERT INTO conversations (item_id,sender_id,receiver_id) VALUES(:item_id,:sender_id,:receiver_id)");
                $stmt->bindParam(':item_id',$item_id,PDO::PARAM_INT);
                $stmt->bindParam(':sender_id',$sender_id,PDO::PARAM_INT);
                $stmt->bindParam(':receiver_id',$receiver_id,PDO::PARAM_INT);
                $stmt->execute();
                $conversation_id = $db->lastInsertId();
                

            } catch(PDOException $e) {
                //Something went wrong rollback!
                $db->rollBack();
                $data['state'] = false;
                $data['errorMsg'] = $e->getMessage();
                return $data;
            } 
        }
        
        try {
            $stmt = $db->prepare("INSERT INTO messages (conversation_id,sender_id,receiver_id,message_text) VALUES(:conversation_id,:sender_id,:receiver_id,:message_text)");
            $stmt->bindParam(':conversation_id',$conversation_id,PDO::PARAM_INT);
            $stmt->bindParam(':sender_id',$sender_id,PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id',$receiver_id,PDO::PARAM_INT);
            $stmt->bindParam(':message_text',$message_text,PDO::PARAM_INT);
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
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
    
    $q = "SELECT conversation_id FROM conversations WHERE item_id = $item_id AND ((sender_id=$sender_id AND receiver_id=$receiver_id) OR (sender_id=$receiver_id AND receiver_id=$sender_id))";
    
    $conversation_id = mysql_fetch_row(mysql_query($q))[0];
    
    if(!$conversation_id){
    
    $q = "INSERT INTO conversations (item_id,sender_id,receiver_id) VALUES($item_id,$sender_id,$receiver_id)";
    
    $res = mysql_query($q);
    
    $conversation_id = mysql_insert_id();
    }
    
    $q = "INSERT INTO messages (conversation_id,sender_id,receiver_id,message_text) VALUES($conversation_id,$sender_id,$receiver_id,'$message_text')";
    
    $res = mysql_query($q);

    if($res){
        return array('state'=>true);
    }else{
        return array('state'=>false,'errorNo'=>mysql_errno(),'errorMsg'=>mysql_error());
    }
}

function getConversations(){
    
    global $db;

    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT conversation_id, item_name, (SELECT display_name FROM users WHERE user_id IN(:user_id1,conversations.sender_id,conversations.receiver_id) AND user_id != :user_id2) AS display_name,last_updated_on, (SELECT MIN(message_status) AS message_status FROM messages WHERE conversation_id = conversations.conversation_id AND receiver_id = :user_id3) AS new_messages FROM conversations LEFT JOIN items ON (conversations.item_id = items.item_id) WHERE (conversations.sender_id = :user_id4 OR conversations.receiver_id = :user_id5) AND deleted != 1");
        $stmt->bindParam(':user_id1',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':user_id2',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':user_id3',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':user_id4',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':user_id5',$_SESSION['user_id'],PDO::PARAM_INT);
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

function deleteConversation($params){
    
    global $db;

    $data = array();
    
    $conversation_id = $params['conversation_id'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE conversations SET deleted = true WHERE conversation_id = :conversation_id");
        $stmt->bindParam(":conversation_id",$conversation_id,PDO::PARAM_STR);
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
};

function getConversationMessageCount($params){

    global $db;

    $data = array();
    
    $conversation_id = $params['conversation_id'];

    try {
        $stmt = $db->prepare("SELECT message_id FROM messages WHERE conversation_id = :conversation_id");
        $stmt->bindParam(':conversation_id',$conversation_id,PDO::PARAM_INT);
        $stmt->execute();  
        $data['message_count'] = $stmt->rowCount();
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function getMessageCount(){
    
    global $db;

    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT message_id FROM messages INNER JOIN conversations ON messages.conversation_id = conversations.conversation_id WHERE messages.receiver_id = :user_id AND message_status = 0 AND deleted = 0");
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();  
        $data['message_count'] = $stmt->rowCount();
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

?>