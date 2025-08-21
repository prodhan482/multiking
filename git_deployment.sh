#!/bin/bash

git add --all && \
git commit -m "$1" && \
git merge ehsan_dev_2 && \
git checkout helloduniya22.com && \
git merge ehsan_dev_2 && \
git checkout ehsan_dev_2 && \
git push origin helloduniya22.com && \
git push origin ehsan_dev_2
