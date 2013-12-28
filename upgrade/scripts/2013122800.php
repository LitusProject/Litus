<?php

removeConfigKey($connection, 'cudi.queue_socket_port');
removeConfigKey($connection, 'cudi.queue_socket_host');
removeConfigKey($connection, 'cudi.queue_socket_remote_host');

addConfigKey($connection, 'cudi.queue_socket_file', 'tcp://127.0.0.1:8899', 'The file used for the websocket of the queue');
addConfigKey($connection, 'cudi.queue_socket_public', '127.0.0.1:8899', 'The public address for the websocket of the queue');

removeConfigKey($connection, 'sport.queue_socket_port');
removeConfigKey($connection, 'sport.queue_socket_host');
removeConfigKey($connection, 'sport.queue_socket_remote_host');

addConfigKey($connection, 'sport.queue_socket_file', 'tcp://127.0.0.1:8897', 'The file used for the websocket of the queue');
addConfigKey($connection, 'sport.queue_socket_public', '127.0.0.1:8897', 'The public address for the websocket of the queue');

removeConfigKey($connection, 'syllabus.update_socket_port');
removeConfigKey($connection, 'syllabus.update_socket_host');
removeConfigKey($connection, 'syllabus.update_socket_remote_host');

addConfigKey($connection, 'syllabus.update_socket_file', 'tcp://127.0.0.1:8898', 'The file used for the websocket of the syllabus update');
addConfigKey($connection, 'syllabus.update_socket_public', '127.0.0.1:8898', 'The public address for the websocket of the syllabus update');