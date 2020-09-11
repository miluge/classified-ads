# DROOPIST : classified Ads

## Try it
Application is available at droopist.guillaumeperbet.fr

## Context
Application was developped during Access Code School formation, in september 2020, by Guillaume Blondel and Guillaume Perbet
Guillaume B was in charge of front end
Guillaume P was in charge of back end

## Application
Application shows classified ads split in height categories
User can filter ads by category or title, see details and post his own ad
Application allow any user to post ads and to upload a picture
Ads creation, modification, validation and deletion uses email notifications

## Install
Install composer and npm modules
Configure a virtual host to point to public/ directory and allow url rewrite
Import database from dump.sql
Rename parameters.php.scale to parameters.php and fill it with your own constants
Activate Twig cache in application/class/Twig.php
