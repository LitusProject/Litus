#!/bin/bash

# Get the current username
CURRENT_USER=$(whoami)

# Create the service file with the specified contents
sudo bash -c "cat > /etc/systemd/system/litus-sockets.service <<EOL
[Unit]
Description=Litus Sockets
After=network-online.target
Wants=network-online.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/home/$CURRENT_USER/Projects/LitusProject/Litus
Environment=APPLICATION_ENV=development
ExecStartPre=/bin/mkdir -p /var/log/litus
ExecStartPre=/bin/chown -R www-data:www-data /var/log/litus
ExecStartPre=/bin/mkdir -p /var/run/litus
ExecStartPre=/bin/chown -R www-data:www-data /var/run/litus
ExecStart=/home/$CURRENT_USER/Projects/LitusProject/Litus/bin/sockets.sh
Restart=on-failure

PermissionsStartOnly=true

[Install]
WantedBy=multi-user.target
EOL"

sleep 5

# Reload systemd to recognize the new service
sudo systemctl daemon-reload

# Enable the service to start on boot
sudo systemctl enable litus-sockets.service

# Start the service
sudo systemctl start litus-sockets.service