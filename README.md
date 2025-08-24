# Bulk Email Application

A comprehensive Laravel application for sending bulk emails with customizable templates, designed to ensure emails land in the inbox rather than spam folders.

## Features

### 🎯 Core Features
- **Custom Email Templates**: Create and manage reusable email templates with variable substitution
- **Bulk Email Campaigns**: Send emails to multiple recipients with campaign tracking
- **Anti-Spam Measures**: Built-in features to improve email deliverability
- **Email Tracking**: Monitor delivery status, bounces, and spam reports
- **Rate Limiting**: Configurable sending rates to avoid being flagged as spam
- **Unsubscribe Management**: Built-in unsubscribe functionality for compliance

### 📧 Email Features
- **Professional Templates**: Responsive HTML email templates
- **Variable Substitution**: Dynamic content with {{variable}} syntax
- **SMTP Configuration**: Support for multiple email providers
- **Delivery Optimization**: Proper headers and formatting for inbox delivery
- **Template Preview**: Preview emails before sending

### 📊 Analytics & Monitoring
- **Campaign Statistics**: Track success rates, delivery rates, and failures
- **Email Logs**: Detailed logs of all email activities
- **Real-time Status**: Monitor campaign progress in real-time
- **Performance Metrics**: Analyze campaign performance

## Anti-Spam Features

### 🔒 Technical Measures
- **Proper Email Headers**: Includes all necessary headers for legitimate emails
- **Rate Limiting**: Configurable delays between emails (default: 1.2 seconds)
- **Batch Processing**: Sends emails in controlled batches
- **Unsubscribe Headers**: Proper List-Unsubscribe headers
- **SPF/DKIM Ready**: Configured for email authentication

### 📋 Best Practices
- **Content Optimization**: Clean, professional email content
- **Sender Reputation**: Proper from address configuration
- **Compliance**: CAN-SPAM Act compliant unsubscribe functionality
- **Monitoring**: Track bounces and spam reports

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL database
- SMTP email service (Gmail, SendGrid, Mailgun, etc.)

### Step 1: Clone and Install
```bash
# Navigate to your project directory
cd bulk-email-app

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env
```

### Step 2: Configure Environment
Edit `.env` file with your settings:

```env
APP_NAME="Bulk Email App"
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bulk_email_app
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Email Configuration (Gmail Example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 3: Database Setup
```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed --class=EmailTemplateSeeder
```

### Step 4: Start the Application
```bash
# Start the development server
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## Usage

### Creating Email Templates

1. Navigate to **Email Templates** → **New Template**
2. Fill in the template details:
   - **Name**: Template identifier
   - **Subject**: Email subject line (supports variables)
   - **Content**: HTML email content (supports variables)
   - **Variables**: Available variables for this template

### Variable Syntax
Use `{{variable_name}}` in your templates:
```html
<h2>Welcome {{name}}!</h2>
<p>Thank you for joining {{company_name}}.</p>
```

### Creating Campaigns

1. Navigate to **Campaigns** → **New Campaign**
2. Select an email template
3. Add recipients (email and name)
4. Configure campaign settings
5. Send immediately or schedule for later

### Recipient Format
```json
[
    {
        "email": "john@example.com",
        "name": "John Doe"
    },
    {
        "email": "jane@example.com", 
        "name": "Jane Smith"
    }
]
```

## Email Provider Setup

### Gmail
1. Enable 2-factor authentication
2. Generate an App Password
3. Use the App Password in your `.env` file

### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

## Configuration Options

### Rate Limiting
Edit `app/Services/BulkEmailService.php`:
```php
protected $maxEmailsPerMinute = 50; // Emails per minute
protected $delayBetweenEmails = 1.2; // Seconds between emails
```

### Email Headers
Customize headers in `app/Mail/BulkEmail.php` for your specific needs.

## Security Considerations

### Email Security
- Use strong SMTP passwords
- Enable 2FA on email accounts
- Use dedicated email addresses for sending
- Monitor sender reputation

### Application Security
- Keep Laravel updated
- Use HTTPS in production
- Implement proper authentication
- Regular security audits

## Troubleshooting

### Common Issues

**Emails going to spam:**
- Check sender reputation
- Verify SPF/DKIM records
- Review email content
- Monitor bounce rates

**Sending failures:**
- Verify SMTP credentials
- Check email provider limits
- Review error logs
- Test with small batches

**Template issues:**
- Validate HTML syntax
- Check variable names
- Test template rendering
- Preview before sending

## Production Deployment

### Server Requirements
- PHP 8.1+
- MySQL 5.7+ or PostgreSQL 10+
- Redis (for queues)
- SSL certificate

### Recommended Setup
1. Use a production web server (Nginx/Apache)
2. Set up SSL certificates
3. Configure database backups
4. Set up monitoring
5. Use queue workers for email processing

### Queue Configuration
```bash
# Start queue workers
php artisan queue:work

# For production, use supervisor to manage queue workers
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions:
- Check the documentation
- Review the troubleshooting section
- Create an issue on GitHub

---

**Note**: This application is designed for legitimate email marketing. Always comply with email regulations (CAN-SPAM, GDPR, etc.) and respect recipient preferences.
