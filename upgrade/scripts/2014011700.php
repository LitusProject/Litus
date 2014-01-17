<?php

pg_query($connection, 'ALTER TABLE nodes.forms_entries ADD draft BOOLEAN');

pg_query($connection, 'UPDATE nodes.forms_entries SET draft = false');