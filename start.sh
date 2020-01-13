apt-get update
apt-get install -y docker.io git
docker swarm init
git clone https://github.com/karapazar/Angaryos
cd Angaryos/
chmod 777 -R backend/storage/
chmod 777 -R backend/bootstrap/cache/

docker stack deploy --compose-file ./docker-stack.yml angaryos
