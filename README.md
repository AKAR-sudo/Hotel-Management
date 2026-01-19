# Hotel Management System

A comprehensive web-based hotel management system built with PHP and MySQL, featuring role-based access control for administrators, staff, and guests.

## Features

### User Management
- **Multi-role Authentication**: Support for Admin, Staff, and User roles
- **Secure Login/Registration**: Password hashing with bcrypt
- **Profile Management**: User profile updates and management

### Hotel & Room Management
- **Hotel CRUD Operations**: Add, edit, delete, and manage hotels
- **Room Management**: Manage room types, pricing, capacity, and availability
- **Image Upload**: Support for hotel and room image uploads

### Booking System
- **Online Reservations**: Users can search and book rooms
- **Booking Management**: View booking history, manage reservations
- **Check-in/Check-out**: Staff can process guest check-ins and check-outs

### Room Service
- **Service Menu**: Manage room service items with categories
- **Order Management**: Place and track room service orders
- **Real-time Status Updates**: Track order preparation and delivery

### Maintenance & Services
- **Maintenance Requests**: Report and track room maintenance issues
- **Service Requests**: Handle guest service requests
- **Priority Management**: Categorize requests by priority levels

### Payment Processing
- **Payment Integration**: Support for various payment methods
- **Transaction Tracking**: Monitor payment status and history

### Analytics & Reporting
- **Booking Reports**: Generate reports on bookings and revenue
- **User Analytics**: Track user activity and engagement
- **Hotel Performance**: Monitor hotel occupancy and performance

## Tech Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+ / MariaDB 10.4+
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS with Font Awesome icons
- **Database Access**: PDO (PHP Data Objects)
- **Session Management**: PHP Sessions
- **File Uploads**: Support for image uploads

## Prerequisites

Before running this application, make sure you have the following installed:

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.4+
- Web server (Apache/Nginx recommended)
- Composer (optional, for dependency management)

## Installation

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd hotel-management-system
   ```

2. **Database Setup**
   - Create a new MySQL database named `hotel`
   - Import the database schema from `hotel.sql`
   ```sql
   mysql -u root -p hotel < hotel.sql
   ```

3. **Configuration**
   - Update database credentials in `includes/config.php` if needed
   - Ensure the `uploads/` directory is writable by the web server

4. **Web Server Configuration**
   - Point your web server document root to the project directory
   - Ensure PHP is properly configured with required extensions:
     - PDO MySQL extension
     - File upload support
     - Session support

5. **Access the Application**
   - Open your browser and navigate to the application URL
   - Default login credentials:
     - Admin: username - "هاوکار ئەدمین", password - (check database)
     - Staff: username - "هاوکار ستاف", password - (check database)
     - User: username - "هاوکار یوسەر", password - (check database)

## Project Structure

```
hotel-management-system/
├── admin/                    # Admin panel files
│   ├── admin_dashboard.php
│   ├── manage_hotels.php
│   ├── manage_rooms.php
│   ├── manage_users.php
│   └── ...
├── staff/                    # Staff panel files
│   ├── staff_dashboard.php
│   ├── manage_checkins.php
│   ├── room_status.php
│   └── ...
├── user/                     # User panel files
│   ├── user_dashboard.php
│   ├── book_hotel.php
│   ├── booking_history.php
│   └── ...
├── includes/                 # Shared PHP files
│   ├── config.php           # Database configuration
│   ├── booking_handler.php
│   ├── payment_processor.php
│   └── ...
├── uploads/                  # File uploads directory
│   ├── hotels/              # Hotel images
│   └── rooms/               # Room images
├── hotel.sql                # Database schema
├── login.php                # Login page
├── register.php             # Registration page
└── logout.php               # Logout functionality
```

## Database Schema

The application uses the following main tables:

- `users` - User accounts with role-based access
- `hotels` - Hotel information and details
- `rooms` - Room details and availability
- `bookings` - Reservation records
- `payments` - Payment transactions
- `guests` - Guest information
- `staff` - Staff member details
- `maintenance_requests` - Maintenance issue tracking
- `room_service_menu` - Room service items
- `room_service_orders` - Service order records
- `notifications` - System notifications
- `reviews` - Guest reviews and ratings

## Usage

### For Administrators
- Manage hotels, rooms, and users
- View reports and analytics
- Handle maintenance requests
- Configure system settings

### For Staff
- Process check-ins and check-outs
- Manage room status
- Handle service requests
- Update maintenance status

### For Users
- Search and book hotels/rooms
- View booking history
- Request room services
- Leave reviews and ratings

## Security Features

- Password hashing using bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Session-based authentication
- Role-based access control
- File upload validation

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please contact the development team or create an issue in the repository.

## Acknowledgments

- Built with PHP and MySQL
- Uses Font Awesome for icons
- Kurdish language support with RTL layout
