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
    Latency    46.93ms    6.13ms 295.43ms   90.88%
    Req/Sec     3.15k     1.29k   13.34k    70.07%
  628191 requests in 10.09s, 125.21MB read
Requests/sec:  62269.09
Transfer/sec:     12.41MB
````
