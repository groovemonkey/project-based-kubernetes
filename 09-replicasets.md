## ReplicaSets

Great, you want to run a pod. But might you want to run *multiple* copies of the same pod?

- redundancy / high availability
- scaling
- sharding (farming out work to multiple nodes)

Keyword: reconciliation loop (Houston, the pinky has left the teacup!)
Fancy word for a thing that *all* infra-as-code systems do -- they look at the current state, compare it to the ideal state, and make necessary changes. Start a container, kill a container, whatever.


ReplicaSets let you do this:
    
    kubectl scale replicasets kuard --replicas=4
