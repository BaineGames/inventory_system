<?php

require('./db.php');

function deleteAccount(){
    
    global $db;
    
    $data = array();
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE users SET status=3 WHERE status!=3 AND email = :email AND session_id= :session_id ");
        $stmt->bindParam(':email',$_SESSION['email'],PDO::PARAM_STR);
        $stmt->bindParam(':session_id',$_SESSION['session_id'],PDO::PARAM_STR);
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

function getAccount(){
    
    global $db;
    
    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT email,display_name FROM users WHERE user_id = :user_id ");
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
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

function updateAccount($params){
    
    global $db;

    $data = array();
    
    $email = $params['email'];
    $display_name = $params['display_name'];
    
    try {
        $stmt = $db->prepare("SELECT email FROM users WHERE (email= :email OR display_name = :display_name) AND user_id != :user_id");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()){

            $data['state'] = false;
            $data['errorNo'] = 100;
            $data['errorMsg'] = "Email or Display Name Already In Use";

            return $data;
        }else{

            try {
                $db->beginTransaction();
                $stmt = $db->prepare("UPDATE users SET updated_on=CURRENT_TIMESTAMP, email = :email, display_name= :display_name WHERE user_id = :user_id");
                $stmt->bindParam(':email',$email,PDO::PARAM_STR);
                $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
                $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
                $stmt->execute();
                $db->commit();
                $_SESSION['email'] = $email;
                $_SESSION['display_name'] = $display_name;
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
        $data['state'] = true;
        return $data;
    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function signup($params){
    
    global $db;
    
    $data = array();
    
    $email = $params['email'];
    $pass = password_hash($params['password'],PASSWORD_DEFAULT);
    $display_name = $params['display_name'];
    
    try {
        $stmt = $db->prepare("SELECT email FROM users WHERE email= :email OR display_name = :display_name");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()){
            
            $data['state'] = false;
            $data['errorNo'] = 100;
            $data['errorMsg'] = "Email or Display Name Already In Use";
        
            return $data;
        }else{
            
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("INSERT INTO users (email,password,display_name,status) VALUES(:email,:pass,:display_name,1)");
                $stmt->bindParam(':email',$email,PDO::PARAM_STR);
                $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
                $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
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
        $data['state'] = true;
        return $data;
    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function login($params){
    
    global $db;
    
    $data = array();
    
    $email = $params['email'];
    $pass = $params['password'];
    
    
    try {
        $stmt = $db->prepare("SELECT password FROM users WHERE email = :email ");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();        
        $password_hash = $stmt->fetch(PDO::FETCH_ASSOC)['password'];
        
        if(password_verify($pass,$password_hash)){
            //yes this is password - continue
            try {

                $stmt = $db->prepare("SELECT user_id,email,display_name FROM users WHERE email = :email AND password = :pass ");
                $stmt->bindParam(':email',$email,PDO::PARAM_STR);
                $stmt->bindParam(':pass',$password_hash,PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->rowCount()){            
                    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    session_regenerate_id();
                    $_SESSION['session_id'] = session_id();
                    $_SESSION['logged_in'] = true;
                    $_SESSION['email'] = $user_data['email'];
                    $_SESSION['user_id'] = $user_data['user_id'];
                    $_SESSION['display_name'] = $user_data['display_name'];
                    try {
                        $db->beginTransaction();
                        $stmt = $db->prepare("UPDATE users SET last_login_time=CURRENT_TIMESTAMP, session_id= :session_id WHERE user_id = :user_id");
                        $stmt->bindParam(":session_id",$_SESSION['session_id'],PDO::PARAM_STR);
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
                }else{
                    $data['state'] = false;
                    $data['errorNo'] = 101;
                    $data['errorMsg'] = "Bad Login";
                    return $data;
                }
            } catch(PDOException $e) {
                $data['state'] = false;
                $data['errorMsg'] = $e->getMessage();
                return $data;
            }
        }else{
            //bad login
            $data['state'] = false;
            $data['errorNo'] = 101;
            $data['errorMsg'] = "Bad Login";
            return $data;
        }
    

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
    
    
    
    
}

function logout(){
    
    global $db;
    
    $data = array();
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE users SET session_id = null WHERE session_id = :session_id");
        $stmt->bindParam(":session_id",$_SESSION['session_id'],PDO::PARAM_STR);
        $stmt->execute();
        $db->commit();
        session_unset();
        session_destroy();
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

function removeFriend($params){
    
    global $db;

    $data = array();
    
    $display_name = $params['display_name'];
    
    try {

        $stmt = $db->prepare("SELECT user_id FROM users WHERE display_name = :display_name");
        $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
        $stmt->execute();
        $friend_user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
        $my_user_id = $_SESSION['user_id'];

        $user_a = ($my_user_id < $friend_user_id) ? $my_user_id : $friend_user_id;
        $user_b = ($user_a == $my_user_id) ? $friend_user_id : $my_user_id;    
           
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("DELETE FROM friends WHERE sender_id = :user_a AND receiver_id = :user_b");
                $stmt->bindParam(":user_a",$user_a,PDO::PARAM_STR);
                $stmt->bindParam(":user_b",$user_b,PDO::PARAM_INT);
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

function confirmFriendRequest($params){
    
    global $db;

    $data = array();
    
    $request_id = $params['request_id'];

    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE friends SET request_status = 2 WHERE request_id = :request_id");
        $stmt->bindParam(":request_id",$request_id,PDO::PARAM_STR);
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

function deleteFriendRequest($params){
    
    global $db;

    $data = array();
    
    $request_id = $params['request_id'];
    
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("DELETE FROM friends WHERE request_id = :request_id");
        $stmt->bindParam(":request_id",$request_id,PDO::PARAM_STR);
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

function getFriendRequestsCount(){
    
    global $db;

    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT request_id, sender_id, display_name FROM friends LEFT JOIN users ON friends.sender_id = users.user_id WHERE request_status = 1 AND receiver_id = :user_id ");
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute();  
        $data['friend_count'] = $stmt->rowCount();
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function getFriendRequests(){
    
    global $db;

    $data = array();
    
    try {
        $stmt = $db->prepare("SELECT request_id, sender_id, display_name FROM friends LEFT JOIN users ON friends.sender_id = users.user_id WHERE request_status = 1 AND receiver_id = :user_id ");
        $stmt->bindParam(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->execute(); 
        if($stmt->rowCount() > 0){
            $data['data'] = $stmt->FetchAll(PDO::FETCH_ASSOC);
        }        
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }
}

function addFriend($params){
    
    global $db;

    $data = array();
 
    $display_name = $params['display_name'];

    try {

        $stmt = $db->prepare("SELECT user_id FROM users WHERE display_name = :display_name");
        $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
        $stmt->execute();
        $friend_user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id']; 

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO friends (sender_id,receiver_id,request_status) VALUES(:user_id,:friend_user_id,1)");
            $stmt->bindParam(":user_id",$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindParam(":friend_user_id",$friend_user_id,PDO::PARAM_INT);
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

function searchFriends($params){
    
    global $db;

    $data = array();
    
    $display_name = "%".$params['display_name']."%";
    
    try {
        $stmt = $db->prepare("SELECT display_name, (SELECT request_status FROM friends WHERE (sender_id= :user_id1 AND receiver_id = users.user_id) OR (receiver_id= :user_id2 AND sender_id = users.user_id)) AS request_status FROM users WHERE display_name LIKE  :display_name AND user_id != :user_id3 ");
        $stmt->bindParam(':user_id1',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':user_id2',$_SESSION['user_id'],PDO::PARAM_INT);
        $stmt->bindParam(':display_name',$display_name,PDO::PARAM_STR);
        $stmt->bindParam(':user_id3',$_SESSION['user_id'],PDO::PARAM_INT);
        
        $stmt->execute();  
        $data['data'] = $stmt->FetchAll(PDO::FETCH_ASSOC);
        $data['state'] = true;
        return $data;

    } catch(PDOException $e) {
        $data['state'] = false;
        $data['errorMsg'] = $e->getMessage();
        return $data;
    }        
    
}

?>