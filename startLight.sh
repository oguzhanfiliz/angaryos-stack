apt-get update
apt-get install -y docker.io git

docker swarm init

git clone https://github.com/karapazar/Angaryos

cd Angaryos/

chmod 755 -R frontend/
chmod 777 -R backend/storage/
chmod 777 -R backend/public/
chmod 777 -R backend/bootstrap/cache/
chmod 777 -R services/

sysctl vm.swappiness=0,
sysctl vm.overcommit_memory=1

docker stack deploy --compose-file ./docker-stack-light.yml angaryos
