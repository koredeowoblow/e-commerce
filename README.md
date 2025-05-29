
# 🎓 Student E-Marketplace API

A Laravel-based backend API designed to bridge the gap between students and campus vendors. This project enables product listing, vendor and student registration, secure authentication using JWT, and real-time chat functionality.

---

## 🚀 Features

- 🔐 JWT Authentication
- 🛍️ Vendor and Student registration/login
- 🧾 Product listing and management
- 📦 Category, tag, and variation support
- 🌍 Country, state, and city selection
- 📧 OTP-based email verification
- 💬 Real-time chat with WebSockets (Laravel Reverb)
- 🎯 Role & permission-based access control

---

## 🛠️ Tech Stack

- **Framework:** Laravel 10+
- **Authentication:** JWT
- **Database:** MySQL
- **Real-Time Chat:** Laravel Reverb
- **Testing:** Postman
- **Mailing:** SMTP

---

## ⚙️ Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/koredeowoblow/e-commerce.git
cd e-commerce
````

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure `.env`

Update your database and mail settings:

```env
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=you@example.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Start the Server

```bash
php artisan serve
```

### 7. Start WebSocket Server

```bash
php artisan reverb:start
```

---

## 📡 WebSocket (Chat System)

Real-time messaging between vendors and students using Laravel Reverb.

* Uses private channels
* Events and Listeners to broadcast messages
* Works with JWT-authenticated users

---

## 🔐 API Authentication

This API uses JWT for authentication. Send the token with requests in the `Authorization` header:

```
Bearer YOUR_JWT_TOKEN_HERE
```

---

## 🔁 OTP Verification

OTP is emailed to the user after registration using a custom mailable (`SendOtpMail`). Example:

```php
Mail::to($user->email)->send(new SendOtpMail($user));
```

---

## 🔗 Endpoints Summary

| Method | Endpoint         | Description                    |
| ------ | ---------------- | ------------------------------ |
| POST   | /api/register    | Register a student/vendor      |
| POST   | /api/login       | Login and receive token        |
| GET    | /api/products    | List all products              |
| POST   | /api/products    | Create a product (vendor only) |
| GET    | /api/chat/{user} | Get messages with a user       |

---

## 📂 Folder Overview

```
app/
├── Http/Controllers/
├── Events/
├── Models/
├── Mail/
├── Listeners/
routes/api.php
resources/views/emails/
```

---

## 📄 License

This project is open-source and available under the MIT License.

---

## 👨‍💻 Author

**Owolabi Shinaayomi Akorede**
📧 [daystarowolabi@gmail.com](mailto:daystarowolabi@gmail.com)
🔗 [LinkedIn](https://ng.linkedin.com/in/shinaayomi-owolabi-192210329)
🐙 [GitHub](https://github.com/koredeowoblow)

```

---

Let me know if you want me to create an API documentation file (`docs/api.md`) or Postman collection to match this.
```
