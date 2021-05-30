apt-get update
apt-get install -y docker.io git

docker swarm init

git clone https://github.com/MikroGovernment/angaryos-stack.git

cd angaryos-stack/

chmod 755 -R ./frontend/
chmod 777 -R ./backend/storage/
chmod 777 -R ./backend/public/
chmod 777 -R ./backend/bootstrap/cache/
chmod 777 -R ./services/
chmod 600 ./services/postgresql/.pgpass

read -p "Enter new host address (ex: 192.168.3.50, bulut.omersavas.com): "  hostAdress

declare -a arr=("./backend/.env" "./frontend/src/environments/environment.prod.ts" "./frontend/src/environments/environment.ts")
for i in "${arr[@]}"
do
   echo 'replace: '"$i"
   sed 's/192.168.10.185/'"$hostAdress"'/g' "$i" > ./temp.txt && mv temp.txt "$i"
done

docker stack deploy --compose-file ./docker-stack.yml angaryos

echo 'served at https://'"$hostAdress"'/'