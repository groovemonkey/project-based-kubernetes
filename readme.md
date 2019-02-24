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




# TODO VIDEO 4: Kubernetes Objects
- represented as JSON or YAML files
- you create these and then push them to the kubernetes API with kubectl
- (or receive them as output from kubectl after it hits the kubernetes API for you)

Regardless of the type of object you're dealing with, the basic syntax is:

    kubectl apply -f obj.yaml


You can **interactively** edit using

    kubectl edit <resource-name> <obj-name>

This will:
1. download a yaml representation of the object using the kubernetes API
1. launch your $EDITOR on that textfile
1. re-upload your saved changed using the Kubernetes API, which will re-evaluate that object definition and make the necessary changes.



## Delete stuff
If you've got a resource file (manifest) handy:

    kubectl delete -f obj.yaml

or any object in the kubernetes system with:

    kubectl delete <resource-name> <obj-name>



# TODO VIDEO 5 (bunchastuff?): Labels
Lots of stuff (load balancing) is done with labels. Add labels to objects like this:

Add a label to a pod object, overwrite that label value, then delete the label completely:

    kubectl label pods davespod color=red
    kubectl label --overwrite pods davespod color=blue
    kubectl label pods davespod color-


# More Kubectl concepts
## Namespaces
Like folders, used to organize objects
If you don't specify one with --namespace, kubectl uses the 'default' namespace

## Contexts
More permanent namespaces (modifies your kubernetes config file at $HOME/.kube/config)
Useful if you're managing multiple kubernetes clusters

kubectl config set-context my-context --namespace=mystuff
kubectl config use-context my-context


# More advanced kubectl
kubectl get deployments --namespace=kube-system kube-dns
kubectl get deployments --namespace=kube-system kubernetes-dashboard (dashboard)
kubectl get services --namespace=kube-system kubernetes-dashboard (dashboard LB service)







# TODO Video 6: Pods
Pods are the smallest deployable unit in Kubernetes. So you're never deploying 'a container.' You're at the very least deploying a POD that only consists of one container.

Containers that belong together should be grouped into a pod. It's tempting to think of your Kubernetes **nodes** in the way you thought about 'machines' in the past, but that's not correct. **Pods** are the closest analogue to the traditional **machine** concept:

Containers in the same pod get scheduled together on the same node and have access to:

- shared network namespace
- shared hostname (UTS namespace)
- shared IPC namespace
- storage
- the only private thing containers in the same pod have is their own cgroup

The big difference is that different Pods, **EVEN THOSE THAT ARE SCHEDULED ON THE SAME NODE**, are isolated from each other. They don't share a hostname, IP, or IPC namespace.



Important note:

This Pod abstraction gives you something really important: it allows you to stick to the one-process per container philosphy with a minimum of hackery when using Docker and Kubernetes, because it adapts that one-process-per-container model to how real-life applications are architected. Sticking to one process per container will prevent all kinds of nasty problems that will bite you later on.


## Common Pod Mistakes

"Oh cool, my webapp, nginx container, redis service, and postgres all belong in the same pod!"

WRONG! Harshly criticize yourself and repent.

The question is, what components of your application do you need to *scale together*? Which processes absolutely have to be colocated on the same *machine* (shared storage or IPC) to function perfectly?

Here are some of the problems with the mega-pod above:

1. All of these components will need to be scaled separately, and in different ways (a stateless webapp can easily scale horizontally -- run 20 extra copies of it! -- while your database might need to scale vertically -- just give more memory and CPU time to your single copy of the postgres process).


## Practice Your Professional Pod Pushing
I'm going to show you how to manage pods, BUT: ReplicaSets (which we'll cover later) are a better way of actually managing your pods in production.

That said, all the concepts here (pod management, healthchecks, resource management) are building blocks that you need before you can go to more clever ways to deploy your application.

#### Insert pod manifest here
    kubectl apply -f mypod.yaml

    kubectl get pods
    kubectl describe pods mypod

Delete it by name or from a manifest:

    kubectl delete pods/kuard
    kubectl delete -f mypod.yaml

