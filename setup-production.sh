#!/bin/bash

# Production Setup Script for Laravel Queue Worker and Cron Job
# Run this script on your production server

echo "=========================================="
echo "Laravel Production Setup Script"
echo "=========================================="

# Get project path
read -p "Enter your project path (e.g., /var/www/html/part_synch_mob_app): " PROJECT_PATH

if [ ! -d "$PROJECT_PATH" ]; then
    echo "Error: Project path does not exist!"
    exit 1
fi

echo ""
echo "Project path: $PROJECT_PATH"
echo ""

# Step 1: Install Supervisor
echo "Step 1: Installing Supervisor..."
if command -v apt-get &> /dev/null; then
    sudo apt-get update
    sudo apt-get install -y supervisor
elif command -v yum &> /dev/null; then
    sudo yum install -y supervisor
else
    echo "Error: Package manager not found. Please install Supervisor manually."
    exit 1
fi

# Step 2: Create Supervisor Configuration
echo ""
echo "Step 2: Creating Supervisor configuration..."
sudo tee /etc/supervisor/conf.d/laravel-worker.conf > /dev/null <<EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $PROJECT_PATH/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$PROJECT_PATH/storage/logs/worker.log
stopwaitsecs=3600
EOF

echo "Supervisor configuration created!"

# Step 3: Set Permissions
echo ""
echo "Step 3: Setting permissions..."
sudo chown -R www-data:www-data $PROJECT_PATH/storage
sudo chown -R www-data:www-data $PROJECT_PATH/bootstrap/cache
sudo chmod -R 775 $PROJECT_PATH/storage
sudo chmod -R 775 $PROJECT_PATH/bootstrap/cache

echo "Permissions set!"

# Step 4: Start Supervisor
echo ""
echo "Step 4: Starting Supervisor..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

echo ""
echo "Supervisor status:"
sudo supervisorctl status laravel-worker:*

# Step 5: Setup Crontab
echo ""
echo "Step 5: Setting up Crontab..."
(crontab -l 2>/dev/null; echo "* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo "Crontab configured!"

# Step 6: Create Queue Tables (if using database queue)
echo ""
read -p "Do you want to create queue tables? (y/n): " CREATE_QUEUE
if [ "$CREATE_QUEUE" = "y" ]; then
    cd $PROJECT_PATH
    php artisan queue:table
    php artisan migrate
    echo "Queue tables created!"
fi

# Step 7: Test
echo ""
echo "Step 7: Testing setup..."
cd $PROJECT_PATH

echo "Testing schedule list:"
php artisan schedule:list

echo ""
echo "Testing queue worker:"
php artisan queue:work --once

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Verify Supervisor: sudo supervisorctl status"
echo "2. Verify Cron: crontab -l"
echo "3. Check logs: tail -f $PROJECT_PATH/storage/logs/worker.log"
echo "4. Test command: cd $PROJECT_PATH && php artisan boost:expire"
echo ""
echo "=========================================="

