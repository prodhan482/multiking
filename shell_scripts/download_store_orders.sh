#!/bin/bash

while true
do
    /usr/bin/php /home/admin/web/wmconnects.com/public_html/artisan DownloadWalmartOrderSingleStore

    echo "_______________________________________________________________"
    echo "Loop End. Take Some Rest....."

    sleep 1000
done
