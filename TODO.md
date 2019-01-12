TODO

- Once working, add shared storage for multiple pods from a RWO volume (like DO supports): https://github.com/kubernetes-incubator/external-storage/tree/master/nfs
    - on wordpress project
    - on nextcloud project


- Add mattermost project

- Add mumble project


Wordpress:
- database seeding alternatives (show?):
    - via configmap with included password: https://stackoverflow.com/questions/45681780/how-to-initialize-mysql-container-when-created-on-kubernetes
    - via a command passed to the container on startup
    - via a startup script mounted here: /docker-entrypoint-initdb.d

- add livenessProbe for wordpress deployment
- would this work in the wordpress deployment? (needs new cluster/volume to test)
    - volumeMounts:
        - name: wordpress-data
            mountPath: /var/www/html
        - name: wordpress-config
            mountPath: /var/www/html/wp-config.php
            subPath: wp-config.php


## My feelings about kubernetes
- most apps are not built for this, and will require horrible kludges to get into kubernetes.
- the problem is applications that mix code, config, and application state on a filesystem (i.e. the main way of designing software for the past 50 years).
- the usability of configmaps and secrets is *awful* -- really, really terrible. It doesn't really support how people have been doing configuration/secrets over the last 20 years (env files, etc.), without offering something that's meaningfully better. Kubernetes suffers from really awkward semantics around mounting and accessing this data. A constant frustration.

Bugs:
    -updated image in a deployment doesn't get picked up with apply -f -- you need to delete the deployment and *then* apply the manifest again
    -deadlock when you have 2 replicas try to mount one RWO volume; the second pod never gets created.



