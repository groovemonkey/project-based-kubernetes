## Prerequisites
Once you've got your DO kubernetes cluster set up, and your CCM deployment running on it (see ~/02-digitalocean-setup.md)...

## Notes
Check out the wordpress docker documentation here: https://hub.docker.com/_/wordpress/

Pay attention to:
- environment variables that the container uses (INPUT)
- which processes run inside the container
- service ports that are exposed by the container (OUTPUT)


### Infrastructure Diagram:
On the left is a traditional diagram for this 3-tier web application. On the right, you see how each part of that infrastructure maps to kubernetes concepts.

Don't worry if this doesn't make sense at the beginning.

    Some config data            (k8s ConfigMaps and Secrets)

    MySQL Container             (k8s replicaset)
    MySQL Service               (k8s service)
        |
        |
    WordPress Container         (k8s deployment)
    [ apache and php-fpm ]
        |
        |
    DO Loadbalancer             (k8s service)


## 1. Mysql Setup

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

    kubectl get pods
    kubectl exec -it mysql-abcde -- bash    # replace mysql-abcde with the actual pod name

    # use the root password you created earlier (secrets/wp-mysql-secrets.yaml)
    mysql -u root -p

    # In your mysql shell:
    CREATE DATABASE wordpress;

Ctrl-d to get back out.


Check out what we just created!

    kubectl get pv
    kubectl get secrets
    kubectl get replicasets
    kubectl get pods
    kubectl describe pod $YOURPOD
    kubectl logs $YOURPOD


## 2. Wordpress Setup

Edit the config file at configs/apache.conf if you want to use a domain name for your WordPress site.

    # If you were putting a custom apache config file on your containers, this is the pattern
    # you would use:
    # kubectl create cm --from-file configs/apache.conf apache-config

    kubectl apply -f manifests/wordpress-datavolume-claim.yaml
    kubectl apply -f manifests/wordpress-deployment.yaml

Check out the pattern for getting a single config file into a container in wordpress-deployment.yaml. This is currently the best practice. Yuck!


## 3. Load Balancer Setup
It's just exposing our app to the Internet, not really load-balancing (because we're running a stateful singleton).

    kubectl apply -f manifests/DO-loadbalancer.yaml


## View your work!
Grab the load balancer's external IP here:

    kubectl get services

You can also see this in your DO dashboard: Networking --> Load Balancers.


## Cleanup
To delete everything and start over, go into your DO dashboard and:

1. Delete the kubernetes cluster
1. Networking --> Load Balancers --> LB Settings --> Delete
1. Volumes --> Delete your volumes


## Restarting from a blank slate:
See ~/02-digitalocean-setup.md, or:

1. In your DO dashboard, create a new 'tl-testcluster' kubernetes cluster
1. Download the cluster config file (scroll down)
1. mv ~/Downloads/tl-testcluster-kubeconfig.yaml ~/.kube/config
1. Once `kubectl get nodes` shows your nodes as READY (~5min), continue with the next step.
1. cd digitalocean-cloud-controller-manager
1. kubectl apply -f releases/secret.yml
1. kubectl apply -f releases/v0.1.8.yml
1. cd $THIS_PROJECT/projects/wordpress/
1. Start with the MySQL Setup section at the beginning of this document!
