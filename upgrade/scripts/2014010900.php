<?php

pg_query($connection, 'ALTER TABLE api.keys ADD check_host BOOLEAN');
pg_query($connection, 'UPDATE api.keys SET check_host = TRUE');
