# DigitalOcean Kubernetes Setup

## Clone the DO CCM
    git clone https://github.com/digitalocean/digitalocean-cloud-controller-manager.git
    cd digitalocean-cloud-controller-manager

## Create a cluster?
1.16.2-do.3
name: tl-testcluster

## Download config file (bottom of DO screen) (DOES MOVING IT TO 'config' OBVIATE ALL THE --kubeconfig stuff??)
    mv ~/Downloads/tl-testcluster-kubeconfig.yaml ~/.kube/config

## Ensure cluster version and kubectl version are within 1 minor version of each other
    kubectl version

### Test it
    kubectl get nodes

## Cut a new API token
- https://cloud.digitalocean.com/account/api/tokens (k8s-tutorial)

# Set up authentication
    export DIGITALOCEAN_ACCESS_TOKEN=your_DO_auth_token_here
    cp releases/secret.yml.tmpl releases/secret.yml
    vim releases/secret.yml # add your token

## Create the secret
    kubectl apply -f releases/secret.yml

### Test it
    kubectl -n kube-system get secrets

### Apply one of the example deployments to get the DO CCM running
    kubectl apply -f releases/v0.1.21.yml

## Go to the project directory that you want to work with
    cd ~/projects/wordpress
    # Follow the instructions in the Project-Instructions.md file!
