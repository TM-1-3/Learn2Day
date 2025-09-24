<img src='https://sigarra.up.pt/feup/pt/imagens/LogotipoSI' width="30%"/>

<div align="center">
🌍 <a href="README.md">English</a> | 🇵🇹 <a href="README.pt.md">Português</a>
</div>

<h3 align="center">BSc in Informatics and Computing Engineering<br>L.EIC019 - Web Languages and Technologies<br> 2024/2025 </h3>

---
<h3 align="center"> Collaborators &#129309 </h2>

<div align="center">

| Name            | Number      |
|---------------- |-------------|
| Joana Carvalhal | up202306568 |
| Martim Cadilhe  | up202307833 |
| Tomás Morais    | up202303834 |

Grade : 15,1

</div>

# Learn2Day Project Report

* [Features](#features) 
  * [User](#user)
  * [Freelancers](#freelancers)
  * [Clients](#clients)
  * [Admins](#admins)
* [Running](#running)
* [Credentials](#credentials)

## Features

### User
- [x] Register a new account.
- [x] Log in and out.
- [x] Edit their profile, including their name, username, password, and email.

### Freelancers
- [x] List new services, providing details such as category, pricing, delivery time, and service description, along with images or videos.
- [x] Track and manage their offered services.
- [x] Respond to inquiries from clients regarding their services and provide custom offers if needed.
- [ ] Mark services as completed once delivered.

### Clients
- [x] Browse services using filters like category, price, and rating.
- [x] Engage with freelancers to ask questions or request custom orders.
- [x] Hire freelancers and proceed to checkout (simulate payment process).
- [x] Leave ratings and reviews for completed services.

### Admins
- [x] Elevate a user to admin status.
- [ ] Introduce new service categories and other pertinent entities.
- [x] Oversee and ensure the smooth operation of the entire system.


## Running

    sqlite3 docs/learn2day.db < docs/learn2day.sql
    cd src
    php -S localhost:9000

## Credentials

- martim/123456 -> Admin account
- testetutor/123456 -> Tutor account
- testestudent/123456 -> Student account
