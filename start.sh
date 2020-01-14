apt-get update
apt-get install -y docker.io git
docker swarm init
git clone https://github.com/karapazar/Angaryos
cd Angaryos/
chmod 777 -R frontend/
chmod 777 -R backend/storage/
chmod 777 -R backend/bootstrap/cache/
chmod 0700 -R services/postgresql

docker stack deploy --compose-file ./docker-stack.yml angaryos
