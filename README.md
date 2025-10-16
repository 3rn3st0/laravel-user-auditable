# Laravel User Auditable

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D9.0-FF2D20.svg)](https://laravel.com)

**Author:** Ernesto Chapon ([3rn3st0](https://github.com/3rn3st0))

A Laravel package that provides user auditing capabilities for your database tables and Eloquent models. Easily track which users create, update, and delete records in your application.

## Features

- 🕵️ **User Auditing**: Automatically track `created_by`, `updated_by`, and `deleted_by`
- 🔧 **Flexible Macros**: Schema macros for easy migration creation
- 🎯 **Multiple Key Types**: Support for ID, UUID, and ULID
- 🏷️ **Relationships**: Built-in relationships to user models
- 📊 **Query Scopes**: Easy filtering by user actions
- ⚡ **Zero Configuration**: Works out of the box

## Installation

```bash
composer require tu-usuario/laravel-user-auditable
```

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=user-auditable-config
```

## Usage

### Migrations

Use the provided macros in your migrations:

```php
// Basic usage with default values
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->fullAuditable(); // Uses 'users' table and 'id' key type
});

// Custom user table and UUID key type
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->fullAuditable('admins', 'uuid');
});

// Only user auditing columns
Schema::create('settings', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->text('value');
    $table->userAuditable('users', 'ulid');
});
```

### Models

Use the `UserAuditable` trait in your Eloquent models:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TuUsuario\UserAuditable\Traits\UserAuditable;

class Post extends Model
{
    use SoftDeletes, UserAuditable;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
```

### Relationships

The trait automatically provides relationships:

```php
$post = Post::first();

// Get the user who created the post
$creator = $post->creator;

// Get the user who updated the post
$updater = $post->updater;

// Get the user who deleted the post (if using soft deletes)
$deleter = $post->deleter;
```

### Query Scopes

Filter records by user actions:
```php
// Get all posts created by user with ID 1
$posts = Post::createdBy(1)->get();

// Get all posts updated by user with ID 2
$posts = Post::updatedBy(2)->get();

// Get all posts deleted by user with ID 3
$posts = Post::deletedBy(3)->get();
```

## Available Macros

    * `userAuditable($userTable = 'users', $keyType = 'id')`

    * `dropUserAuditable($dropForeign = true)`

    * `fullAuditable($userTable = 'users', $keyType = 'id')`

    * `uuidColumn($columnName = 'uuid')`

    * `ulidColumn($columnName = 'ulid')`

    * `statusColumn($columnName = 'status', $default = 'active')`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License](LICENSE.md) File for more information.
