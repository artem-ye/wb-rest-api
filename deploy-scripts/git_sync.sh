#!/bin/bash

WORK_DIR="`pwd`"
BACKUP_DIR="$WORK_DIR/.backup"
GIT_ZIP_URL="https://github.com/artem-ye/wb-rest-api/archive/master.zip"

# Parsing archive name and repo name from GIT URL
# For example, Parsing for url: https://github.com/artem-ye/wb-rest-api/archive/master.zip
# Will be:
# ARCH_FILE_NAME: master.zip
# ARCH_REPO_NAME: wb-rest-api-master
ARCH_FILE_NAME="`echo $GIT_ZIP_URL | grep -oE '\w+\.zip$' `" 
ARCH_REPO_NAME="`echo $GIT_ZIP_URL | sed -sE 's/(http.?:\/\/[^\/]+\/)([^\/]+\/)([^\/]+)(\/.*)$/\3/'`" 
ARCH_REPO_NAME="`echo $ARCH_REPO_NAME-$ARCH_FILE_NAME | sed -E 's/\.zip$//'`" 


# -----------------------------------------------------------------
# Aka backup: moving current dir into ./backup sudbir 
# -----------------------------------------------------------------

# Preparing backup dir
if [ ! -d "$BACKUP_DIR" ]; then
	mkdir $BACKUP_DIR  

	if [ $? -ne 0 ]; then
		echo "Unable to create backup dir $BACKUP_DIR"
		exit 1
	fi
else
	rm -rf $BACKUP_DIR/*
fi


# Moving current dir into backup dir
script_name=`basename "$0"`
exit_status=0

for f in ./*; do
	if [ "`basename $f`" = $script_name ]; then 
		echo "Skipping $f"
		continue
	fi

	mv "$f" "$BACKUP_DIR/" || exit_status=1
done

if [ $exit_status -ne 0 ]; then
	echo "Moving to backup dir failed. Git not synced"
	exit 1
fi

# -----------------------------------------------------------------
# Downloading and extracting data from git repo
# -----------------------------------------------------------------

wget -q $GIT_ZIP_URL 

if [ ! -f $ARCH_FILE_NAME ]; then
	echo; echo "File $ARCH_FILE_NAME not found"
	echo "You can restore project current state form $BACKUP_DIR"
	exit 1
fi 

unzip $ARCH_FILE_NAME 
rm $ARCH_FILE_NAME

if [ ! -d $ARCH_REPO_NAME ]; then
	echo; echo "Dir: $ARCH_REPO_NAME not found"
	echo "You can restore project current state form $BACKUP_DIR"
	exit 1
fi

mv $ARCH_REPO_NAME/* $WORK_DIR
rm -rf $ARCH_REPO_NAME

#ARCH_FILE_NAME="` echo $GIT_ZIP_URL | grep -oE '\w+\.zip$' `" 
#ARCH_REPO_NAME="`echo -n $GIT_ZIP_URL | sed -sE 's/(http.?:\/\/[^\/]+\/)([^\/]+\/)([^\/]+)(\/.*)$/\3/'`" 


exit 0
