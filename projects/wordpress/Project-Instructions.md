Once you've got your DO kubernetes cluster set up, and your CCM deployment running on it...    


## Mysql Setup

Secret files require base64-encoded values if you want to use them in a sane way (--from-file is hopelessly broken with .env files).

Generate new MYSQL_PASSWORD and MYSQL_ROOT_PASSWORD values like this and replace them in secrets/wp-mysql-secrets.env:

    echo && cat /dev/urandom | env LC_CTYPE=C tr -dc [:alnum:] | head -c 15 | base64 && echo


Now, Create an all-in-one secret:

    kubectl apply -f secrets/wp-mysql-secrets.yaml


Create your mysql volume and replicaset. Expose this new internal service.

    kubectl apply -f manifests/mysql-volume-claim.yaml
    kubectl apply -f manifests/mysql-replicaset.yaml
    kubectl apply -f manifests/mysql-service.yaml


Get a shell inside the mysql container, log into mysql, and set up the DB:

    kubectl exec -it mysql-h2nsl -- bash
    mysql -u root -p # use the root password you created earlier  

    CREATE DATABASE IF NOT EXISTS wordpress;
    CREATE USER 'wordpress'@'10.0.0.0/255.0.0.0' IDENTIFIED BY 'MYSQL_PASSWORD';
    GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'10.0.0.0/255.0.0.0';
    FLUSH PRIVILEGES;

Ctrl-d to get back out.


Check out what we just created!

    kubectl get pv
    kubectl get secrets
    kubectl get replicasets
    kubectl get pods
    kubectl describe pod $YOURPOD
    kubectl logs $YOURPOD


## Wordpress Setup

Edit the config file at configs/apache.conf if you want to use a domain name for your WordPress site.

    kubectl create cm --from-file configs/wp-config.php wordpress-config
    kubectl create cm --from-file configs/apache.conf apache-config

    kubectl apply -f manifests/wordpress-datavolume-claim.yaml
    kubectl apply -f manifests/wordpress-deployment.yaml
    # kubectl apply -f manifests/wordpress-service.yaml # not needed because of DO load balancer service?

Check out the pattern for getting a single config file into a container in wordpress-deployment.yaml. This is currently the best practice. Yuck!



## Load Balancer Setup
It's just exposing our app to the Internet, not really load-balancing (because we're running a stateful singleton).

    kubectl apply -f manifests/DO-loadbalancer.yaml
