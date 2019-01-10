# Nextcloud setup

https://github.com/nextcloud/docker

BESTFIX: Create shared storage for multiple pods from a RWO volume (like DO supports):
    https://github.com/kubernetes-incubator/external-storage/tree/master/nfs


BORINGFIX: move fpm and nginx containers into a single pod, together. Storage is shared.

BORINGFIX: run this as a singleton (replicaset with 1 replica) using the apache container (instead of php-fpm) and it should work.

NOTE: because nextcloud requires a shared volume between multiple containers (pods), we *can't* yet set it up on DigitalOcean.

In Kubernetes terms, we need to mount this PersistentVolume with the ReadWriteMany accessmode (https://kubernetes.io/docs/concepts/storage/persistent-volumes/#access-modes), and then add VolumeMounts in both nginx and php-fpm deployments.

# 1. php-fpm container

## Volumes

    /var/www/html/ folder where all nextcloud data lives


## Env Vars

    MYSQL_DATABASE=nextcloud - Name of the database using mysql / mariadb.
    MYSQL_USER=nextcloud - Username for the database using mysql / mariadb.
    MYSQL_PASSWORD=generate_and_insert_secret_here - Password for the database user using mysql / mariadb.
    MYSQL_HOST= - Hostname of the database server using mysql / mariadb.



# 2. nginx container

This is just a fastcgi proxy.


## Volumes:

    /var/www/html/ (same one mounted into the php-fpm containers)

