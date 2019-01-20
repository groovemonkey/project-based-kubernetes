# DigitalOcean Kubernetes Setup

## Clone the DO CCM
    git clone https://github.com/digitalocean/digitalocean-cloud-controller-manager.git
    cd digitalocean-cloud-controller-manager

## Create a cluster?
1.13.1-do.2
name: tl-testcluster

## Download config file (bottom of DO screen) (DOES MOVING IT TO 'config' OBVIATE ALL THE --kubeconfig stuff??)
    mv ~/Downloads/tl-testcluster-kubeconfig.yaml ~/.kube/config

## Ensure cluster version and kubectl version are within 1 minor version of each other
    kubectl version

### Test it
    kubectl get nodes

## Cut a new API token
- https://cloud.digitalocean.com/settings/api/tokens (k8s-tutorial)

# Set up authentication
    export DIGITALOCEAN_ACCESS_TOKEN=your_DO_auth_token_here
    cp releases/secret.yml.tmpl releases/secret.yml
    vim releases/secret.yml # add your token

## Create the secret
    kubectl apply -f releases/secret.yml

### Test it
    kubectl -n kube-system get secrets

### Apply one of the example deployments to get the DO CCM running
    kubectl apply -f releases/v0.1.8.yml

## Go to the project directory that you want to work with
    cd projects/wordpress
    # Follow the instructions in the Project-Instructions.md file!


##########################

# Check the dashboard (does this work?)
kubectl proxy
- starts up a server running on localhost:8001
- visit http://localhost:8001/ui


