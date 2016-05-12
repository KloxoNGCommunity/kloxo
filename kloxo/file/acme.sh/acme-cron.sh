#!/usr/bin/env bash

/root/.acme.sh/acme.sh --cron --home "/root/.acme.sh" > /dev/null
/root/.acme.sh/acme-pem.sh > /dev/null