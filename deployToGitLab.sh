#! /bin/bash

#
# vtk.sh
# @author Pieter Maene <pieter.maene@vtk.be>
#

pushd > /dev/null

cd LITUS_PATH

git checkout vtk-master
git pull --rebase
git merge --no-edit master
git push vtk vtk-master:master

git checkout master
## HEEL BELANGRIJK OM TERUG NAAR MASTER TE CEHCKOUTEN!!!!! NIET VERGETEN

popd > /dev/null