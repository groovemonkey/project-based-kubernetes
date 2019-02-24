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

