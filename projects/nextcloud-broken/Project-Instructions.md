Once you've got your DO kubernetes cluster set up, and your CCM deployment running on it...    

## NFS Setup
We need to take our DO ReadWriteOnce volume and use it like a ReadWriteMany volume -- both the nextcloud php-fpm pods and the nginx pods need access to it. So we're going to muck around with Kubernetes' insides and create a custom storage class.

    git clone https://github.com/kubernetes-incubator/external-storage/
    mv external-storage kubernetes-external-storage
    cd kubernetes-external-storage/nfs

Up-to-date instructions are in README.md, but here are the important parts:

    # edit these manifests before applying them and change 'example' to 'tutorialinux' or whatever else you want.
    kubectl create -f deploy/kubernetes/deployment.yaml
    kubectl create -f deploy/kubernetes/class.yaml
    kubectl create -f deploy/kubernetes/claim.yaml




## Mysql Setup

    # Database root password
    kubectl create secret generic nc-db-rootpass --from-file=./secrets/mysql-rootpass.txt

    # Create mysql volume, replicaset, expose service
    kubectl apply -f manifests/mysql-volume-claim.yaml
    kubectl apply -f manifests/mysql-replicaset.yaml  
    kubectl apply -f manifests/mysql-service.yaml

    # See what we just created
    kubectl get pv
    kubectl get secrets
    kubectl get replicasets
    kubectl get pods
    kubectl describe pod $YOURPOD
    kubectl logs $YOURPOD


## NextCloud Setup

    kubectl create cm --from-file configs/nextcloud.conf nextcloud-config

    kubectl apply -f manifests/nextcloud-datavolume-claim.yaml
    kubectl apply -f manifests/nextcloud-fpm-deployment.yaml
    kubectl apply -f manifests/nextcloud-service.yaml

Check out the pattern for getting a single config file into a container in nextcloud-fpm-deployment.yaml. This is currently the best practice. Yuck!


## Nginx Setup

    kubectl create cm --from-file configs/nginx-nextcloud.conf nginx-config

    kubectl apply -f manifests/nginx-deployment.yaml
    kubectl apply -f manifests/nginx-service-DO-LB.yaml

