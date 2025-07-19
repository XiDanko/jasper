# ts-enums-generator
`ts-enums-generator` is a Laravel package that generates TypeScript enums from PHP enums. It replicates the folder structure of the source directory using customizable naming conventions.

## Installation
To install the package, use Composer:
```bash
composer require xidanko/ts-enums-generator
```

## Configuration
You can publish the configuration file using the following command:
```bash 
php artisan vendor:publish --tag=ts-enums-generator-config
```

## Usage
To generate TypeScript enums from PHP enums, use the following command:
```bash 
php artisan ts-enums:generate --source=[default: "app/Enums"] --destination=[default: "resources/ts/enums"]
```

### Options
- `--source`: The directory containing the PHP enums. This option has a default value that can be set in the configuration file.
- `--destination`: The directory where the TypeScript enums will be generated. This option has a default value that can be set in the configuration file.

## Customization
You can customize the naming conventions for the created folders and files using the configuration file. The package will replicate the folder structure of the source directory using the provided naming conventions.

Available naming conventions are: `kebab`, `snake`, `studly`, and `camel`.

## License
This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
