## Project: Simple Order Management [Boilerplate Code]
## Technology: Php Codeigniter

### Table of Content
* Features
* Database
* API End Point
* Sub Task
* Credit

### Features

> Build with Php Codeigniter framework

> JWT Token

> Password BCrypt

> Bulk Insertion


### Database
```javascript
CREATE TABLE IF NOT EXISTS `user` (
    `id` int NOT NULL AUTO_INCREMENT,
    `mobile` varchar(15) NOT NULL,
    `password_hash` text,
    `product_salt` text,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
```

```javascript
CREATE TABLE IF NOT EXISTS `products` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `price` decimal(18,2) NOT NULL,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
```

```javascript
CREATE TABLE IF NOT EXISTS `order_master` (
    `id` int NOT NULL AUTO_INCREMENT,
    `mobile` int NOT NULL,
    `addr` text NOT NULL,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
```

```javascript
CREATE TABLE IF NOT EXISTS `order_list` (
    `id` int NOT NULL AUTO_INCREMENT,
    `order_id` int NOT NULL,
    `product_id` int NOT NULL,
    `price` decimal(18,2) NOT NULL,
    `qty` int NOT NULL,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
```
### API End Point
#### ADMIN API 01:
GET: http://localhost/product/index.php/api/product

:Header:
```javascript
{
    "Content-Type": "application/json"
}
```

:Request:


:Response:
```javascript
{
    "status": 1,
    "message": "Products found",
    "data": [
        {
            "id": "1",
            "name": "Product1 500 ml",
            "price": "150.00",
            "modified": "2020-06-13 00:02:33"
        }
    ]
}
```

#### ADMIN API 02:
POST: http://localhost/product/index.php/api/product

:Header:
```javascript
{
    "Content-Type": "application/json"
}
```

:Request:
```javascript
{
    "name": "Product1 500 ml", "price": "200"
}
```

:Response:
```javascript
{
    "status": "1",
    "message": "Product created successful"
}
```

:Error Response:
```javascript
{
    "status": "0",
    "message": "Product number already exists"
}
```

#### ADMIN API 03:
GET: http://localhost/product/index.php/api/product/orderlistall

:Header:
```javascript
{
    "Content-Type": "application/json"
}
```

:Request:


:Response:
```javascript
{
    "data" :[
        {
            "mobno": "9791070918",
            "addr": "12, Kumaran Street, Salem - 636001",
            "list": [
                {"id": "1", "name": "Product1 500 ml", "price": "200", "qty": "1" },
                {"id": "3", "name": "Product3 250 gm", "price": "160", "qty": "2" },
                {"id": "4", "name": "Product4 250 gm", "price": "120", "qty": "1" }
            ]
        },
        {
            "mobno": "9791070919",
            "addr": "46, Laxmi Street, Salem - 636001",
            "list": [
                {"id": "2", "name": "Product2 1 kg", "price": "150", "qty": "1" },
                {"id": "3", "name": "Product3 250 gm", "price": "160", "qty": "1" },
                {"id": "5", "name": "Product5 250 gm", "price": "180", "qty": "1" }
            ]
        }
    ]
}
```

#### USER API 01:
GET: http://localhost/product/index.php/api/user/register

:Header:
```javascript
{
    "Content-Type": "application/json"
}
```

:Request:
```javascript
{ "mobno": "9791070918", "password": "123456" }
```

:Response:
```javascript
{
    "status": "1",
    "message": "Registered successful"
}
```

:Error Response:
```javascript
{
    "status": "0",
    "message": "Mobile number already exists"
}
```

#### USER API 02:
GET: http://localhost/product/index.php/api/user/login

:Header:
```javascript
{
    "Content-Type": "application/json"
}
```

:Request:
```javascript
{ "mobno": "9791070918", "password": "123456" }
```

:Response:
```javascript
{
    "status": "1",
    "token": "jwt token..."
    "message": "Login successful"
}
```

:Token:
```javascript
{
  "mobile": "9791070319",
  "iat": 1593658604,
  "exp": 1593676604
}
```

#### USER API 03:
GET: http://localhost/product/index.php/api/user/placeorder

:Header:
```javascript
{
    "Content-Type": "application/json"
    "Authorization": "jwt token..."
}
```

:Request:
```javascript
{
    "addr": "12, Kumaran Street, Salem - 636001",
    "list": [
        { "id": "1", "price": "200", "qty": "1"},
        { "id": "3", "price": "160", "qty": "2"},
        { "id": "4", "price": "120", "qty": "1"}
    ]
}
```

:Response:
```javascript
{
    "status": "1",
    "message": "Order placed successful"
}
```

### Sub Task
- [x] Database Table
- [x] API End Point
- [x] Bulk Insert
- [x] JWT Authentication
- [x] Password BCrypt
- [ ] Server-side Validation
- [ ] Payment integration

### Credit
##### Found in Youtube 
**Channel Name**: [Online Web Tutor](https://www.youtube.com/channel/UCB2flCo-gW6RhpVVXySqcMg) . [Playlist](https://www.youtube.com/playlist?list=PLT9miexWCpPWKV83pCHGAIIg2lSBHQa9q)
