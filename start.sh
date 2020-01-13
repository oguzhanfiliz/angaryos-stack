apt-get update
apt-get install -y docker.io git wget
docker swarm init
git clone https://github.com/karapazar/AngaryosLight
cd AngaryosLight/
docker stack deploy --compose-file ./docker-stack.yml angaryos
