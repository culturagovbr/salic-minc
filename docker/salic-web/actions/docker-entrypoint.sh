#!/bin/bash
set -e
echo "[ ****************** ] Starting Endpoint of Application"
if ! [ -d "/var/www/salic/vendor" ]; then
    echo "Application not found in /var/www/salic/vendor - Installing composer dependencies now..."
    if [ "$(ls -A /var/www/salic/vendor)" ]; then
        echo "WARNING: /var/www/salic/vendor is not empty - press Ctrl+C now if this is an error!"
        ( set -x; ls -A; sleep 5 )
    fi

    cd /var/www/salic

    echo "[ ****************** ] Installing composer dependencies."
    composer install --prefer-source --no-interaction
fi

if [ "$UPDATE_COMPOSER_DEPENDENCIES" == "true" ]; then
	echo "[ ****************** ] Updating composer dependencies."
	cd /var/www/salic
    composer update --prefer-source --no-interaction
fi

if  ! [ -e "/var/www/salic/application/configs/application.ini" ] ; then
    echo "[ ****************** ] Copying sample application configuration to real one"
    cp /var/www/salic/application/configs/exemplo-application.ini /var/www/salic/application/configs/application.ini

    sed -i "s/@@DB_HOST@@/$DB_HOST/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DB_NAME@@/$DB_NAME/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DB_USERNAME@@/$DB_USERNAME/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DB_PASSWORD@@/$DB_PASSWORD/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DB_PORT@@/$DB_PORT/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DB_PDOTYPE@@/$DB_PDOTYPE/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@RECEITA_URL@@/$RECEITA_URL/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@RECEITA_USER@@/$RECEITA_USER/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@RECEITA_PASSWORD@@/$RECEITA_PASSWORD/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_DEFAULT@@/$EMAIL_DEFAULT/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_TRANSPORT_HOST@@/$EMAIL_TRANSPORT_HOST/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_TRANSPORT_TYPE@@/$EMAIL_TRANSPORT_TYPE/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_TRANSPORT_AUTH@@/$EMAIL_TRANSPORT_AUTH/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_TRANSPORT_USERNAME@@/$EMAIL_TRANSPORT_USERNAME/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@EMAIL_TRANSPORT_PASSWORD@@/$EMAIL_TRANSPORT_PASSWORD/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@MOBILE_SALICMOBILEHASH@@/$MOBILE_SALICMOBILEHASH/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@MOBILE_ENCRYPTHASH@@/$MOBILE_ENCRYPTHASH/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@MOBILE_GCMAPIKEY@@/$MOBILE_GCMAPIKEY/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@MOBILE_GCMURL@@/$MOBILE_GCMURL/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@SENTRY_URL@@/$SENTRY_URL/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@AVALIACAO_PROPOSTA_HASH@@/$AVALIACAO_PROPOSTA_HASH/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@URL_BASE@@/$URL_BASE/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DISPLAY_ERRORS@@/$DISPLAY_ERRORS/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@DISPLAY_STARTUP_ERRORS@@/$DISPLAY_STARTUP_ERRORS/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@JWT_TOKEN@@/$JWT_TOKEN/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@HOST_NFE@@/$HOST_NFE/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@TEST_LOGIN@@/$TEST_LOGIN/g" /var/www/salic/application/configs/application.ini
    sed -i "s/@@TEST_PASSWORD@@/$TEST_PASSWORD/g" /var/www/salic/application/configs/application.ini
fi

# X-Debug
if [ "$XDEBUG_INSTALL" == "true" ]; then
    echo "[ ****************** ] Starting install of XDebug and dependencies."
	pecl shell-test xdebug && echo "Package xdebug Installed" || (
		yes | pecl install xdebug
		echo "zend_extension="`find /usr/local/lib/php/extensions/ -iname 'xdebug.so'` > $XDEBUGINI_PATH
    	echo "xdebug.remote_enable=$XDEBUG_REMOTE_ENABLE" >> $XDEBUGINI_PATH

    	if ! [ -d "$XDEBUG_PROFILER_OUTPUT_DIR" ] ; then
    		mkdir -p $XDEBUG_PROFILER_OUTPUT_DIR
    	fi
    	if ! [ -v $XDEBUG_REMOTE_AUTOSTART ] ; then
        	echo "xdebug.remote_autostart=$XDEBUG_REMOTE_AUTOSTART" >> $XDEBUGINI_PATH
    	fi
    	if ! [ -v $XDEBUG_REMOTE_CONNECT_BACK ] ; then
        	echo "xdebug.remote_connect_back=$XDEBUG_REMOTE_CONNECT_BACK" >> $XDEBUGINI_PATH
    	fi
		if ! [ -v $XDEBUG_REMOTE_HANDLER ] ; then
        	echo "xdebug.remote_handler=$XDEBUG_REMOTE_HANDLER" >> $XDEBUGINI_PATH
    	fi
		if ! [ -v $XDEBUG_PROFILER_ENABLE ] ; then
        	echo "xdebug.profiler_enable=$XDEBUG_PROFILER_ENABLE" >> $XDEBUGINI_PATH
    	fi
    	if ! [ -v $XDEBUG_PROFILER_OUTPUT_DIR ] ; then
    		echo "xdebug.profiler_output_dir=$XDEBUG_PROFILER_OUTPUT_DIR" >> $XDEBUGINI_PATH
    	fi
    	if ! [ -v $XDEBUG_REMOTE_PORT ] ; then
    		echo "xdebug.remote_port=$XDEBUG_REMOTE_PORT" >> $XDEBUGINI_PATH
    	fi
    	if ! [ -v $XDEBUG_REMOTE_HOST ] ; then
    		echo "xdebug.remote_host=$XDEBUG_REMOTE_HOST" >> $XDEBUGINI_PATH
    	fi

    	if ! [ -v $XDEBUG_DEFAULT_ENABLE ] ; then
            echo "xdebug.default_enable=$XDEBUG_DEFAULT_ENABLE" >> $XDEBUGINI_PATH
        fi
        if ! [ -v $XDEBUG_IDEKEY ] ; then
            echo "xdebug.idekey=$XDEBUG_IDEKEY" >> $XDEBUGINI_PATH
        fi

    	echo "xdebug.remote_host="`/sbin/ip route|awk '/default/ { print $3 }'` >> $XDEBUGINI_PATH
    	#echo "xdebug.remote_host="`hostname -I` >> $XDEBUGINI_PATH
    	#echo "xdebug.remote_host=localhost" >> $XDEBUGINI_PATH

		echo "[ ****************** ] Ending install of XDebug and dependencies."
	)
fi

echo "[ ****************** ] Ending Endpoint of Application"
exec "$@"
