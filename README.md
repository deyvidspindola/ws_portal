## Running
To build and run the application, clone the repository and run in a terminal
from the same directory where the ```docker-compose-{environment}.yaml``` file
is, where *environment* can be *dev*, *test*, *prod* or whatever:

```shell
$ docker-compose up
```

To stop the application, run from the same directory:

```
$ docker-compose stop
```

## Accessing
After build and run it's possible to test by accessing [http://localhost:8000](http://localhost:8000).

## Structure
Here is how the project is structured:

* **/app**: Application files
* **/resources**: Configuration and stand-alone scripts for tooling

## References
* [The Twelve-Factor App](https://12factor.net)
