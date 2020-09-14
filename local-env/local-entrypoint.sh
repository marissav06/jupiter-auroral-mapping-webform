#!/bin/bash

cp /local-env/aeacus_database.php /var/www/html/dbin/jupiter-auroral-mapping/lib/BU/PrivilegeManager/config/database.php
cp /local-env/app_database.php /var/www/html/dbin/jupiter-auroral-mapping/config/database.php

exec /all-entrypoints.sh "$@"
