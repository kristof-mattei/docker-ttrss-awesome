# step 1: pull sources
git pull

# step 2: move config
cp config.php-dist config.php

# step 3: patch url
sed -i -e "/'SELF_URL_PATH'/s/ '.*'/ 'http:\/\/localhost\/'/" config.php

# apply DB settings
sed -i -e "/'DB_TYPE'/s/ '.*'/ 'postgres'/" config.php
sed -i -e "/'DB_HOST'/s/ '.*'/ 'TODO'/" config.php
sed -i -e "/'DB_USER'/s/ '.*'/ 'TODO'/" config.php
sed -i -e "/'DB_NAME'/s/ '.*'/ 'TODO'/" config.php
sed -i -e "/'DB_PASS'/s/ '.*'/ 'TODO'/" config.php
sed -i -e "/'DB_PORT'/s/ '.*'/ 'TODO'/" config.php

# apply schema, if needed
/usr/bin/php /srv/apply-schema.php
