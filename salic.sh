#!/bin/sh

case $1 in
    "start")
        echo "docker-compose up -d"
        docker-compose up -d
        ;;
    "update")
        docker run --rm -v $(pwd):/app composer/composer update -vvv
        docker run -t --rm --name salic-front-update -v "$PWD":/home/node/app -w /home/node/app node:10 sh -c "npm install --silent && npm run update --silent "
        ;;
    "sh")
        docker exec -it salic-web bash
        ;;
    "check")
        which docker

        if [ $? -eq 0 ]
        then
            docker --version | grep "Docker version"
            if [ $? -eq 0 ]
            then
                echo "docker ..OK"
            else
                echo "install docker"
            fi
        else
            echo "install docker" >&2
        fi
        ;;
    *) echo "salic.sh start | update | sh | check";;
esac
