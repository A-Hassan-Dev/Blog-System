# PHP Blog System

A professional, responsive blog management system built with PHP and MySQL. This project features a clean user interface for readers and a comprehensive admin panel for content creators.

## 🚀 Features

### Frontend (User Interface)
- **Latest Posts**: Homepage displays the most recent published articles with images and excerpts.
- **Reading Experience**: Dedicated post pages for reading full articles.
- **Responsive Design**: Fully responsive layout powered by Bootstrap 5, optimized for mobile, tablet, and desktop.

### Backend (Admin Panel)
- **Post Management**: Create, edit, delete, and manage post status (Draft vs. Published).
- **Category Management**: Organize posts into custom categories.
- **Comment System**: Moderation tools to approve or reject reader comments.
- **User Management**: Manage authors and administrators.
- **Image Uploads**: Integrated image handling for post thumbnails.

## 🛠️ Tech Stack
- **Backend**: PHP 8.x
- **Database**: MySQL (PDO for secure queries)
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons

## 📋 Installation

1. **Clone the project** to your local server directory (e.g., `htdocs` or `www`).
2. **Setup Database**:
   - Create a database named `phpblog`.
   - Import the `schema.sql` file into your MySQL server.
3. **Configure Connection**:
   - Open `config/database.php`.
   - Update the `$host`, `$dbname`, `$username`, and `$password` variables with your local database credentials.
4. **Run**:
   - Start your local server (XAMPP, WAMP, or built-in PHP server).
   - Access the homepage at `http://localhost/blog_system/index.php`.

## 🔐 Admin Access

To access the admin panel, navigate to `/admin/index.php`.

**Default Credentials:**
- **Email**: `admin@blog.com`
- **Password**: `password`

## 📂 Project Structure
```text
blog_system/
├── admin/          # Admin panel dashboard and tools
├── assets/         # CSS and images
├── config/         # Database configuration
├── uploads/        # Uploaded post images
├── index.php       # Blog homepage
├── post.php        # Single post view
└── schema.sql      # Database structure
```

---
Built with ❤️ for simple and effective blogging.


