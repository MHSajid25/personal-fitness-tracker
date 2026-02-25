# Personal Fitness Tracker

A comprehensive web-based fitness tracking application built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### User Types

1. **Admin**
   - Login interface
   - Manage trainers and users (view/add/update/delete)
   - Manage public content (fitness tips, routines)
   - Monitor user activity logs and feedback

2. **Trainer**
   - Login interface
   - View feedback on routines/plans created by the trainer
   - Respond to user feedback with suggestions and encouragement

3. **Registered User**
   - Registration and login
   - Log daily workouts, meals, and water intake
   - View progress statistics with charts
   - Share feedback on fitness routines and diet plans
   - Create and manage personal workout/diet plans
   - Receive suggestions from trainers

4. **Unregistered User**
   - Browse general fitness content (workout routines, tips, diet plans)
   - Must register to create personal plans or track progress

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Web browser

### Setup Steps

1. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u root -p < database/schema.sql
   ```
   
   Or manually:
   - Create a MySQL database
   - Import `database/schema.sql` into your database

2. **Configuration**
   - Edit `config/database.php` and update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'fitness_tracker');
     ```

3. **Web Server Setup**
   - Place the project in your web server directory (e.g., `htdocs`, `www`, or `/var/www/html`)
   - Ensure PHP is enabled
   - Make sure the web server has read/write permissions

4. **Access the Application**
   - Open your browser and navigate to: `http://localhost/project1/`

## Default Login Credentials

After importing the database, you'll need to set up your own admin and trainer accounts, or update the password hashes in the database.

**To create a new admin account:**
1. Register a new user account through the registration page
2. Manually update the `user_type` to 'admin' in the database:
   ```sql
   UPDATE users SET user_type = 'admin' WHERE username = 'your_username';
   ```

**Or update existing default accounts:**
The schema includes default admin and trainer accounts, but you'll need to update their passwords. You can generate a new password hash using:
```php
<?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>
```

Then update the database:
```sql
UPDATE users SET password = 'generated_hash' WHERE username = 'admin';
```

## Project Structure

```
project1/
├── admin/              # Admin interface
│   ├── dashboard.php
│   ├── manage-users.php
│   ├── manage-trainers.php
│   ├── manage-content.php
│   ├── activity-logs.php
│   └── view-feedback.php
├── assets/
│   └── css/
│       └── style.css  # Main stylesheet
├── config/
│   ├── config.php     # Configuration and helper functions
│   └── database.php   # Database connection
├── database/
│   └── schema.sql     # Database schema
├── public/            # Public pages (unregistered users)
│   ├── routines.php
│   ├── tips.php
│   └── diet-plans.php
├── trainer/           # Trainer interface
│   ├── dashboard.php
│   ├── view-feedback.php
│   └── respond-feedback.php
├── user/              # Registered user interface
│   ├── dashboard.php
│   ├── log-activity.php
│   ├── progress.php
│   ├── plans.php
│   └── feedback.php
├── index.php          # Home page
├── login.php          # Login page
├── register.php       # Registration page
├── logout.php         # Logout handler
└── README.md          # This file
```

## Key Features

### Activity Logging
- Log workouts, meals, and water intake
- Track calories and nutrition
- Add notes to activities

### Progress Tracking
- View workout frequency charts
- Monitor calorie intake over time
- Track water consumption
- Visual progress with Chart.js

### Feedback System
- Users can rate and comment on routines/plans
- Trainers can respond with suggestions
- Admin can monitor all feedback

### Content Management
- Public workout routines with exercise details
- Fitness tips and advice
- Diet plans with meal information
- Admin can manage all content

## Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with prepared statements
- Session management for authentication
- User type-based access control
- Input sanitization with `htmlspecialchars()`

## Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Charts:** Chart.js (for progress visualization)
- **Styling:** Custom CSS with responsive design

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Future Enhancements

- Email notifications
- Mobile app
- Social sharing features
- Advanced analytics
- Workout video integration
- Meal planning calendar

## License

This project is created for educational purposes.

## Support

For issues or questions, please check the code comments or refer to the database schema for understanding the data structure.

