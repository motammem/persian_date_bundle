# Persian Date Bundle
Symfony2 PersianDateBundle
### Installation:

```composer
composer require mtm/persian-date-bundle:1.0.1
```

### Usage:

#### Form Type:
This bundle comes with a `mtm_persian_date_type` form type which prompt user 
for persian date at view layer and converts it to Standard PHP DateTime object at business layer.
So you can use Standard PHP DateTime object as form data.

Here is how to use it:

```php

$dateTime = new \DateTime('tomorrow');

$this->createForm('mtm_persian_date_type', $dateTime);

```

#### Twig Filter:
This bundle also adds a very handy twig filter `persian_date` to format Standard PHP DateTime object.

```html

<td>
    {{ post.createdAt| persian_date("Y-m-d') }}
</td>

```
