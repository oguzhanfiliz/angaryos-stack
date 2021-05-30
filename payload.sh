read -p "Enter new host address (ex: 192.168.3.50, bulut.omersavas.com): "  hostAdress

declare -a arr=("./backend/.env" "./frontend/src/environments/environment.prod.ts" "./frontend/src/environments/environment.ts")
for i in "${arr[@]}"
do
   echo 'replace: '"$i"
   sed 's/192.168.10.185/'"$hostAdress"'/g' "$i" > ./temp.txt && mv temp.txt "$i"
done

echo 'served at https://'"$hostAdress"'/'