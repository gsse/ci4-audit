# Decoda\Audit
Lightweight object logging for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require decoda/audit`
2. Update the database: `> php spark migrate -all`
3. Set up your models:

```
class JobModel extends Model
{
	use \Decoda\Audit\Traits\AuditTrait;
	protected $afterInsert = ['auditInsert'];
	protected $beforeUpdate = ['auditBeforeUpdate'];
	protected $afterUpdate = ['auditAfterUpdate'];
	protected $afterDelete = ['auditDelete'];
```


## Features

Provides ready-to-use object logging for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require decoda/audit`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate -all`

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Audit.php** to **app/Config/Audit.php** and follow the instructions in the
comments. If no config file is found in **app/Config** the library will use its own.

## Usage

Once the library is included all the resources are ready to go and you just need to
specify which models and events to audit. Use AuditTrait to add support to any models
you would like tracked:

```
class JobModel extends Model
{
	use \Decoda\Audit\Traits\AuditTrait;
```

Then specify which events you want audited by assigning the corresponding audit methods
for those events:

```
	protected $afterInsert = ['auditInsert'];
	protected $afterUpdate = ['auditUpdate'];
	protected $afterDelete = ['auditDelete'];
```

The Audit library will create basic logs of each event in the `audits` table, for example:

```
| id | source | source_id | user_id | event  | summary  |          created_at |
+----+--------+-----------+---------+--------+----------+---------------------+
| 10 | sites  |        27 |       9 | create | 2 rows   | 2019-04-05 15:58:40 |
| 11 | jobs   |        10 |       9 | update | 5 rows   | 2019-04-05 16:01:35 |
````
