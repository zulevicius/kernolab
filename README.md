 # Task
 
 Company X which has an EMI license, has a social system where users can top up their balance and spend money for boosting their social experience. Company X decides to allow users do funds withdrawal, so they now need system which will allow users make transactions(payments) to their bank accounts. They hire you, to develop separate API for those transactions(payments). Also, company X requires you to use at least two transaction providers(processors) for different processing depending on currencies.

 ## Description
 This is a stateless API based on PHP 7 and MySQL which simulates money transfer operations using JSON data for requests and responses. On successful request the API returns HTTP response with code 200, on failure it returns code 500, and 404 - if requested endpoint is not found.
 It accepts five requests to manage transactions, here is the list of them with CLI cURL examples (presume the API is running on http://localhost:8080/kernolabapi/):
 - `POST index.php?endpoint=inittransaction` initiates transaction:
```
curl -X POST -H "Content-Type: application/json" \
 -d "{\"user_id\":1, \
  \"details\":\"details details details\", \
  \"receiver_account\":\"LT001111222233334444\", \
  \"receiver_name\":\"Name Surname\", \
  \"amount\":20.00, \
  \"currency\":\"eur\"}" \
 http://localhost:8080/kernolabapi/?endpoint=inittransaction
```
 - `PUT index.php?endpoint=submittransaction` launches submit background process for the oldest unconfirmed transaction:
```
curl -X PUT -H "Content-Type: application/json" \
 -d '{"code":"111"}' \
 http://localhost:8080/kernolabapi/?endpoint=submittransaction
```
 - `PUT index.php?endpoint=submitalltransactions` submits all unconfirmed transactions:
```
curl -X PUT -H "Content-Type: application/json" \
 http://localhost:8080/kernolabapi/?endpoint=submitalltransactions
```
 - `GET index.php?endpoint=gettransaction` gets transaction by ID:
```
curl -X GET -H "Content-Type: application/json" \
 -d '{"transaction_id":1}' \
 http://localhost:8080/kernolabapi/?endpoint=gettransaction
```
 - `GET index.php?endpoint=getusertransactions` pulls all user transactions in descending order of date created:
```
curl -X GET -H "Content-Type: application/json" \
 -d '{"user_id":1}' \
 http://localhost:8080/kernolabapi/?endpoint=getusertransactions
```

 ## Technical requirements

 -	PHP 7+
 -	MySQL
 
 Note: if you have several PHP versions on your machine be sure that 7+ version is put into your `$PATH`. It is needed for background process and unit tests execution.
 
 ## Database preparation
 
 Import `setup/kernotransactions.sql` script to your MySQL server.
 Connection parameters can be edited in `models/DBClass.php`:
 ```
private $host = 'localhost';
private $username = 'root';
private $password = '';
private $database = 'kernotransactions';
```

 ## Unit tests execution
 
 Use CLI to execute `phpunit.phar` file from a root directory:
 ```
 php ./phpunit.phar --bootstrap ./autoload.php ./tests/
 ```
 
