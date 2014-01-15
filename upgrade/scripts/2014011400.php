<?php

addConfigKey($connection, 'br.cv_archive_years', 'a:0:{}', 'The cv archive years');

pg_query($connection, 'ALTER TABLE br.companies ADD cv_book_archive_years VARCHAR(255)');