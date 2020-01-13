apt-get update
apt-get install -y docker.io git
docker swarm init
git clone https://github.com/karapazar/Angaryos
cd Angaryos/
docker stack deploy --compose-file ./docker-stack.yml angaryos
