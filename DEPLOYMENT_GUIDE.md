# Deployment Guide: CloudPanel (pi.dupno.com)

This guide explains how to host the Invoice Generator on your VPS using **CloudPanel**, specifically for the subdomain `pi.dupno.com`. This method isolates the application so it will **not** affect your existing WordPress site (`dupno.com`).

## 1. Configure DNS (Important)
Before creating the site, ensure your domain is pointing to the server.
1.  Go to your Domain Registrar (where you bought `dupno.com`).
2.  Add an **A Record**:
    - **Host/Name**: `pi`
    - **Value/Target**: Your VPS IP Address.

## 2. Create the Database
Since you are using MySQL, set it up first in CloudPanel.

1.  Log in to your **CloudPanel**.
2.  Go to **Databases** > **Add Database**.
3.  **Database Name**: `invoice_app` (or your preferred name).
4.  **Database User**: Create a new user (e.g., `invoice_user`).
5.  **Password**: Generate a strong password. **Copy this password**, you will need it later.
6.  Click **Create**.

## 3. Create the Site
1.  Go to **Sites** > **Add Site**.
2.  **Select "Create a PHP Site"** (Top right option in the "What kind of site..." screen).
3.  **Domain Name**: `pi.dupno.com`.
4.  **PHP Version**: Select **PHP 8.1** or **PHP 8.2**.
5.  **Vhost Template**: `Generic` (The default is usually fine).
6.  **Site User**: Create a new user (e.g., `pi_user`) to keep it isolated from your main site.
7.  Click **Create**.

## 3. Upload Files
You need to upload the project files to the directory created for the site. The path is usually:
`/home/<site-user>/htdocs/pi.dupno.com/`

### Method A: SFTP / Filezilla
1.  Connect to your server using SFTP (User: `<site-user>`).
2.  Navigate to `/htdocs/pi.dupno.com/`.
3.  Delete the default `index.php` created by CloudPanel.
4.  Upload all project files (`index.php`, `config.php`, `assets/`, `api/`, `views/`, `vendor/`, `generate_pdf.php`).

### Method B: Git (Recommended)
If your code is on GitHub/GitLab:
1.  SSH into your server.
2.  Navigate to the folder: `cd /home/<site-user>/htdocs/pi.dupno.com/`
3.  Delete existing files: `rm -rf *`
4.  Clone your repo: `git clone .`
5.  Run `composer install`.

## 4. Configure Application
1.  On the server, open `config.php`:
    ```bash
    nano /home/<site-user>/htdocs/pi.dupno.com/config.php
    ```
2.  Update the database credentials with the ones you created in Step 1:
    ```php
    define('DB_HOST', 'localhost'); // CloudPanel uses 127.0.0.1 or localhost
    define('DB_NAME', 'invoice_app');
    define('DB_USER', 'invoice_user');
    define('DB_PASS', 'your_password_here');
    ```
3.  Save and exit (`Ctrl+X`, `Y`, `Enter`).

## 5. Nginx Configuration
CloudPanel's default Generic PHP template needs a small adjustment to handle our custom routing (clean URLs like `/login` instead of `/login.php`).

1.  In CloudPanel, go to **Sites** > **pi.dupno.com** > **Vhost**.
2.  Look for the `location /` block. It usually looks like this:
    ```nginx
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    ```
3.  If it is already there, **you are good to go**. This tells Nginx: "If the file doesn't exist, send the request to `index.php`", which is exactly what our application handles.

## 6. Install SSL
1.  Go to **Sites** > **pi.dupno.com** > **SSL/TLS**.
2.  Click **Actions** > **New Let's Encrypt Certificate**.
3.  Click **Create**.

## 7. Verify
1.  Open `https://pi.dupno.com` in your browser.
2.  You should see the login page.
3.  Try logging in and creating an invoice.
