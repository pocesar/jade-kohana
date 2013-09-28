jade-kohana
===========

Jade templating engine on Kohana. This module works by compiling `.jade` templates to PHP files.

All compiled files goes to the `application/cache/jade` folder (that must be created and chmod'ed properly)

Usage
===========

You have two options:

#### Use the JadeTemplate instead of ControllerTemplate

```php
// Will look for template.jade by default, that is in views/template.jade
class Controller_Index extends JadeTemplate
{
    function action_index()
    {
        $this->template->greeting = 'Hello';
    }
}
```

#### Load the Jade views manually through JadeView class

```php
class Controller_Index extends Controller
{
    function action_index()
    {
        $this->response->body(JadeView::factory('path/to/file.jade'));
    }
}
```

This module extends from `Kohana_Controller` and `Kohana_View`, so it's _almost_ a drop-in replacement for existing controllers and views, behaves exactly like a `View` and `$jadeView instanceof View` returns true

You can, in your `application` folder, change the default `View` to use `JadeView`, but it will break view rendering for PHP views.

#### Known limitations

* Jade errors won't display a line number, making it very hard to spot indentation errors
* The compiled code cache is very crude and relies only on file modification times
* Result compiled php file is messy (that is a PHP Jade limitation)