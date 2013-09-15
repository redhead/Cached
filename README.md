Cached
======
A Nette framework extension providing a Kdyby\Aop aspect for caching method return values.

Install
-------
Require the extension file using composer:

```sh
$ composer require redhead/cached:@dev
```

In you Nette application configuration, add the extension to be registeres and the aspect:

```
extensions:
		cached: Cached\CachedExtension
		
aspects:
  - Cached\CachingAspect(@cacheStorage)
```

where @cacheStorage is a reference to a service implementing Nette\Caching\IStorage
(you can specify your own storage, or remove the argument so the dependecy will by autowired)



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
- sliding (boolean) - if the sliding feature be used
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

Then add property 'profile' with the name of the profile as a value to theadvised methods you want to 
share the options with.

```
...
  /**
   * @Cached\Cached(profile="myProfile")
   */
...
```

You can set additional options or override the options of the profile by adding the properties to the annotation.

When no profile is specified, it defaults to profile 'default', which you can set in the configuration.
The options for this profile will be used for every adviced method without property 'profile'.
