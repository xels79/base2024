#!/bin/bash
lsc="$HOME/node_modules/less/bin/lessc"
tless="$HOME/programs/base_asterion/web/less/*.css"
ptoweb="$HOME/programs/base_asterion/web"
echo "Компиляция less файлов:"
rm $HOME/programs/base_asterion/web/less/*.css
$lsc $HOME/programs/base_asterion/web/less/bootstrap.less $HOME/programs/base_asterion/web/less/bootstrap.css --no-color -x