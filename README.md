
## Synopsis

OnewirePoller.php is a script that polls thru OWFS (1-Wire File System) and prints temperature values of 18B20 sensors as well as state of latches. 

## Motivation

This script borned in Raspberry pi where I am running OWFS where I have large amount of 1-Wire sensors. This script makes it easier to see when new sensors comes up and from existing you can see clear output. This script is able also to store results in txt files as well as send those via json.

## Installation

set following in script

DEFINE ("LOG2JSON",1); # Do you want to upload results with json

DEFINE ("UPDATE",1); # Do you want to update json/txt endpoints at all?

DEFINE ("LOGDIR","/var/www/logger/"); # Where results are saved (txt file))

DEFINE ("LOGURL","<json url>"); # Where results are pushed (json endpoint)

DEFINE ("OWDIR","/media/1-wire/"); # OWFS location

DEFINE ("DEBUG",0); # Do you want script to make any output

following array descriptes your sensors and types. if sensor is not listed, you will get prompt about it. You have to add address to array if you want any output from that sensor. 

$devices['28.FFEC65760400'] = array('sensor'=>array('temperature'),'description'=>'livingroom temperature','tags'=>array('livingroom','temperature'));

$devices['12.2E30B7000000'] = array('sensor'=>array('latch.B'),'description'=>'livingroom','tags'=>array('livingroom','door'));

## Usage

pi@pi ~/git/onewirePoller $ php onewirePoller.php
* [warn] Not known : 81.566643000000 *
* [warn] Not known : 10.0ED2A3020800 *
* [ ok ] 12.2E30B7600000 : latch.B     = 0     (door1) : nochange
* [ ok ] 12.372EB7670000 : latch.B     = 0     (door2) : nochange
* [ ok ] 12.892EB6760000 : latch.B     = 0     (door3) : nochange
* [ ok ] 12.A82DB6435000 : latch.B     = 0     (door4) : nochange
* [ ok ] 12.BC37B6045000 : latch.B     = 0     (door5) : nochange
* [ ok ] 26.139121034000 : temperature = 6.66  (Closet) :
* [ ok ] 26.139123410000 : humidity    = 58.43 (Closet) :
* [ ok ] 28.1EF453550000 : temperature = 11.56 (Room1) :
* [ ok ] 28.58EF55650000 : temperature = 11.69 (Room2) :
* [ ok ] 28.785316560000 : temperature = 3.38  (Room3) :
* [ ok ] 28.65E652350000 : temperature = 10.63 (Room4) :
* [ ok ] 28.799CF6023400 : temperature = 8.44  (Room5) :
* [ ok ] 28.FF265A234400 : temperature = 9.25  (Room6) :
* [ ok ] 28.FF4E23460400 : temperature = 7.94  (Room7) :
* [ ok ] 28.FF5F34530400 : temperature = 7.94  (Room8) :
* [ ok ] 28.FF7244440400 : temperature = 7.19  (Room9) :
* [ ok ] 28.FF8073450400 : temperature = 1.63  (Room10) :
* [ ok ] 28.FF8273450400 : temperature = -0.31 (Room11) :
* [ ok ] 28.FF897B240400 : temperature = 1.56  (Room12) :
* [ ok ] 28.FF94133D0400 : temperature = 20.56 (Room13) :
* [ ok ] 28.FFBA23460400 : temperature = 8.06  (Room14) :
* [ ok ] 28.FFD0323C0400 : temperature = 19.69 (Room15) :
* [ ok ] 28.FFEC34560400 : temperature = 6.81  (Room16) :