This will let the pod shut down gracefully (default grace period is 30 secs). Container state is NOT saved (unless you're using PersistentVolumes).


## Manual port forwarding on a pod:

    kubectl port-forward kuard 8080:8080


## Health Checks
We want to check if our pod services are healthy. Because processes can get into weird states, we want to make sure all of our containers in a pod are actually behaving normally. You get to define 'normally' in your pod manifest, and then that check will run against every container in your pod:

## Liveness Probe
"Is this container alive?"

### Insert example here, e.g.
apiVersion: v1
kind: Pod
metadata:
  name: kuard
spec:
  containers:
    - image: gcr.io/kuar-demo/kuard-amd64:1
      name: kuard
      livenessProbe:
        httpGet:
          path: /healthy
          port: 8080
        initialDelaySeconds: 5
        timeoutSeconds: 1
        periodSeconds: 10
        failureThreshold: 3
      readinessProbe:
        httpGet:
          path: /ready
          port: 8080
      ports:
        - containerPort: 8080
          name: http
          protocol: TCP


    kubectl apply -f kuard-pod-health.yaml
    kubectl port-forward kuard 8080:8080

### CHANGETHIS
Point your browser to http://localhost:8080. Click the “Liveness Probe” tab. You
should see a table that lists all of the probes that this instance of kuard has received. If
you click the “fail” link on that page, kuard will start to fail health checks.


## Readiness Probe
"Is this container ready to receive traffic?"
Good for containers that have some bootstrapping/initialization to do before they're ready to receive traffic or respond to liveness checks.

## Other check types
- tcpsocket
- exec probes


## Pod Resource Management
As part of your pod definition, you'll want to define the resources you want to dedicate to each of your containers.

Add the following to your pod manifest (for each -containers item):

resources:
  requests:
    cpu: "500m"
    memory: "128Mi"

This requests a minimum amount of resources for each container, so that the scheduler knows how to pack these together on nodes.

Your containers will be *guaranteed* to get a minimum of these resources. If the node isn't filled to capacity, your containers can consume more resources (as much as is free on the node).


If you want to set resource *limits* for a container, you can do it like this:

limits:
  cpu: "1000m"
  memory: "256Mi"




## Persistent Data: Volumes
By default, the filesystems mounted to your containers don't survive container or pod restarts. This is fine for stateless web applications, but not exactly ideal for your database.

You'll define two things in the pod manifest:

1. The volumes that this pod will have available to it.
1. The actual mounts that you want each of your different containers to have.


apiVersion: v1
kind: Pod
metadata:
  name: kuard
  spec:
    volumes:
      - name: "kuard-data"
        hostPath:
          path: "/var/lib/kuard"
    containers:
      - image: gcr.io/kuar-demo/kuard-amd64:1
        name: kuard
        volumeMounts:
          - mountPath: "/data"
          name: "kuard-data"
      ports:
        - containerPort: 8080
        name: http
        protocol: TCP


## Storage volume types
You'll most commonly be using a few types, although many more are available:

- emptydir: a shared, empty directory mounted on all containers.
- cloud-provider-specific network storage (EBS, DigitalOcean's storage which we'll be using)
- hostpath: mounts from the host filesystem into the containers

###### TODO INSERT DIGITALOCEAN STORAGE VOLUME HERE



## Problems with Pods (Why Use ReplicaSets instead?)
- if a node fails after a pod is scheduled, that pod is not rescheduled
- you can't launch multiple running versions of the same pod (you'd have to copy and rename)
- once a pod is scheduled onto a node, it doesn't move







# TODO VIDEO 3
# Labels and Annotations

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








## ReplicaSets

Great, you want to run a pod. But might you want to run *multiple* copies of the same pod?

- redundancy / high availability
- scaling
- sharding (farming out work to multiple nodes)

Keyword: reconciliation loop (Houston, the pinky has left the teacup!)
Fancy word for a thing that *all* infra-as-code systems do -- they look at the current state, compare it to the ideal state, and make necessary changes. Start a container, kill a container, whatever.


ReplicaSets let you do this:
    
    kubectl scale replicasets kuard --replicas=4


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



## ConfigMaps

Configuration data (json objects, really). A bunch of different ways to configure things, crammed into one overloaded concept.

- Environment variables that are visible to your container process
- Command-line arguments that are appended to your container 'command'
- A filesystem that can be mounted in your container (yeeeah...). Keynames become filenames, values become file contents. 

Configmaps are UTF-8 plaintext. They can't hold binary data.

See example manifests in this project.

See your configmaps with:

    kubectl get configmaps



## Secrets
For sensitive data -- API keys, passwords, TLS or other encryption keys, etc.

Secrets can have one or more data elements associated with them (e.g. a TLSKEY secret can consist of both a .key and .crt file).

As of Kubernetes 1.7, this handles your secrets in a reasonable way: secrets are encrypted and only visible on nodes that run pods which *need* those secrets.

Your pods will probably access secrets by using a *secrets volume* (they can also use the API, of course). These are mounted on tmpfs volumes, and will not sync to disk (they're in-memory only). Each data element will be represented by a separate file in the mounted secrets volume.

Example: see the secret manifest in this project.

Secrets *can* store binary data. If you're defining secrets in YAML files, the data needs to be base64-encoded.

    kubectl get secrets


### Viewing a Secret
To see a secret, use this:

    kubectl get secret wp-db-secrets -o yaml

The secret (in the 'data' section of this yaml output) you're seeing here is base64-encoded, so decode with e.g.

    echo 'THESECRETYOUSEE' | base64 --decode

Ignore the '%' that terminates the output -- 'NGiJi6A46YJTjTx%' is actually 'NGiJi6A46YJTjTx'. Hooray! Now you're looking at the actual stored secret.


### Updating a Secret
When you update secrets, the appropriate files and env vars are updated on running containers. It's up to your application or container process to re-read files and env vars as needed (if things fail, for example). Yes, this couples your application to kubernetes in a small but real way.





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


















# Debugging with kubectl

## Logs
In production, you want to be using a log aggregation service for managing container logs. BUT:

Pod logs:

    kubectl logs <pod-name>

Specific container:

    kubectl logs <pod-name> -c containername

Previous run of a container (if container process is failing or in a reboot loop)

    kubectl logs <pod-name> -c containername --previous


## Commands
Launch an interactive bash shell in a pod:

    kubectl exec -it <pod-name> -- bash

The more kubernetes-friendly way of doing this is to create a *job* manifest:
- in the spec, use the container you'd like to execute a one-time task on
- use the 'command' feature and pass a list of command/argument strings


## Files
Copy a file from/to a pod container (if you find yourself copying *to* a container, re-examine the life decisions that have led you to this point):

    kubectl cp <pod-name>:/path/to/remote/file /path/to/local/file





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

