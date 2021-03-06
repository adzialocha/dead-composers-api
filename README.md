# dead-composers-api

Dead Composers API.

## Requirements

* Apache / nginx etc. Server
* SQL Database
* PHP >= 7.0

## Setup

1. Prepare PHP project

    ```
    git clone git@github.com:adzialocha/dead-composers-api.git
    cd dead-composers-api
    composer install
    ```

2. Initialize MySQL database

    Create a database and import `setup.sql` to create the needed table.

3. Configuration

    Copy the configuration file via `mv config.php.example config.php` and change the settings according to your environment.

## Usage

### Responses

The API is always called via `<your_base_path>/api/`. The results are always formatted like this:

```
{
  "params": {
    "limit": 10,
    "offset": 0,
    "order_by": "public_domain_day",
    "order": "DESC"
  },
  "status": "ok",
  "data": [
    {
      "id": 5486,
      "name": "Roland Verlooven",
      "public_domain_day": "2087-11-01",
      "public_domain_years": 70,
      "birth_day": "1938-03-02",
      "death_day": "2017-11-01",
      "nationality": "be",
      "source_url": "http://www.wikidata.org/entity/Q43214277"
    },
    ...
  ]
}
```

* **params**: holds the current filter parameter of this API query
* **status**: when everything went well, this should contain "ok"
* **data**: the actual list of composers

The composer object holds the following values:

* **id**: Unique ID in database
* **name**: Firstname and lastname of the composer
* **public_domain_day**: the day in `yyyy-mm-dd` format when the composers work enters public domain
* **public_domain_years**: the number of years the composer enters public domain since his day of death
* **birth_day**: Birth day of composer in `YYYY-MM-DD` format
* **death_day**: Day of death of composer in `YYYY-MM-DD` format
* **nationality**: Country code in ISO 3166 format
* **source_url**: URI to where the data is from

### Requests

The following request parameters can be used to filter the results:

#### Pagination

* **limit**: Number of result items (default=10, maximum=10000)
* **offset**: Start from the item with this number (default=0)

#### Order

* **order_by**: Order the results (default=`public_domain_day`)
* **order**: Direction of order, possible values are: `ASC` or `DESC` (default=`ASC`)

Possible `order_by` values are: `name`, `public_domain_day`, `public_domain_years`, `birth_day`, `death_day`, `nationality`, `source_url`.

#### Filter

You can filter results by defining a timeframe via the `id`, `from` and `to` parameters:

* **id**: Get a single record identified via its `id` (default=*unset*)
* **from**: Get public domain dates >= this day in `YYYY-MM-DD` format (default=*current date*)
* **to**: Get public domain dates <= this day in `YYYY-MM-DD` format (default=*all*)

#### Format

The results can be returned in different formats.

* **format**: Response format (default=`json`)

Possible formats are: `json`, `xml`, `ics` (Calendar export).

### Examples

* The first 500 composers in XML format: `api/?offset=0&limit=500&format=xml`
* All composers for the month december 2017: `api/?limit=100&from=2017-12-01&to=2017-12-31`
* Calendar export for getting entries starting from current day: `api/?format=ics&limit=1000`
* Calendar export for only one day: `api/?format=ics&from=2018-05-01&to=2018-05-01`

### Database update

To update the database with current data from Wikidata use the following link: `api/?update=<key>&batch=<index>`. Use the secret key you entered in the `config.php` file to start the import process. This might take some seconds. The update process is split up in batches to prevent timeouts and memory problems, use the `batch` param to decide which batch should be processed (starting from 0).

It is recommended to use this URL to issue an Cron job, calling that link every day or week or similar.
