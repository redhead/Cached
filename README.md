Cached
======
A Nette framework extension providing a Kdyby\Aop aspect for caching method return values.

Installation
------------

First, [install the Kdyby\Aop extension](https://github.com/Kdyby/Aop/blob/master/docs/en/index.md#installation).

Require the extension file using composer:

```sh
$ composer require redhead/cached:@dev
```

In you Nette application configuration, add the extension to be registered and Kdyby\Aop aspect:

```
extensions:
		cached: Cached\CachedExtension
		
aspects:
  - Cached\CachingAspect(@cacheStorage)
```

where @cacheStorage is a reference to a service implementing Nette\Caching\IStorage
(you can specify your own storage, or remove the argument so the dependecy will be autowired)



Usage
-----

Now, you can put annotation @Cached on methods in your services like this:

```php
class MyService {

  /**
   * @Cached\Cached
   */
  public function getNumber() {
    return rand(1, 1000);
  }
  
}
```

Note: due to a bug in Kdyby\Aop, you can't import the annotation in this moment.
Until it's fixed you have to provide fully qualified name of the annotation (@Cached\Cached)

After the first call of the method above, the return value is cached.
Every other call will not execute the method and will return the cached value instead.



Annotation properties
---------------------

There are few properties you can use in the annotation. They specify the caching options.
See the example:

```php
...
  /**
   * @Cached\Cached(key="myService.number", sliding=true, expire="+1 hour")
   */
  public function getNumber() {
    return rand(1, 1000);
  }
...
```

Annotation options are following:

- namespace (string) - the namespace to use for caching 
- key (string) - the key under which the return value is stored in the cache storage
- profile (string) - the name of the cache profile to use, defaults to 'default' (see below)
- expire (string) - specifies the time the cache will expire
- sliding (boolean) - if the sliding feature should be used
- tags (array) - tags for clearing the cache
- files (array) - the paths to files that will trigger the cache expiration when edited
- priority (integer) - the priority number


Cache profiles
--------------

You can specify the settings for the above options that are shared across many adviced methods,
so you don't need to write them all the time. The settings will be in one place - in your config file!

To create a profile, add section 'cached' and subsection 'profiles' to your configuration.
Then add another subsection bearing the name of your profile, then set the profile options. Like this:

```
cached:
  profiles:
    myProfile:
      sliding: true
      expire: +1 hour
```

Then add annotation property 'profile' with the name of the profile as a value to the advised methods you want to 
share the options with.

```
...
  /**
   * @Cached\Cached(profile="myProfile")
   */
...
```

You can set additional options and/or override the options of the profile by adding properties to the annotation.

When no profile is specified, it defaults to profile 'default', which you can set in the configuration.
The options for this profile will be used for every adviced method without property 'profile'.



Profile options
---------------

Profile options have few differences from the annotation options. You can specify these options:
- enabled (boolean) - if false, no caching is performed and the original method will get called every time (caching is completely disabled for this profile)
- namespace (same as above)
- expire (same as above)
- sliding (same as above)
- tags (same as above)
- files (same as above)
- priority (same as above)

Profile options are missing 'key' and 'profile' annotation property counterparts (as they don't make any sense here).



Extension options
-----------------

You can specify options for the whole extension in section 'cached':

- enabled (boolean) - if false, caching is disabled for all annotations and profiles, no caching is performed at all.


Configuration example
---------------------

Here is an example of the whole configuration setup and usage of every config option:


```
  extensions:
  	aop: Kdyby\Aop\DI\AopExtension
    	annotations: Kdyby\Annotations\DI\AnnotationsExtension
  	cached: Cached\CachedExtension
  
  aspects:
  	- Cached\CachingAspect
  
  cached:
  	enabled: true
  	profiles:
  		default:
  			expire: 1 hour
  			sliding: true
  			files: [file1.php, file2.php]
  			tags: [tag1, tag2]
  			priority: 1
  		myProfile:
  			...
```
