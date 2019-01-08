Once you've got your DO kubernetes cluster set up, and your CCM deployment running on it...    


## Mysql Setup

    # root user
    kubectl create secret generic db-rootpass --from-file=./secrets/mysql-rootpass.txt

    # application user
    kubectl create secret generic db-user-pass --from-file=./secrets/mysql-username.txt --from-file=./secrets/mysql-pass.txt

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

