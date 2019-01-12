# Archive

[12:43] <groovemonkey> Hi all, I'm looking for a way to mount a volume in a container, along with a configfile (from a configmap) at the same mountpoint. It looks like duplicate  mountpoints aren't allowed (I originally thought the mounts would just be applied in order). The error is "Invalid value: "/var/www/html": must be unique"

[12:44] <groovemonkey> the use case is a fairly straightforward wordpress install. I'm mounting a persistent volume for the wp install at /var/www/html, and then trying to add the wp-config.php file via a configmap inside that same directory

[12:45] <groovemonkey> I understand that I could hack around this by mounting the persistent volume at e.g. /var/www and then having the configmap mountpath be /var/www/html, but it just feels really kludgy. Am I missing something?


mysql.default.svc.cluster.local


mysql login for wordpress failing because it's logging in as user@IP

2019-01-10T20:51:46.318024Z 883 [Note] Access denied for user 'wordpress
'@'10.244.68.0' (using password: YES)

(just using username 'wordpress')


root:
NGiJi6A46YJTjTx

User:
IxB34qEqttDnmzQ

CREATE USER 'wordpress'@'10.0.0.0/255.0.0.0' IDENTIFIED BY 'IxB34qEqttDnmzQ';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'10.0.0.0/255.0.0.0';


<?php
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASSWORD', getenv('DB_PASSWORD'));
    define('DB_HOST', getenv('DB_HOST'));

    $db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
?>


CREATE USER 'wordpress'@'%' IDENTIFIED BY 'IxB34qEqttDnmzQ';

