# Automatic API

## Get Automatic default

#### Method: GET

#### Endpoint: https://magicbadges.myshopkit.app/vge/magic-badges/v1/automatics

## Response

### Lỗi

```
export interface Response {
    message: "Message loi"
    code: Number
}
```

### Success

```ts
export interface Automatics {
    data: Data[]
    message: string
    status: string
}

export interface Data {
    items: Items[];
}

export interface Items {
    id: string
    config?: Config
    urlImage: string
    title: string
    postType: string
    description: string
    isSelected: boolean
}

export interface Config {
    placement: string
    size: number
    texts: any[]
}

```

## 2.Create Badges Automatic

#### method:post

### API endpoint:

https://magicbadges.myshopkit.app/vge/magic-badges/v1/automatics

##### body-param

param | type | description |default
--- | --- | ---| --- |
postType | string | - | -
badgeUrl | string | - | -
title | string | - | -
description | string | - | mô tả badge
config | string | config badges của sản phẩm | undefined

````ts
export interface Autobadge {
    data: Data
    message: string
    status: string
}

export interface Data {
    id: string
    date: string
}

````

## 3.Update,Patch Badges Automatic

#### method:PUT

### API endpoint:

https://magicbadges.myshopkit.app/vge/magic-badges/v1/automatics/:id

##### x-www-form-urlencoded

param | type | description |default
--- | --- | ---| --- |
title | string | - | -
badgeUrl | string | - | -
description | string | - | mô tả badge
status | (active/deactive) | trạng thái | -
config | string | config badges của sản phẩm | undefined

````ts
export interface Autobadge {
    data: Data
    message: string
    status: string
}

export interface Data {
    id: string
    date: string
}

````

## 4.Delete Badges Automatic

### API endpoint:

https://magicbadges.myshopkit.app/vge/magic-badges/v1/automatics/:id


##### x-www-form-urlencoded

param | type | description |default
--- | --- | ---| --- |

````ts
export interface Autobadge {
    data: Data
    message: string
    status: string
}

export interface Data {
    id: string,
    urlImage:string
}

````
