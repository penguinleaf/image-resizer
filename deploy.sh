#!/bin/bash
export PROJECT_NAME=${PWD##*/}

echo "Compressing..."
git archive --output=$PROJECT_NAME.tar.gz HEAD web

echo "Uploading..."
scp -c blowfish -q $PROJECT_NAME.tar.gz pglf-prod:~
rm $PROJECT_NAME.tar.gz

echo "Deploying..."
ssh pglf-prod -q << EOF
sudo rm -r /var/www/$PROJECT_NAME/*
sudo tar --strip-components=1 -zxf $PROJECT_NAME.tar.gz -C /var/www/$PROJECT_NAME
rm ~/$PROJECT_NAME.tar.gz
EOF


