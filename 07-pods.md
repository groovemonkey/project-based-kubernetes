# Video 6: Pods
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





## Problems with Pods (Why Use ReplicaSets instead?)
- if a node fails after a pod is scheduled, that pod is not rescheduled
- you can't launch multiple running versions of the same pod (you'd have to copy and rename)
- once a pod is scheduled onto a node, it doesn't move


