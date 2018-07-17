#!/bin/bash
VERSION="1.0.0"
VENDOR_NAME="Swiftgift"
PACKAGE_NAME="Gift"
VENDOR_NAME_=$(echo $VENDOR_NAME | awk '{print tolower($0)}');
PACKAGE_NAME_=$(echo $PACKAGE_NAME | awk '{print tolower($0)}');
FILE_NAME="$VENDOR_NAME_-$PACKAGE_NAME_-$VERSION.zip";
ZIP_TMP_DIR=$(mktemp -d);
DEST_DIR="$ZIP_TMP_DIR/$PACKAGE_NAME";
mkdir $DEST_DIR;
CURRENT_DIR=$(pwd);
find . -type f -not -path './.git*' -not -path './dist/*' -not -path './make-zip.sh' -not -name '*~' -not -name '.*' -exec cp --parents '{}' "$DEST_DIR" ";";
cd $ZIP_TMP_DIR;
zip -r $FILE_NAME "./$PACKAGE_NAME";
cp $FILE_NAME "$CURRENT_DIR/dist/";
cd $CURRENT_DIR;
rm -rf $ZIP_TMP_DIR;
