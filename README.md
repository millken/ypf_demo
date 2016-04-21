ypf_demo
===

using
==
````
[millken@localhost ypf_demo]$ ./swoole_server start
server starting ...
starting worker : test        [ OK ]
starting worker : test2        [ OK ]
starting cron worker     [ OK ]

````

performance
===

````
$wrk -t 20 -c 5000 -d 10s "http://127.0.0.1:9002/"
Running 10s test @ http://127.0.0.1:9002/
  20 threads and 5000 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    39.45ms   11.18ms 152.10ms   78.37%
    Req/Sec     3.68k     1.98k   10.74k    59.34%
  736879 requests in 10.09s, 170.06MB read
Requests/sec:  73052.02
Transfer/sec:     16.86MB
````
