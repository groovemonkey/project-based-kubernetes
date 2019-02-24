Dave's instructions (working)


## Intro video:
In this mini-course, we're going to take a thorough look at the basics of Kubernetes. You're going to:

- set up a load-balanced web application that mixes stateful and stateless containers
- Learn the primitives that you'll use to configure your application on Kubernetes: Containers, Pods, Services, Deployments, ReplicaSets, DaemonSets
- leverage DigitalOcean's kubernetes service so you can save time and get right to defining and rolling out an application
- learn about Kubernetes' architecture (which will give you the context to understand how all of this works)
- avoid common problems and misunderstandings that can make using Kubernetes painful
- a bunch more stuff

### What is Kubernetes? TODO BLERGHHHHHHHH
Kubernetes is a lego set that lets you build an infrastructure platform for your application or distributed services. You will never assign services to specific nodes again. The main conceptual thing it does is SCHEDULE WORK on your 'datacenter computer.'

It's about getting away from managing individual, physical servers, and moving *towards* defining your application in a way that lets it feed on shared resources (CPU time, RAM, persistent storage, cloud infrastructure abstractions like load balancers) in a datacenter somewhere far away.


### What does kubernetes DO?
- It orchestrates and manages the containers that your application is made of
- It takes you from "some sofware packaged into containers" to "a complex real-life application, highly available, properly abstracted into separately scalable components, and straightforward to operate."


### Kubernetes Thinking

1. Pods manage containers
1. ReplicaSets manage Pods
1. Services expose pod processes to the outside world
1. ConfigMaps and Secrets configure Pods
1. Labels are the plumbing that ties everything together
1. Deployments manage the change from one set of containers to another (e.g. for a software release)

#### Additional Concepts
1. DaemonSets and Jobs give you alternate ways to think about running processes (e.g. sidecar containers and one-offs)
1. Provider-specific concepts like LoadBalancers let kubernetes tie into cloud-specific services and abstractions


#### Things I'm not covering
- Dynamic Volume Provisioning and StorageClass objects
- Datastore Replication using StatefulSets
- 


Requirements:
- a basic understanding of linux containers and why you might use them
- a DO account (link in description). You can get away without this but you'll have to adapt some of the things we do to whatever kubernetes setup you're using.


### What applications does kubernetes work best with?

A containerized microservice architecture. Separately scalable components. Stateful components decoupled from stateless components.

Getting your application architecture to that point is 90% of the battle. Defining, running, tooling, and maintaining it on Kubernetes is the other 90%. Yay job security!


# Practical Project

1. see 01-kubectl-setup.md
1. see 02-digitalocean-setup.md

# TODO Video 3: kubectl
Kubernetes is, like so many other things, a CRUD app. You can

- Create resources (kubectl apply)
- Read resources (kubectl get)
- Update resources (kubectl edit/apply)
- Delete resources (kubectl delete)

Get help with

    kubectl help

