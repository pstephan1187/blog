>> How to setup a disposable testing database for Laravel 5

I recently started a new project at work that was going to be based on Laravel 5. I have only recently started learning unit testing and I wanted to implement unit testing on this project (as a note, all production projects need to use some kind of test suite). Right now I really like the simplicity and power of Jeffrey Way's <a href="https://github.com/laracasts/Integrated" target="_blank">Integrated</a> package, so I chose to use that (the Integrated package extends PHPUnit, so you can use them together).

Setting it up is very simple on Laravel (this post will not cover that, but if you need, you can find good directions <a href="https://github.com/laracasts/Integrated/wiki/Installation-and-Setup" target="_blank">here</a>). After setting up some tests, I quickly discovered I wanted to be able to check my database for insertions and deletions and the like. I didn't want to run tests agains the main database because I didn't want any of the test data to corrupt it. So I decided to setup a database specifically for testing.

I didn't want, or need, to use the full power of MySQL, so I didn't want to go through the trouble of setting up a database. Instead, SQLite was simple and quick to get setup and would be great for testing.

### Setting up the Database

Laravel comes ready to use SQLite out of the box. You can see in the `config/database.php` file:

```php
'sqlite' => [
	'driver'   => 'sqlite',
	'database' => storage_path().'/database.sqlite',
	'prefix'   => '',
],
```

The only thing you need to do is create the database. In the root directory of your project, you can run this command:

```
touch storage/database.sqlite
```

### Configuring PHPUnit/Integrated

Now we don't want to use that database for our application so we won't set it to the default database. Instead, we only want to use it in our tests. So we need to do a little setup. In the `tests/TestCase.php` file that ships with Laravel, we need to add this method:

```php
public function prepareForTests(){
	Config::set('database.default', 'sqlite');
	Artisan::call('migrate');
}
```

This will do two things. First, it will set the default database to our test database for our test suite. Second, it runs any migrations against the database that have been added since our last test.

> Alternatively you could run `Artisan::call('migrate:refresh')` to rerun all migrations, thereby clearing out any data in your database.

We need to make sure this is run so we need to add it to our test setup. PHPUnit runs a `setUp()` method before tests are run, so we can extend that method. At the top of your `tests/TestCase.php` file, you will want to add this:

```php
public function setUp(){
	parent::setUp();

	$this->prepareForTests();
}
```

And your ready to go! Now you can utilize your test database in all your tests without worrying about filling your main database full of bogus data. Enjoy!