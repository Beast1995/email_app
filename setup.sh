#!/bin/bash

echo "🚀 Setting up Bulk Email Application..."

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "ℹ️  .env file already exists"
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed sample data
echo "🌱 Seeding sample data..."
php artisan db:seed --class=EmailTemplateSeeder --force

# Set proper permissions
echo "🔒 Setting file permissions..."
chmod -R 755 storage bootstrap/cache

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your database settings in .env file"
echo "2. Configure your email settings in .env file"
echo "3. Start the development server: php artisan serve"
echo "4. Visit http://localhost:8000 to access the application"
echo ""
echo "📚 For detailed setup instructions, see README.md"
echo "" 