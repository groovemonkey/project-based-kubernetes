## Deployments
Rolling out new versions/release of your code.

    kubectl run


# Create a test deployment
vim dcohen-nginx-deployment.yml

    apiVersion: apps/v1
    kind: Deployment
    metadata:
      name: nginx-deployment-example
    spec:
      replicas: 1
      selector:
        matchLabels:
          app: nginx-deployment-example
      template:
        metadata:
          labels:
            app: nginx-deployment-example
        spec:
          containers:
            - name: nginx
              image: library/nginx


# Apply it
kubectl create -f ./dcohen-nginx-deployment.yml 
