***Please follow these instructions to run this endpoint:***

1. Import the sql into DB
2. Change the information found in the database file to your infos
3. Use Postman or your favorite API platform to perform the respective tasks.

***This API covers:***

* Basic REST API routing and URLs
* List, show, create, update and delete database
* Controllers and table entries
* Relevant HTTP status codes
* Data validation
* JSON decoding and encoding

***How it works:***

/POST

{
    "first_name": "",
    "last_name": "",
    "email": "",
    "subscriber_status": 1
}

http://localhost/subscriber-php-rest-api/subscriber/

/GET 3
http://localhost/subscriber-php-rest-api/subscriber/2

/GET
http://localhost/subscriber-php-rest-api/subscriber/2

/PATCH 3
http://localhost/subscriber-php-rest-api/subscriber/2

/DELETE 3
http://localhost/subscriber-php-rest-api/subscriber/2