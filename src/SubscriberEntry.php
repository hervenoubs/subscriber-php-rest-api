<?php
/**
 * Description of SubscriberEntry
 *
 * @author HERVE Tchokote
 */

class SubscriberEntry
{
    public $link;

    function __construct() 
    {
        $db_connection = new Database();
        $this->link = $db_connection->connect();
        return $this->link;
    }
    
    public function getAllSubscribers(): array
    {
        $sql = "SELECT * FROM ml_subscribers";
        
        $stmt = $this->link->query($sql);
        
        $data = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            
            $row['subscriber_status'] = (bool) $row['subscriber_status'];
            
            $data[] = $row;
             
        }
        
        return $data;
    }
    
    public function getSubscriber(string $id): array | false
    {
        $sql = "SELECT * FROM ml_subscribers
                WHERE subscriber_id = :subscriber_id";
        try {
            $stmt = $this->link->prepare($sql);

            $stmt->bindValue(":subscriber_id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result !== false) {
                $result['subscriber_status'] = (bool) $result['subscriber_status'];
            }
            
            return $result;
            
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    
    public function insertSubscriber(array $data): string
    {
        $sql = "
            INSERT INTO ml_subscribers
                (first_name, last_name, email, subscriber_status)
            VALUES
                (:first_name, :last_name, :email, :subscriber_status)
        ";
        try {
            $stmt = $this->link->prepare($sql);
            
            $stmt->bindValue(":first_name", $data['first_name'], PDO::PARAM_STR);
            $stmt->bindValue(":last_name", $data['last_name'], PDO::PARAM_STR);
            $stmt->bindValue(":email", $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(":subscriber_status", (bool) $data['subscriber_status'] ?? false, PDO::PARAM_BOOL);
            
            $stmt->execute();
            
            return $this->link->lastInsertId();
            
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    
    public function updateSubscriber(array $current, array $new): int
    {
        $sql = "UPDATE ml_subscribers "
                . "SET first_name = :first_name, last_name = :last_name, email = :email, subscriber_status = :subscriber_status "
                . "WHERE subscriber_id = :subscriber_id";
        
        $stmt = $this->link->prepare($sql);
        
        $stmt->bindValue(":first_name", $new["first_name"] ?? $current["first_name"], PDO::PARAM_STR);
        $stmt->bindValue(":last_name", $new["last_name"] ?? $current["last_name"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
        $stmt->bindValue(":subscriber_status", $new["subscriber_status"] ?? $current["subscriber_status"], PDO::PARAM_BOOL);
        
        $stmt->bindValue(":subscriber_id", $current["subscriber_id"], PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function deleteSubscriber(string $id): int
    {
        $sql = "DELETE FROM ml_subscribers "
                . "WHERE subscriber_id = :subscriber_id";
        
        $stmt = $this->link->prepare($sql);
        
        $stmt->bindValue(":subscriber_id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    // Function to get subscriber ID by email
    public function getSubscriberIdByEmail(string $email): string
    {
        $sql = "SELECT subscriber_id FROM ml_subscribers WHERE email = :email LIMIT 1";
        try {
            $stmt = $this->link->prepare($sql);
            
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['subscriber_id'] : 0;
            
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    
}