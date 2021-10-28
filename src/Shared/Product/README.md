# Product Guide

Using Strategy pattern to make sure that We can integrate this project to unlimited platform.

## How can I access to a platform?

```php
$oPlatform = ProductFactory::setPlatform($platform);
```

List of available platform:

1. shopify
2. wordpress

## Supported Methods

List of supported method:

1. [getProductsBySlug](#getProductsBySlug)
2. [search](#search)
3. [getProducts](#getProducts)
4. [getCost](#getCost)
5. [hasNextPage](#hasNextPage)
6. [getLastCursor](#getLastCursor)

### Discover how it works

#### <a name="getProductsBySlug"></a> Method getProductsBySlug

##### Description:

```php
$oPlatform->getProductsBySlug(array $aSlug, $customerID);
```

It helps to get products by slug (If you are using Shopify, it named handle)

##### Parameters

1. $aSlug: A list of slug. EG: ['hello', 'world']
2. $customerID: The shop author id

##### Return values

```typescript
export interface Error {
    message: string,
    code: number
}
```

```typescript
export interface Success {
    message: string,
    data: {
        items?: Item
    }
}
```

<a name="itemformat"></a>Item Format

```typescript
export interface Item {
    id: string,
    title: string,
    createdAt: string, // '2021-08-06T05:00:22Z'
    priceRangeV2: {
        maxVariantPrice: {
            amount: string
        },
        minVariantPrice: {
            amount: string
        }
    }
}
```

#### <a name="search"></a> Method search

##### Description:

```php
$oPlatform->search(string $titleKeyword, $customerID, $isExtract = false, $limit = 50);
```

Searching products by title keyword

##### Parameters

1. $titleKeyword: For example: shirt
2. $customerID: The shop author id
3. isExtract: If this feature is true, it will return title that matches exactly like $titleKeyword
4. $limit: Maximum of items per request

##### Return values

```typescript
export interface Error {
    message: string,
    code: number
}
```

```typescript
export interface Success {
    message: string,
    data: {
        items?: Item
    }
}
```

Click to see [Item Format](#itemformat)

#### <a name="getProducts"></a> Method getProducts

##### Description:

```php
$oPlatform->getProducts$customerID, array $aArgs = []);
```

Retrieve all products in the store

##### Parameters

1. $customerID: The shop author id
2. $aArgs: It contains 2 keys: cursor and limit.

##### Return values

```typescript
export interface Error {
    message: string,
    code: number
}
```

```typescript
export interface Success {
    message: string,
    data: {
        items?: Item
    }
}
```

Click to see [Item Format](#itemformat)

#### <a name="search"></a> Method getProducts

##### Description:

```php
$oPlatform->getProducts$customerID, array $aArgs = []);
```

Retrieve all products in the store

##### Parameters

1. $customerID: The shop author id
2. $aArgs: It contains 2 keys: cursor and limit.

##### Return values

```typescript
export interface Error {
    message: string,
    code: number
}
```

```typescript
export interface Success {
    message: string,
    data: {
        items?: Item
    }
}
```

Click to see [Item Format](#itemformat)

#### <a name="getCost"></a> Method getCost

##### Description:

```php
$oPlatform->getCost();
```

Retrieve currently cost information.

Waring: You must execute a query before using this method.

##### Return values

```typescript
export interface Error {
    message: string,
    code: number
}
```


```typescript
export interface Success {
    message: string,
    data: {
        requestedQueryCost: number,
        actualQueryCost: number,
        throttleStatus: {
            maximumAvailable: number,
            currentlyAvailable: number,
            restoreRate: number
        },
    }
}
```
#### <a name="hasNextPage"></a> Method hasNextPage

##### Description:

```php
$oPlatform->hasNextPage();
```

Allowing you to know whether there is a next page or not

Waring: You must execute a query before using this method.

##### Return values

Returning TRUE if there is a next page. Otherwsie, returning FALSE

#### <a name="getLastCursor"></a> Method getLastCursor

##### Description:

```php
$oPlatform->getLastCursor();
```

Returning last cursor / id in the lastest query

Waring: You must execute a query before using this method.

##### Return values

Return a cursor STRING.
