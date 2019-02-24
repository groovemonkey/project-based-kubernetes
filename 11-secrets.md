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
