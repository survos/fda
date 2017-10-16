MYSQL_LOOPS="60"
i=0
while ! nc $DATABASE_HOST $DATABASE_PORT >/dev/null 2>&1 < /dev/null; do
  i=`expr $i + 1`
  if [ $i -ge $MYSQL_LOOPS ]; then
    echo "$(date) - ${DATABASE_HOST}:${DATABASE_PORT} still not reachable, giving up"
    exit 1
  fi
  echo "$(date) - waiting for ${DATABASE_HOST}:${DATABASE_PORT}..."
  sleep 1
done
