#!/bin/sh
set -eu

mkdir -p /app/public/build
cp -R /opt/rodagest-build/. /app/public/build/

exec "$@"
