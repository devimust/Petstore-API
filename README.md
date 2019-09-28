# Petstore API

This repository exist as a result of a challenge to create a Petstore API based on [Swagger Petstore](https://petstore.swagger.io/).

Three full end-points exist:

```
POST {api-base}/v1/pet
GET {api-base}/v1/pet/{id}
DELETE {api-base}/v1/pet/{id}
```


## Local development

Ensure you have php 7.1 and MySQL 5.7 installed locally (including any Laravel 5.8 dependencies). Ensure you have an empty database schema setup in MySQL and the correct database connection details are configured in `.env`.

Checkout this respository and run the following commands to get this API running locally for development purposes:

```
cp .env.example .env
# setup database connection details in .env
composer install
php artisan serve
php artisan key:generate
php artisan migrate:fresh
# access the url via http://localhost:8000/v1/pet
```


## Testing

An example Postman collection has been added to the repository here `./Petstore-API.postman_collection.json`.


## Production build workflow

### Assumptions

This API is running in production on AWS ECS (elastic container service) behind an AWS API Gateway. The API is connected to a relational database hosted in AWS RDS Aurora. The aim is to perform continuous integration (CI) / continuous delivery (CD) on the Petstore API.

```
[ Client Application ] -> [ AWS API GATEWAY ] -> [ AWS EKS: Petstore API ] -> [ AWS RDS Aurora ]
```

The company has a Github repo (production is based on the `master` branch), Travis CI build server, Jenkins deploy tool and Elasticsearch/Kibana integration for reporting/monitoring.

### Development to Production workflow

A developer will checkout a new feature branch e.g. `feature/ABC-123` from `master`. Changes will be committed to the feature branch. Once development is done, a pull request (PR) will be created and the code will be reviewed. As part of the PR process a job is executed on Travis CI to run unit tests and perform code linting checks to ensure consistent code styles are followed. The PR can only be merge-able when developers have approved the PR and the build step completed successfully.

Once the PR is merged a job is executed on Travis CI to build a new version of the Petstore API. This is done via a docker image. Travis CI will update the production environment file (`.env`) with production sensitive settings (e.g. database settings, memcached location, etc..) and run a `composer install`. Once complete it will build the new docker image by using the `./Dockerfile`. The new image is version-tagged and pushed to the AWS ECR docker registry.

At this point it is up to the EKS deployment process to reference the new docker image by using a blue/green deployment approach and draining old sessions over to the new Petstore API version. The EKS cluster will be responsible for scaling under load and for health purposes.

Steps can be retro-fitted to support a staging/test or on-demand environments (e.g. environment per feature branch).


## Other

A few things I'd like to mention:

* The second commit was very big. In a real world application I'd expect many commits to reflect tweaks to code or new features.
* This API only supports JSON and not XML.
* This API does not care about Authentication. In a real microservices world the Auth process could be managed by an auth-based microservice to set a session in the client application. This could then be passed into this API in some form of authentication token / identification data.
* No unit tests were written given the time constraints.
* I chose Laravel as I've been told the company makes use of it. I'm aware there are many short-hand techniques that could be used to make the code more readable and cleaner. I've gone for a technique that I was sure would work in the specified time available.
* There might be some validation errors when not sending the full payload as per the Swagger Petstore examples.
* An error response returns the correct http response code (e.g. 400, 404, 405) but the response body is in plain text which is in contradiction with the Swagger ApiResponse contract. It was a bit confusing given the response body from the demo 'Try it out' feature on petstore.swagger.io.
* I ran out of time to do foreign-key database logic on the Tags data models. It is however implemented for the Categories model.
* No caching considerations were taken. Tools like memcached, varnish/Apache mod_cache and OPcache could be applied to speed up performance of the responses. For more content heavy API requests ETags could also be implemented to return 304 (not modified) responses where needed.
* The Dockerfile references `php artisan serve` in the default `CMD`. A recommended way to run an application like this would be via supervisord or some robust wrapper inside the docker container or via a separated webserver like NGINX or Apache.
