Master Retailer Database, Mobile App
====================================

    composer install
    bin/load-data
    bin/console server:start 0.0.0.0:8000
    


1) Setting up / updating local environment 
----------------------------------

These should run in screen or somewhere:

    while sleep 5; do bin/console fda:export --no-debug; done
    while sleep 5; do bion/console fda:scrape --scrape 50000 --extract 200 --no-debug; done
    bin/console fda:import-raw --limit 0 --no-debug


install Nodejs (http://nodejs.org/download)

The first time, in terminal run:
`sudo npm install -g grunt-cli`  

`gem install compass`

`npm install grunt-git`

`npm install` (needed first time only and after new packages are added to package.json)

after this:

`grunt` (run this after pulling changes from repository)

after pulling/changing `npm install && grunt` is needed, although `grunt` alone should be enough as package.json changes very rarely

Data
====

First, go to http://www.accessdata.fda.gov/scripts/oce/inspections/oce_insp_searching.cfm and download each fiscal year data 
by clicking on *Excel Icon Export Data to Excel by Fiscal Year*

    bin/console fda:load-fiscal-years
    bin/console fda:import-raw 10  --limit 30 --verbose --warnings-only
    bin/console fda:scrape --scrape 9999 --extract 9999 

2) Styles
---------

Sass styles are in `src/Appsources/scss` folder and are compiled with compass in grunt

`grunt styles` command will compile styles and poll for changes (finish with ctrl+c)

----
 


**Overview**

There are several components to this project:


Get All Retailers from warnings
Get All Letters from warnings
Get All Letters with 1140.14e that haven't be tagged with the result of some job
Get All Letters with 1140.14e that have been tagged "Other" with job 1140.14e_violation

**RetailPosse and FDA**

First, get into the command line prompt with an EXISTING database

    psql -U root -hlocalhost posse
    
Then run this sql
   
    create user posse with password 'posse1123';
    grant all on schema posse to posse;
    grant all on schema data to posse;

Adding the fda.conf file to apache.  You may need to
create the database (db_fda) first, then populate it:

    http://www.accessdata.fda.gov/scripts/oce/inspections/oce_insp_searching.cfm

Create the databases and users:

    create database db_fda;
    grant all on db_fda.* to fda@'%' identified by 'fda';
    create database db_survos;
    grant all on db_survos.* to survos@'%' identified by 'surv05';

and set the usernames and passwords in parameters.yml

Click on download Data to Excel and save the file to
/usr/sites/sf/fda/src/Tobacco/FDABundle/Resources/data/fda.csv

    php app/console propel:model:build @TobaccoFDABundle --connection=fda
    php app/console propel:sql:build @TobaccoFDABundle --connection=fda
    php app/console propel:sql:insert @TobaccoFDABundle --force --connection=fda

Now import the data.  This should go into a loaddata.bat file or something.

    app/console fda:import-raw --no-debug --limit 600000 1000000
    php app/console fda:scrape --scrape 9999 --extract 9999 --no-debug
    app/console fda:import-statute  --no-debug
    php app/console fda:import-geocodes  --no-debug

### Access the Application via the Browser

    http://l.fdainspections.info/app_dev.php

### Run the Behat tests

To run all Behat tests:

    php app/console -e=test behat

To run Behat tests for one bundle:

    php app/console -e=test behat @TobaccoFDABundle

The test code lives in the Features package for every bundle
(`src/Tobacco/FDABundle/Features/*.feature`).


## Docker

Development:
```
bin/docker-env dev
docker-compose up -d --build
docker-compose down
```

Push production build:
```
bin/docker-env prod
docker-compose build
docker-compose push
```

Docker swarm (can be used for local development too):
```
docker swarm init [--advertise-addr <ip>]
Swarm initialized: current node (<node_id>) is now a manager.

bin/docker-env prod
docker stack deploy -c docker-compose.yml fda
docker stack rm fda
docker swarm leave --force
```

Troubleshooting:
```
docker stack services fda
docker stack ps fda
```

## Heroku deploy
Install heroku:
```
nvm install v8.6.0
nvm alias default v8.6.0
nvm use default
npm install -g heroku-cli
```
 
Heroku setup:
```
heroku login
heroku plugins:install heroku-container-registry
heroku container:login
cd project
heroku create
```

Build image locally: 
```
cp ./env-example .env
docker-compose up --build -d web
#check if no errors
docker-compose logs
then check the local app http://localhost:8080 
```

Once everything works, build and push image to heroku:
```
heroku container:push web --app fda
heroku ps:scale web=1 #first time only
heroku logs #check if no errors
heroku open 
heroku run bash
```

## docker-machine, example of Scaleway management
Allows you to manage VPS using your local docker client.

```
https://github.com/docker/machine/releases/download/v0.12.2/docker-machine-`uname -s`-`uname -m` >/tmp/docker-machine &&\nchmod +x /tmp/docker-machine &&\nsudo cp /tmp/docker-machine /usr/local/bin/docker-machine
```
Add into your bashrc:
```
export SCALEWAY_TOKEN=<token>
export SCALEWAY_ORGANIZATION=<key>
```

Create new VPS:
```
https://github.com/scaleway/docker-machine-driver-scaleway
curl -sL https://github.com/scaleway/docker-machine-driver-scaleway/releases/download/v1.3/docker-machine-driver-scaleway-linux-amd64 -O
sudo chmod +x ./docker-machine-driver-scaleway-linux-amd64
sudo mv ./docker-machine-driver-scaleway-linux-amd64 /usr/local/bin/docker-machine-driver-scaleway

docker-machine create -d scaleway \
  --scaleway-organization=$SCALEWAY_ORGANIZATION --scaleway-token=$SCALEWAY_TOKEN \
  --scaleway-name="sdk1" --scaleway-debug \
  --scaleway-commercial-type="VC1M" --scaleway-volumes="50G" \
  sdk1
```

Commands: 
```
eval $(docker-machine env sdk1)
eval $(docker-machine env sdk1 -u)
docker-machine ls
docker-machine ssh sdk1
docker-machine ip sdk1
docker-machine status sdk1
docker-machine info sdk1
docker-machine url sdk1
docker-machine config sdk1
docker-machine stop sdk1
docker-machine config sdk1
docker-machine start sdk1
docker-machine rm sdk1
```
