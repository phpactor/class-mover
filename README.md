Class Mover
===========

[![Build Status](https://travis-ci.org/phpactor/class-mover.svg?branch=master)](https://travis-ci.org/phpactor/class-mover)
[![StyleCI](https://styleci.io/repos/<repo-id>/shield)](https://styleci.io/repos/<repo-id>)

This is a library dedicated to refactoring class locations.

It takes care of:

- **Finding references to a class**: Find all references to a class (or
  classes).
- **Finding references to class methods**: Find references to a class method,
  or all method calls on a class, or all method calls ever.
- **Replacing references to the class**: Update any references in the code
  (using a given method, e.g. all under a path or all files in the git repo).
- **Modifying use statements**: update any use statements for the replaced
  class.
- **Adding use cases**: where necessary.

Why?
----

When using an editor such as VIM, one of the biggest issues I face is moving
classes and replacing their references - it is such a big issue for me
that I rarely do it.

Current approaches involve git moving the class, then running a for loop in
bash over a set of files and applying perl replace to them, piping the output
of that to a temporary file and then moving that temporary file to overwrite
the old one. And that doesn't always work well.

This package aims to provide a solid way of doing this, and can, for example,
be packaged in an 

Usage
-----

```bash
$targetClass = 'Acme\Blog\Post';
$replacementClass = 'Acme\Blog\Article';
$sourceCode = file_get_contents('SomeSource.php');

$classMover = new ClassMover();

$source = $classMover->replaceReferences(
    $classMover->findReferences($sourceCode, $targetClass)
    $replacementClass
);

echo (string) $source;
```
