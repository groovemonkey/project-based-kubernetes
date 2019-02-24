## ConfigMaps

Configuration data (json objects, really). A bunch of different ways to configure things, crammed into one overloaded concept.

- Environment variables that are visible to your container process
- Command-line arguments that are appended to your container 'command'
- A filesystem that can be mounted in your container (yeeeah...). Keynames become filenames, values become file contents. 

Configmaps are UTF-8 plaintext. They can't hold binary data.

See example manifests in this project.

See your configmaps with:

    kubectl get configmaps
