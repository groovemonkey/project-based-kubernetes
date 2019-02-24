videos:

Containers, Pods, Services, Deployments, ReplicaSets, DaemonSets

# Thinking in Kubernetes

Here are the objects we're covering:

1. Pods manage containers
1. ReplicaSets manage Pods
1. Services expose pod processes to the outside world
1. ConfigMaps and Secrets configure Pods
1. Labels are the plumbing that ties everything together
1. Deployments manage the change from one set of containers to another (e.g. for a software release)


# Kubernetes Objects
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






# More Kubectl concepts (not covered in detail here)

## Namespaces
Like folders, used to organize objects
If you don't specify one with --namespace, kubectl uses the 'default' namespace

## Contexts
More permanent namespaces (modifies your kubernetes config file at $HOME/.kube/config)
Useful if you're managing multiple kubernetes clusters

kubectl config set-context my-context --namespace=mystuff
kubectl config use-context my-context

