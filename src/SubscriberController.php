<?php
/**
 * Description of SubscriberController
 *
 * @author HERVE Tchokote
 */

class SubscriberController
{
    public function __construct(private SubscriberEntry $entry) 
    {  
    }
    
    public function processRequest(string $method, ?string $id): void
    {
        if($id) {
            
             $this->processResourceRequest($method, $id);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }
    
    private function processResourceRequest(string $method, string $id): void
    {
        $subscriber = $this->entry->getSubscriber($id);
        
        if (! $subscriber) {
            http_response_code(404);
            echo json_encode(["response" => "Subscriber not found"]);
            return;
        }
        
        switch ($method) {
            case "GET":
                echo json_encode($subscriber);
                break;
            
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);
                
                if (! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                // Check if other subscriber already exists with this same email
                $subscriberExist['subscriber_id'] = $this->entry->getSubscriberIdByEmail($data['email']);

                if ($subscriberExist['subscriber_id'] != $data['subscriber_id']) {
                    echo json_encode([
                        "error" => "Another subscriber with email {$data['email']} already exists."
                    ]);
                    http_response_code(400);
                    
                } else {
                    // No subscriber with this credential exist, proceed with update
                    $num = $this->entry->updateSubscriber($subscriber, $data);
                    
                    echo json_encode([
                        "Response" => "Subscriber $id updated",
                        "Num" => $num
                    ]);
                }
                break;
                
            case "DELETE":
                $num = $this->entry->deleteSubscriber($id);
                
                echo json_encode([
                    "Response" => "Subscriber $id deleted",
                    "Num" => $num
                ]);
                break;
                
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
        
    }
    
    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->entry->getAllSubscribers());

                break;
            
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);
                
                if (! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                // Check if the subscriber already exists
                $subscriberExist = $this->entry->getSubscriberIdByEmail($data['email']);

                if ($subscriberExist) {
                    echo json_encode([
                        "error" => "Subscriber with email {$data['email']} already exists."
                    ]);
                    http_response_code(400);
                    
                } else {
                    // Subscriber doesn't exist, proceed with insertion
                    $id = $this->entry->insertSubscriber($data);
                    
                    http_response_code(201);
                    echo json_encode([
                        "Response" => "New subscriber inserted",
                        "id" => $id
                    ]);
                }
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
    
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        
        // Sanitize and validate first_name
        if (empty($data['first_name'])) {
            $errors[] = "First name is required";
        } else {
            $sanitizedFirstName = htmlspecialchars($data['first_name'], ENT_QUOTES, 'UTF-8');
            if ($data['first_name'] !== $sanitizedFirstName) {
                $errors[] = "Invalid characters in first name";
            }
        }
        
        // Sanitize and validate last_name
        if (empty($data['last_name'])) {
            $errors[] = "Last name is required";
        } else {
            $sanitizedLastName = htmlspecialchars($data['last_name'], ENT_QUOTES, 'UTF-8');
            if ($data['last_name'] !== $sanitizedLastName) {
                $errors[] = "Invalid characters in last name";
            }
        }
        
        // Validate and sanitize email
        if (array_key_exists("email", $data)) {
            $sanitizedEmail = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
        }
        
        // Validate and sanitize subscriber_status
        if (array_key_exists("subscriber_status", $data)) {
            $sanitizedSubscriberStatus = filter_var($data['subscriber_status'], FILTER_VALIDATE_INT);
            if ($sanitizedSubscriberStatus === false) {
                $errors[] = "Subscriber status must be an integer";
            }
        }
        
        return $errors;
    }

}
