# Skill Learning Academy Marketplace

A professional full-stack web application built using PHP, MySQL, Bootstrap 5, and vanilla CSS. It features a complete role-based system (Student, Instructor, Admin) with secure authentication, a course marketplace, custom dashboards, and modern UI components.

## 📂 Project Structure

```text
skill-academy/
│
├── index.php                 # Main course marketplace / landing page
├── login.php                 # Secure login page (password_verify)
├── register.php              # Registration page (password_hash)
├── logout.php                # Secure session destruction
├── database.sql              # Clean complete MySQL database schema
├── README.md                 # Project documentation and setup guide
│
├── includes/
│   ├── db.php                # PDO Database connection
│   ├── auth.php              # Session checks & Role-Based Access Control logic
│   ├── header.php            # Global header + dynamic navigation
│   ├── footer.php            # Global footer
│   └── sidebar.php           # Role-based dashboard sidebar
│
├── dashboard/
│   ├── student.php           # Enrolled courses and learning statistics
│   ├── instructor.php        # Created courses and earning analytics
│   └── admin.php             # Global platform overview and recent activity
│
├── courses/
│   ├── view_courses.php      # Course explorer with search functionality
│   ├── create_course.php     # Instructor form to publish new courses
│   ├── course_details.php    # Single course view with sticky Enroll CTA
│   └── enroll.php            # Backend enrollment controller & mock payment
│
├── admin/
│   ├── manage_users.php      # Global user directory and deletion panel
│   └── manage_courses.php    # Global course directory and moderation panel
│
└── assets/
    ├── css/
    │   └── style.css         # Modern SaaS CSS variables and custom animations
    ├── js/                   # (Ready for external scripts)
    └── images/               # (Ready for local assets)
```

---

## 🚀 How to Run in XAMPP

Follow these exact steps to run the platform locally on your machine using XAMPP:

### Step 1: Install & Set Up XAMPP
1. Download and install [XAMPP](https://www.apachefriends.org/index.html).
2. Open the **XAMPP Control Panel**.
3. Start the **Apache** and **MySQL** modules.

### Step 2: Place the Project Folder
1. Move or copy the entire `skill-academy` project folder.
2. Navigate to your XAMPP installation directory (normally `C:\xampp\htdocs\`).
3. Paste the `skill-academy` folder inside the `htdocs` directory.
   - The path should look exactly like: `C:\xampp\htdocs\skill-academy\`

### Step 3: Set up the Database
1. Open your web browser and navigate to exactly: **`http://localhost/phpmyadmin`**
2. In the top or left menu, click **Databases**.
3. Under "Create database", enter strictly: **`skill_academy`** and click **Create**.
4. Click on the newly created `skill_academy` database on the left sidebar.
5. Click on the **Import** tab at the top.
6. Click **Choose File** and select the `database.sql` file located inside the root of your `skill-academy` folder.
7. Scroll down and click **Import** (or "Go"). This will instantly structure your tables and insert the default Admin payload.

### Step 4: Access the Application
1. Open a new tab in your browser.
2. Navigate to: **`http://localhost/skill-academy`**
3. The platform will boot up!

---

## 🔐 Default Admin Account
A default administrator account has automatically been injected into your database for testing purposes.

* **Email:** `admin@skillacademy.com`
* **Password:** `admin123`

---

## 🛠️ Tech Stack Used

* **Frontend:** HTML5, CSS3, Bootstrap 5.3, FontAwesome 6, Google Fonts (Inter)
* **Backend:** PHP 8+ (Vanilla)
* **Database:** MySQL (Strict PDO syntax, prepared statements)
* **Security:** `password_hash()` (Bcrypt), strict Session isolation, XSS output sanitation, strict RBAC routing.
