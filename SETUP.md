# Setup Guide for Gourmet Express

Follow these steps exactly to get the development environment functional.

## 1. Prerequisites
Ensure the following are installed:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Git](https://git-scm.com/)

## 2. Environment Configuration
The project relies on environment variables for security and portability.

1.  **Clone the Repository:**
    ```bash
    git clone git@github.com:AngelaRuiu/Gourmet-Express.git
    cd gourmetExpress
    ```
2.  **Create the `.env` file:**
    Copy the `.env.example`  to a new file named `.env`:
    ```bash
    cp .env.example .env
    ```
3.  **Update Credentials:** Open `.env` and set all the missing credentials and ENVs

## 3. Launching the Containers
Use the management script to build and start the infrastructure.

1.  **Grant Execution Permissions:**
    ```bash
    chmod +x manage.sh
    ```
2.  **Build and Start:**
    ```bash
    ./manage.sh build
    ```
    *This will pull the images for MySQL, Nginx, and phpMyAdmin, and build the custom PHP container.*

## 4. Installing Dependencies
Once the containers are running, install the PHP libraries defined in `composer.json`
```bash
./manage.sh composer
```
     *This will pull the images for MySQL, Nginx, and phpMyAdmin, and build the custom PHP container.*

## 5. Verifying the Installation
Once the setup is complete, you can access the following services:
- **Website:** http://localhost:8080 
- **Database:** http://localhost:8081 
    - Host:db
    - User/Password: As defined in .env
- **Mailtrap Inbox:**  Login to Mailtrap Dashboard to see outgoing emails.
