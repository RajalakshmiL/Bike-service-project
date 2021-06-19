# DB-Schema :
1). customer
```
CREATE TABLE "customer" (
  "customer_id" INT NOT NULL AUTO_INCREMENT COMMENT 'Customer Id',
  "customer_name" VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Customer Name',
  "customer_mail" VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Customer Mail Id',
  "customer_mobile" VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Customer Phone number',
  "customer_password" VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Customer Password',
  "active_status" TINYINT DEFAULT '1' COMMENT 'Customer is active or inactive',
  PRIMARY KEY ("customer_id")
)
```
2). booking
```
CREATE TABLE "booking" (
  "booking_id" INT NOT NULL AUTO_INCREMENT COMMENT 'Booking Id',
  "customer_id" INT DEFAULT NULL COMMENT 'Customer Id',
  "service_id" TINYINT DEFAULT NULL COMMENT 'Service Id',
  "service_date" DATE DEFAULT NULL COMMENT 'Service date',
  "delivery_date" DATE DEFAULT NULL COMMENT 'Delivery date',
  "status_id" TINYINT DEFAULT NULL COMMENT 'Service Status id',
  PRIMARY KEY ("booking_id")
)
```
3). service
```
CREATE TABLE "service" (
  "service_id" tinyint NOT NULL AUTO_INCREMENT COMMENT 'Service Id',
  "service_name" varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Service Name',
  "is_used" int DEFAULT '0' COMMENT 'Used count',
  "active_status" tinyint DEFAULT '1' COMMENT 'Service is active or not',
  PRIMARY KEY ("service_id")
)

insert  into `service`(`service_id`,`service_name`,`is_used`,`active_status`) values 
(1,'General service check-up',0,1),
(2,'Oil change',0,1),
(3,'Water wash',0,1);
```
4). service_status
```
CREATE TABLE "service_status" (
  "status_id" tinyint NOT NULL AUTO_INCREMENT COMMENT 'Status Id',
  "status_name" varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Status Name',
  "active_status" tinyint DEFAULT '1' COMMENT 'Status is active or not',
  PRIMARY KEY ("status_id")
)

insert  into `service_status`(`status_id`,`status_name`,`active_status`) values 
(1,'Pending',1),
(2,'Ready for delivery',1),
(3,'Completed',1);
```
5). session_data
```
CREATE TABLE "session_data" (
  "session_id" varchar(35) NOT NULL,
  "data" text NOT NULL,
  "created_at" int unsigned NOT NULL,
  "modified_at" int unsigned DEFAULT NULL,
  PRIMARY KEY ("session_id")
)
```

# .env file
```
"DBNAME_ADMDB" = "XXXX"
"DBUSER_ADMDB" = "XXXX"
"DBPWD_ADMDB" = "XXXX"
"DBHOST_ADMDB" = "XXXX"
"DBPORT_ADMDB" = "XXXX"
"MAIL_HOST" = "smtp.gmail.com"
"MAIL_PORT" = "465"
"MAIL_ENC" = "SSL"
"MAIL_DRIVER" = "smtp"
"MAIL_USER" = "XXXX@XXXX.com"
"MAIL_PWD" = "XXXX"
"JOHN_MAIL" = "csedot.97@gmail.com"
```

# End Points :
**API End Points and Requests:**

**{{url}} - project.raj.vettrisakthi.com/service**

1). https://{{url}}/customer/register - POST
```
Request: 

{
    "customer_name": "Magesh",
    "customer_mail": "magesh@gmail.com",
    "customer_password": "secret",
    "customer_mobile": "9842744444"
}
```

2). https://{{url}}/customer/login - POST
```
{
    "customer_mail": "magesh@gmail.com",
    "customer_password": "secret"
}
```

3). https://{{url}}/customer/booking - POST
```
{
    "customer_id": "1",
    "service_id": "1",
    "service_date": "17-06-2021"
}
```

4). https://{{url}}/customer/bookingStatus - POST
```
{
    "customer_id": "1",
    "service_date": "17-06-2021"
}
```

5). https://{{url}}/customer/{customer_id} - GET


6). https://{{url}}/service/ - POST
```
{
    "service_name": "Brakes Check"
}
```

7). https://{{url}}/service/{service_id} - PUT
```
{
    "service_name": "Brakes Check"
}
```

8). https://{{url}}/service/{service_id} - DELETE

9). https://{{url}}/service/list - GET

10). https://{{url}}/service/{booking_id}  - GET

11). https://{{url}}/service/delivery - POST
```
{
    "booking_id": "1"
}
```

12). https://{{url}}/service/complete
```
{
    "booking_id": "1"
}
```