## kubectl basics: get
kubectl get nodes (you've already seen this)
kubectl get componentstatuses (what the kubernetes master is made of)

## More detail: describe
kubectl describe nodes node-1
kubectl describe <resource-name> <obj-name>


## output
Default output is shortened.

- use -o wide to get full output (like -v for other CLI applications)
- use -o json or -o yaml to get full output in machine-parsable formats
- if you're piping output into other Unix tools, use --no-headers to avoid having to trash the first line
- you can also use jsonpath output and pass in a template, but I prefer working with the 'jq' tool on the command line



## REMOVED FROM HERE: 04-kubernetes-objects



# TODO (bunchastuff?): Labels
Lots of stuff (load balancing) is done with labels. Add labels to objects like this:

Add a label to a pod object, overwrite that label value, then delete the label completely:

    kubectl label pods davespod color=red
    kubectl label --overwrite pods davespod color=blue
    kubectl label pods davespod color-



## REMOVED FROM HERE: 04-kubernetes-objects


# More advanced kubectl
kubectl get deployments --namespace=kube-system kube-dns
kubectl get deployments --namespace=kube-system kubernetes-dashboard (dashboard)
kubectl get services --namespace=kube-system kubernetes-dashboard (dashboard LB service)




## REMOVED FROM HERE: pods


## REMOVED FROM HERE: 07-persistent-data-volumes.md




# TODO Labels and Annotations

## Labels
Labels are simple key-value pairs. 63-char alphanumeric strings. Can contain a slash, before which there must be a domain. e.g.

tutorialinux.com/foobar

Annotations are 'hints' that can be used by other tools.

    kubectl get pods --selector="ver=2"
    kubectl get pods --selector="app=bandicoot,ver=2"
    kubectl get pods --selector="app in (alpaca,bandicoot)"

Is 'canary' set to *anything*?
    kubectl get deployments --selector="canary"

### Available logic
=, !=, in, notin, foobar, !foobar

### Selectors
Selectors are used in manifests:

    --selector="app=alpaca,ver=1"

becomes:

    selector:
      app: alpaca
      ver: 1  



## Annotations TODO minimal coverage -- look into this if you want
Additional metadata. Same format as label keys. More focus on the 'namespace' part (domain before the slash).
e.g.
kubernetes.io/change-cause

Usually higher-level semantic stuff, used for integration with tools, 3rd-party libraries, tracking reasons for doing things, adding other information that you don't want to filter by.

If you want to use it to filter, use a label instead.




## REMOVED FROM HERE: 08-services.md


## REMOVED FROM HERE: 09-replicasets.md




### Scaling

Horizontal pod autoscaling is supported.
(Vertical is coming)

- Cluster autoscaling (actually adding nodes, i.e. compute resources, to a cluster) is supported via 3rd-party tie-ins (depending on your cloud provider).

Here's some horizontal pod autoscaling:

    kubectl autoscale rs kuard --min=2 --max=5 --cpu-percent=80



## DaemonSets

Let you schedule a service across a set of nodes in a cluster (or the whole cluster). This is useful for running sidecar containers, e.g.

- log collectors/streamers
- "one pod per node" use cases

Like everything else in Kubernetes, everything is found/matched via labels.

Use DaemonSets when ONE copy of a pod/service should run on nodes.
Use ReplicaSets when it's irrelevant *where* the copies run.

Use spec:NodeSelectors to limit what nodes you're running on.


## Jobs
One-off tasks (as opposed to eternally-running services). A job creates pods that run until they succeed (i.e. exit with 0)

- Database Migrations
- batch jobs
- maintenance/cleanup tasks
- queue workers

    kubectl run -i oneshot --image=gcr.io/kuar-demo/kuard-amd64:1 --restart=OnFailure \
    -- --keygen-enable --keygen-exit-on-complete --keygen-num-to-gen 10

--restart=OnFailure tells Kubernetes to create a job.
everything after '--' is a command-line arg that's passed to the container binary.

Jobs automatically create a unique label (since there are probably a lot of them, and manual naming would get hard/confusing). Using labels with them might lead to weird outcomes.



## REMOVED FROM HERE: 10-secrets.md and 11-configmaps.md




## Deployments

Rolling out new versions/release of your code.








# DigitalOcean Features

## Node Labels and Addresses
You get stuff for free! https://github.com/digitalocean/digitalocean-cloud-controller-manager/blob/master/docs/controllers/node/examples

get node k8s-worker-02 -o yaml

## Load Balancers
Make a service manifest with spec.type LoadBalancer and DO creates a load balancer for this app! Woohoo!

https://github.com/digitalocean/digitalocean-cloud-controller-manager/blob/master/docs/controllers/services/examples/http-nginx.yml



## Deployments?
kubectl run




## REMOVED FROM HERE: 1xx-real-life-k8s-skills--debugging-commands-files.md



# Or...
##########################
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





#### TODO

# Security?
Are our cluster UI and RESTful resources reachable from the Internet?


# Helm - package management for Kubernetes
https://helm.sh/

