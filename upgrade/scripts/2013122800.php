<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
