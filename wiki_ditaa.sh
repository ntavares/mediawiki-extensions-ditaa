#!/bin/bash
#
# wiki_ditaa.sh - wrapper script for ditaa0_9.jar
#
# Place this script together with ditaa0_9.jar
#
JARPATH=$(dirname $0)

#
# usage: java -jar ditaa.jar <inpfile> [outfile] [-A] [-d] [-E] [-e
#       <ENCODING>] [-h] [--help] [-o] [-r] [-s <SCALE>] [-S] [-t <TABS>]
#       [-v]
#
java -jar $JARPATH/ditaa0_9.jar $1 $2 --verbose --scale 0.8
