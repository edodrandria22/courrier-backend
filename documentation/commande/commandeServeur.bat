#Pour installer git 
sudo apt update
sudo apt install git -y

#Pour creer une nouvelle dossier
mkdir mesupres

#Pour entrer dans le dossier 
cd mesupres

#Pour installer nvm

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

#Pour recharger la configuration 
source ~/.bashrc


#Pour installer node
nvm install 24.12.0

#Pour ajouter depot php 
sudo apt update
sudo apt install software-properties-common ca-certificates lsb-release apt-transport-https -y

sudo add-apt-repository ppa:ondrej/php

#Pour installer php 8.2
sudo apt install php8.2 -y  

#Pour ajouter le depot de postgres
sudo apt update
sudo apt install curl ca-certificates gnupg -y

#ajouter la cle du depot
sudo install -d /usr/share/postgresql-common/pgdg

sudo curl -o /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc https://www.postgresql.org/media/keys/ACCC4CF8.asc

#Pour installer postgres
sudo apt update
sudo apt install postgresql-16 postgresql-client-16 -y


#Pour se connecter a postgres

sudo -u postgres psql

#Pour creer une utilisateur

CREATE USER mesupres WITH PASSWORD 'mesupres';


#Pour installer amqp
sudo apt update
sudo apt install php8.2-amqp -y

#Pour telecharger mercure
cd /tmp
wget https://github.com/dunglas/mercure/releases/download/v0.24.2/mercure_Linux_x86_64.tar.gz

#Pour le decrompresser
tar -xzf mercure_Linux_x86_64.tar.gz

#Pour installer le binaire
sudo mv mercure /usr/local/bin/mercure

#Pour donner de permition a cette dossier 
sudo chmod +x /usr/local/bin/mercure


#Pour installer rabbitMq
sudo apt-get update
sudo apt-get install curl gnupg apt-transport-https -y

#Ajouter la clé RabbitMQ
curl -1sLf "https://keys.openpgp.org/vks/v1/by-fingerprint/0A9AF2115F4687BD29803A206B73A36E6026DFCA" | sudo gpg --dearmor | sudo tee /usr/share/keyrings/com.rabbitmq.team.gpg > /dev/null

#Pour ajouter le depot de rabbitMq
sudo tee /etc/apt/sources.list.d/rabbitmq.list <<EOF
deb [arch=amd64 signed-by=/usr/share/keyrings/com.rabbitmq.team.gpg] https://deb1.rabbitmq.com/rabbitmq-erlang/ubuntu/noble noble main
deb [arch=amd64 signed-by=/usr/share/keyrings/com.rabbitmq.team.gpg] https://deb2.rabbitmq.com/rabbitmq-erlang/ubuntu/noble noble main

deb [arch=amd64 signed-by=/usr/share/keyrings/com.rabbitmq.team.gpg] https://deb1.rabbitmq.com/rabbitmq-server/ubuntu/noble noble main
deb [arch=amd64 signed-by=/usr/share/keyrings/com.rabbitmq.team.gpg] https://deb2.rabbitmq.com/rabbitmq-server/ubuntu/noble noble main
EOF


sudo apt-get update

#Pour installer erlang
sudo apt-get install -y erlang-base \
erlang-asn1 \
erlang-crypto \
erlang-eldap \
erlang-ftp \
erlang-inets \
erlang-mnesia \
erlang-os-mon \
erlang-parsetools \
erlang-public-key \
erlang-runtime-tools \
erlang-snmp \
erlang-ssl \
erlang-syntax-tools \
erlang-tftp \
erlang-tools \
erlang-xmerl

#Pour installer rabbitMq
sudo apt-get install rabbitmq-server -y

#Pour activer rabbitMq au demarage
sudo systemctl enable rabbitmq-server
sudo systemctl start rabbitmq-server

#Pour activer l'interface web de rabbitMq
sudo rabbitmq-plugins enable rabbitmq_management



#Pour creer un utilisateur
sudo rabbitmqctl add_user courrier mesupres

#Pour donner une permition a cette utilisateur
sudo rabbitmqctl set_permissions -p / courrier ".*" ".*" ".*"


#Pour donner une administration a l'interface web de rabbitMq
sudo rabbitmqctl set_user_tags courrier administrator

#Pour installer pm2 -- le lancement du serveur sans arret 
sudo npm install -g pm2



#Pour creer le serveur
npm run build
pm2 start npm --name "courrier-front" -- start -- --hostname 0.0.0.0
pm2 start "php -S 0.0.0.0:8000 -t public" --name courrier-backend
pm2 start "php bin/console messenger:consume async -vv" --name "symfony-messenger"

#Pour lancer le serveur 
pm2 start courrier-front
pm2 start courrier-backend