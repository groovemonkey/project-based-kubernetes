## Service Discovery
Services are used to expose your pod deployments to other applications in your Kubernetes cluster (or the outside world). They use readiness checks to make sure that traffic is only sent to ready/healthy pods, and let you do all kinds of fancy things.

Create a service with

    kubectl expose

e.g.
    # Create the deployment
    kubectl run alpaca-prod --image=gcr.io/kuar-demo/kuard-amd64:1 --replicas=3 --port=8080 --labels="ver=1,app=alpaca,env=prod"

    # Create a service
    kubectl expose deployment alpaca-prod

    # Check it out
    kubectl get services -o wide


Port forward and check out the service on localhost:48858

    ALPACA_POD=$(kubectl get pods -l app=alpaca -o jsonpath='{.items[0].metadata.name}')
    kubectl port-forward $ALPACA_POD 48858:8080

    # DNS is created for you
    #  servicename.namespace.type.clusterbasedomain
    dig alpaca-prod.default.svc.cluster.local


## Exposing a Service to the outside world with NodePorts
A NodePort maps a certain port to a service, so that any Kubernetes node receiving traffic there forwards it to your service (which distributes it to a healthy pod).

Set spec.type for your service to NodePort.


## Using DigitalOcean load balancers (yay integration)
Let's do this like professionals, and create a Cloud Load Balancer that points to our service.

Set spec.type for your service to LoadBalancer.

Check your EXTERNAL IP (the public DNS name or IP of your Cloud Load Balancer) and feel free to point DNS at it!

    kubectl get services

######### TODO add service definition using LoadBalancer.




### Advanced: Endpoints
Each service also comes with 'endpoint' objects. So if you're writing software that *knows* it can hook into Kubernetes for service discovery, you can have your application do things like this, instead of hardcoding IPs or using DNS:

    kubectl get endpoints alpaca-prod --watch
    kubectl describe endpoints alpaca-prod


