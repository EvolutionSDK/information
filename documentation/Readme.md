About the Information Bundle
================================
The information bundle extends SQL to allow custom information on any model.

## Usage

```php
$info = $model->_->information;

// Required - Choose a namespace (system or user)
$info->__type = 'user';

// Read a record
$info->someRecord;

// Update a record
$info->someRecord = 'someValue';
```