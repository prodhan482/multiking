#!/bin/bash

git add --all && \
git commit -m "$1" && \
git merge ehsan_dev_2 && \
git checkout kingmulti.net && \
git merge ehsan_dev_2 && \
git checkout ehsan_dev_2 && \
git push origin kingmulti.net && \
git push origin ehsan_dev_2
