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
