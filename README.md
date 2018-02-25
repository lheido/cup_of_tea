# Cup of Tea

An alternative to the Coffee module (Drupal) for training purpose.

## Installation

1. Add these repositories to your `composer.json`
```json
{
    "type": "package",
    "package": {
        "name": "lheido/cup_of_tea",
        "version": "1.0.1",
        "type": "drupal-module",
        "dist": {
            "url": "https://github.com/lheido/cup_of_tea/archive/1.0.1.zip",
            "type": "zip"
        }
    }
},
{
    "type": "package",
    "package": {
        "name": "ccampbell/mousetrap",
        "version": "v1.6.1",
        "type": "drupal-library",
        "dist": {
            "url": "https://github.com/ccampbell/mousetrap/archive/1.6.1.zip",
            "type": "zip"
        }
    }
}
```
2. Then `composer require ccampbell/mousetrap lheido/cup_of_tea`
3. Finally enable the module via `drush` or the BO.
