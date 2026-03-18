# 🎓 Skill Learning Academy Marketplace

A full-stack web application that enables users to learn, teach, and manage skill-based courses through a secure and interactive online platform.

---

## 🚀 Project Overview

The **Skill Learning Academy Marketplace** is a PHP & MySQL-based platform where:

* Students can browse and enroll in courses
* Instructors can create and manage courses
* Admins can monitor and control the platform

The system includes **secure authentication**, **role-based access**, and a **modern responsive UI**.

---

## ✨ Features

### 🔐 Authentication & Security

* Secure user registration and login
* Password hashing using `password_hash()`
* Login verification using `password_verify()`
* Session-based authentication
* Role-based access control

---

### 👨‍🎓 Student Features

* Browse available courses
* Search courses
* Enroll in courses
* View enrolled courses
* Dashboard with course statistics

---

### 👨‍🏫 Instructor Features

* Create and manage courses
* View enrolled students
* Instructor dashboard

---

### 👑 Admin Features

* Manage users
* Manage courses
* Monitor platform activity

---

### 🎨 UI/UX Features

* Responsive design using Bootstrap 5
* Modern dashboard with sidebar navigation
* Course cards layout (Udemy-style)
* Clean and intuitive interface

---

## 🛠️ Tech Stack

| Layer    | Technology                         |
| -------- | ---------------------------------- |
| Frontend | HTML, CSS, Bootstrap 5, JavaScript |
| Backend  | PHP                                |
| Database | MySQL                              |
| Server   | Apache (XAMPP)                     |

---

## 📂 Project Structure

```
skill-academy/
│
├── index.php
├── login.php
├── register.php
├── logout.php
│
├── includes/
│   ├── db.php
│   ├── header.php
│   ├── footer.php
│   ├── auth.php
│   └── sidebar.php
│
├── dashboard/
│   ├── student.php
│   ├── instructor.php
│   └── admin.php
│
├── courses/
│   ├── view_courses.php
│   ├── create_course.php
│   ├── course_details.php
│   └── enroll.php
│
├── admin/
│   ├── manage_users.php
│   └── manage_courses.php
│
└── assets/
    ├── css/
    ├── js/
    └── images/
```

---

## 🗄️ Database Schema

### Users Table

* id
* name
* email
* password
* role

### Courses Table

* id
* title
* description
* price

### Enrollments Table

* id
* user_id
* course_id

---

## ⚙️ Installation & Setup

### 1️⃣ Clone the Repository

```
git clone https://github.com/your-username/skill-academy.git
```

---

### 2️⃣ Move to XAMPP Directory

```
C:\xampp\htdocs\skill-academy
```

---

### 3️⃣ Start Server

* Start **Apache**
* Start **MySQL**

---

### 4️⃣ Create Database

Open:

```
http://localhost/phpmyadmin
```

Create database:

```
skill_academy
```

Import SQL tables (provided in project).

---

### 5️⃣ Run the Project

Open browser:

```
http://localhost/skill-academy
```

---

## 🔄 Application Flow

```
Register → Login → Dashboard → Browse Courses → Enroll → View Courses
```

---

## 🔒 Security Features

* Password hashing
* Session management
* Role-based authorization
* Basic input validation

---

## 📈 Future Enhancements

* Payment gateway integration
* Course video upload system
* Ratings & reviews
* Email verification
* AI-based course recommendations
* REST API integration
* Mobile app version

---

## 🤝 Contribution

Contributions are welcome!

1. Fork the repository
2. Create a new branch
3. Commit your changes
4. Push to GitHub
5. Open a Pull Request

---

## 📜 License

This project is for educational purposes.

---

## 👨‍💻 Author

**S A Syed Amrullah**

* GitHub: https://github.com/syed0299

---

## ⭐ Acknowledgements

* Bootstrap for UI components
* XAMPP for local development
* Google AI / Antigravity for code assistance

---
