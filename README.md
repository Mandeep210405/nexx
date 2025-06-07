# Nexx - Educational Platform

Nexx is a comprehensive educational platform designed to facilitate seamless interaction between students and faculty members. The platform provides a robust environment for managing study materials, assignments, classroom interactions, and academic resources.

## Features

### For Students
- Access study materials and resources
- View and submit assignments
- Join virtual classrooms
- Access previous papers and practical materials
- Watch educational videos
- Track academic progress
- Submit and view assignment submissions

### For Faculty
- Create and manage classrooms
- Upload study materials
- Create and grade assignments
- Manage student requests
- Track student progress
- Upload and manage educational videos

## Technology Stack

### Backend
- PHP 7.4+
- MySQL Database
- Apache Web Server

### Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap

### Additional Technologies
- AJAX for asynchronous operations
- File upload handling
- Session management
- Secure authentication system

## Project Structure

```
nexx/
├── admin/              # Admin panel files
├── ajax/              # AJAX handlers
├── assets/            # Static assets (CSS, JS, images)
├── config/            # Configuration files
├── database/          # Database related files
├── includes/          # Common PHP includes
├── uploads/           # User uploaded files
└── main files         # Core application files
```

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/nexx.git
```

2. Set up your web server (Apache) and point it to the project directory

3. Import the database:
   - Navigate to the `main database to import` directory
   - Import the SQL file into your MySQL database

4. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials

5. Set up file permissions:
   - Ensure the `uploads` directory is writable
   - Set appropriate permissions for configuration files

## Configuration

1. Database Configuration:
   - Update database credentials in `config/database.php`
   - Ensure proper database user permissions

2. File Upload Configuration:
   - Configure maximum upload size in PHP settings
   - Set appropriate file type restrictions

## Security Features

- Secure password hashing
- Session management
- Input validation and sanitization
- File upload security
- Access control for different user roles

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details

## Contact

Your Name - your.email@example.com
Project Link: https://github.com/yourusername/nexx 