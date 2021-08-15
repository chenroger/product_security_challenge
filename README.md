# The Zendesk Product Security Challenge

Hello friend,

We are super excited that you want to be part of the Product Security team at Zendesk. **To get started, you need to fork this repository to your own GitHub profile and work off that copy.** 

### The Challenge

Implement an easy, secure authentication mechanism that allows users to:
- Create an account
- Log in and log out
- Reset their password

We have created the basic boilerplate for you (which you are free to modify) and it is up to you to implement the server-side functionality and expand from there. There is no restriction on language, frameworks or implementation, however we ask that: 
- You consider the security challenges that come with any authentication mechanism and implement controls to protect against common attacks.
- You document the controls you have implemented and come to your interview prepared to justify these decisions.
- Your submission is linked to your GitHub account (i.e. the forked repository you created) and contains all the source code and clear documentation on how to run the application (a dockerized, packaged, or compiled submission is welcome, but not essential). 
- You have fun and hopefully learn something! Whilst we know this isn’t strictly a developer role, the ability to understand what goes into secure design and implementation is an important part of our job. The time it takes to complete this task will vary based on your previous experience, but we don’t want you up until 2am every night trying to get this done. We are looking for a submission that demonstrates your abilities and knowledge.
 
If you are having trouble, for whatever reason, please reach out to us! 

## Setup
This application is built on PHP with SQLite.
Install PHP (example for Debian-based distros):
```
sudo apt install php7.3 php7.3-sqlite
```

Download this repo and run the following command from the project directory:
```
php -S 0.0.0.0:8080

```

The web application can now be accessed at http://127.0.0.1:8080

## Implemented Security Controls
* Sending the login request as a POST request
* Prepared statements for SQL query execution
* Only a single session is allowed for each user
* Session duration of 30 minutes
* Only alphanumeric usernames allowed
* Password hashing with salt (bcrypt with a work factor of 10)
* Secret question answer hashing (bcrypt with a work factor of 10)
* Password complexity
    * At least 8 characters
    * At least 1 capital, 1 lower case, and 1 number
* 128 bits session cookies
    * 30 minute validity
    * Include a check to prevent assigning a session already in use by another user
* Minimum 8 characters needed for secret question answers
* Vague failed login message
* Failed login and password reset locks out at 5 attempts for 30 minutes (two separate lockouts)
* CSRF token for login and for logout
* Autocomplete is configured to off to prevent the browser storing secret question answers
* HTTPOnly cookie directive is set
* Cache control headers set on authenticated page

### Limitations
* A proper web server is not used, the inclusion of a web server such as Apache will allow for the following:
    * TLS for the encryption of data in transit
    * Preventing access to files like .htaccess and users.db

### Improvements that could be made
* SQLCipher to encrypt the database at rest (connection string would still need to be added to the PHP code)
* Multiple secret questions to be used to authenticate users for a password reset
* Prevent common passwords from being used (e.g. Password1, Welcome123)