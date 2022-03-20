# Saasu Connect

## Overview
This php package allows for a connection to Saasu to make requests on
the Saasu API.

It caters for searches, inserts and updates.

It uses [spatie/guzzle-rate-limiter-middleware](https://github.com/spatie/guzzle-rate-limiter-middleware)
to meet the rate limitation of 1 request per second according to the
[API Limits](https://www.saasu.com/help/api-limits).

## Installing the package
Use composer to install the package.
```bash
composer require aleahy/saasu-connect
```

## Usage
In order to connect to a Saasu file, you need a `username`, `password` and `file ID`.

### Connecting
```php
$client = SaasuAPI::createClient($username, $password);
$connection = new SaasuAPI($client, $fileID);
```
Requests can then be made with the connection.
```php
use Aleahy\SaasuConnect\Entities\Invoice as SaasuInvoice;

$connection->findEntity(SaasuInvoice::class, [
  'AmountOwed' => 490.0
]);
```

### Available Methods
The following methods currently exist:

`findEntity` - Finds the provided entity with the search attributes. Returns a collection of entities.

`insertEntity` - Makes a post request for the given entity with the provided attributes.

`getEntity` - Returns the specific entity with the given id.

`getAllEntities` - Returns all the entities in a single array.
### Available Entities
- Company
- Contact
- Invoice
