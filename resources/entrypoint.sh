#!/usr/bin/env bash
#
# @file      entrypoint.sh
# @version   0.0.1
# @brief     Docker entrypoint script
#
# ------------------------------------------------------------------------------
#
# Leave if any command fails
set -e

# Run Apache in background and output the logs in foreground
service apache2 start

# Habilita o pdo_oci no php
php5enmod pdo_oci
php5enmod oci8

# Restarta o Apache
service apache2 restart

tail -f /var/log/apache2/access.log

# Used for debugging if no foreground application is being called
# while true; do
#    date
#    sleep 5
# done
