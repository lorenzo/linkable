# CakePHP Linkable Behavior #

Linkable behavior is a companion for the CakePHP built-in containable behavior. It helps fill the gaps that are
not covered by containable: You will be able to contain association that are not directly associated with your model
and to generate queries joining all specified models.

This is particularly useful when you want to filter results by conditions in a hasMany or hasAndBelongsToMany relationship.

Original behavior by rafaelbandeira3 on GitHub.

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.

## Requirements ##

* CakePHP 2.x
* PHP 5.2+

## Installation ##

If you are using composer, add this to your composer.json file:
```json
	{
		"extra": {
			"installer-paths": {
				"Plugin/Linkable": ["lorenzo/linkable"]
		}
	},
		"require" : {
			"lorenzo/linkable": "master"
		}
	}
```

Otherwise just clone this repository inside your app/Plugin folder:

git clone git://github.com/lorenzo/linkable.git Plugin/Linkable

### Enable plugin

You need to enable the plugin your `app/Config/bootstrap.php` file:

``CakePlugin::load('Linkable');``

### Configuration

To use this behavior, add it to your AppModel:
```php
	<?php
		class AppModel extends Model {
		
			public $actsAs = array('Containable', 'Linkable.Linkable');
		
	}
```

## Usage

Here's an example using both linkable and containable:
```php
	<?php
	$this->TestRun->CasesRun->find('all', array(
		'link' => array(
			'User' => array('fields' => 'username'),
			'TestCase' => array('fields' => array('TestCase.automated', 'TestCase.name'),
				'TestSuite' => array('fields' => array('TestSuite.name'),
					'TestHarness' => array('fields' => array('TestHarness.name'))
					)
				)
			),
		'conditions' => array('test_run_id' => $id),
		'contain' => array(
			'Tag'
			),
		'fields' => array(
			'CasesRun.id', 'CasesRun.state', 'CasesRun.modified', 'CasesRun.comments'
			)
		));
```
Relationships:

* CasesRun is the HABTM table of TestRun <-> TestCases
* CasesRun belongsTo TestRun
* CasesRun belongsTo User
* CasesRun belongsTo TestCase
* TestCase belongsTo TestSuite
* TestSuite belongsTo TestHarness
* CasesRun HABTM Tags


Output SQL:
```sql
	SELECT `CasesRun`.`id`, `CasesRun`.`state`, `CasesRun`.`modified`, `CasesRun`.`comments`, `User`.`username`, `TestCase`.`automated`, `TestCase`.`name`, `TestSuite`.`name`, `TestHarness`.`name` FROM `cases_runs` AS `CasesRun` LEFT JOIN `users` AS `User` ON (`User`.`id` = `CasesRun`.`user_id`) LEFT JOIN `test_cases` AS `TestCase` ON (`TestCase`.`id` = `CasesRun`.`test_case_id`) LEFT JOIN `test_suites` AS `TestSuite` ON (`TestSuite`.`id` = `TestCase`.`test_suite_id`) LEFT JOIN `test_harnesses` AS `TestHarness` ON (`TestHarness`.`id` = `TestSuite`.`test_harness_id`) WHERE `test_run_id` = 32

	SELECT `Tag`.`id`, `Tag`.`name`, `CasesRunsTag`.`id`, `CasesRunsTag`.`cases_run_id`, `CasesRunsTag`.`tag_id` FROM `tags` AS `Tag` JOIN `cases_runs_tags` AS `CasesRunsTag` ON (`CasesRunsTag`.`cases_run_id` IN (345325, 345326, 345327, 345328) AND `CasesRunsTag`.`tag_id` = `Tag`.`id`) WHERE 1 = 1
```
If you were to try this example with containable, you would find that it generates a lot of queries to fetch all of the data records. Linkable produces a single query with joins instead.


### Filtering a parent model by records in a child model:
```php
<?php
$this->Article->find('all', array(
	'contain' => array(
		'Author'
	),
	'link' => array(
		'Comment'
	),
	'conditions' => array(
		'Comment.user_id' => 1
	)
));
```

The previous example will bring all articles having a comment done by user 1. Please note that if there is more than one comment
per article from a user, this query will actually return an Article record per each comment made. This is because Linkable
will use a single query using joins.

### version 1.1.1

- Using inner joins for filtering queries
- Bug fixes

### version 1.1:
- Brought in improvements and test cases from Terr. However, THIS VERSION OF LINKABLE IS NOT DROP-IN COMPATIBLE WITH Terr's VERSION!
- If fields aren't specified, will now return all columns of that model
- No need to specify the foreign key condition if a custom condition is given. Linkable will automatically include the foreign key relationship.
- Ability to specify the exact condition Linkable should use. This is usually required when doing on-the-fly joins since Linkable generally assumes a belongsTo relationship when no specific relationship is found and may produce invalid foreign key conditions. Example:

	$this->Post->find('first', array('link' => array('User' => array('conditions' => array('exactly' => 'User.last_post_id = Post.id')))))

- Linkable will no longer break queries that use SQL COUNTs


### More examples
Look into the unit tests for some more ways of using Linkable
