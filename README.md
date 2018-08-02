ypf_demo
===

using
===

Test PHP Mode
````
#php -S 7000 example.php
````

Test Swoole Mode
````
#php swoole.php
````

open  http://127.0.0.1:7000

Test Swoole Worker Mode
````
#php swoole_worker.php
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
