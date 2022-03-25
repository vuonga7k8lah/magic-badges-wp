# Product API

## Get manuals Products

#### Method: GET

#### Endpoint: https://website.com/wp-json/magic-badges/v1/manual-products

## Params

| Param | Type | Description | Default |
| --- | --- | ----| --- |
| limit| int | Số lượng items / trang. Maximum: 30. Quá 30 sử dụng default | 30 |
| page | string |  | 1 |
| s | string | search sản phẩm |  |
| status | 'active' / 'deactive' / 'any' | Trường hợp all thì trả về cả popups active và deactive| any |
| ?pluck | string | Mỗi pluck cách nhau bởi dấu phẩy. Ví dụ: title, id. Trường hợp không có pluck trả lai hết| undefined|

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
export interface Products {
    data: Data[]
    message: string
    status: string
}

export interface Data {
    items: Items;
    maxPages: number
}

export interface Items {
    id: string
    title: string
    price: Price
    image: Image
    slug: string
    /** manual config badges**/
    manual: Manual
    isSelected: boolean
}

export interface Manual {
    config: object,
    urlImage: string,
    'id': string,
}

export interface Price {
    maxVariantPrice: MaxVariantPrice
    minVariantPrice: MinVariantPrice
}

export interface MaxVariantPrice {
    amount: string
}

export interface MinVariantPrice {
    amount: string
}

export interface FeaturedImage {
    height: number
    src: string
    width: number
}
```

## Get full Products

#### Method: GET

#### Endpoint: https://website.com/vge/magic-badges/v1/full-products

## Params

| Param | Type | Description | Default |
| --- | --- | ----| --- |
| limit| int | Số lượng items / trang. Maximum: 30. Quá 30 sử dụng default | 10 |
| s | string | search sản phẩm |  |
| orderby | ProductSortKeys | lọc theo dữ liệu nào | TITLE |
| status | 'active' / 'deactive' / 'any' | Trường hợp all thì trả về cả popups active và deactive| any |
| ?pluck | string | Mỗi pluck cách nhau bởi dấu phẩy. Ví dụ: title, id. Trường hợp không có pluck trả lai hết| undefined|

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
export interface Products {
    data: Data[]
    message: string
    status: string
}

export interface Data {
    items: Items;
    hasNextPage: boolean
}

export interface Items {
    id: string
    title: string
    createdAt: string
    priceRangeV2: PriceRangeV2
    featuredImage: FeaturedImage
    handle: string
    /** manual config badges**/
    manual: Manual
    isSelected: boolean
}

export interface Manual {
    config: object,
    urlImage: string,
    id: string,
}

export interface PriceRangeV2 {
    maxVariantPrice: MaxVariantPrice
    minVariantPrice: MinVariantPrice
}

export interface MaxVariantPrice {
    amount: string
}

export interface MinVariantPrice {
    amount: string
}

export interface FeaturedImage {
    height: number
    src: string
    width: number
}
```

## 2.Create Badges Manual

#### method:post

### API endpoint:

https://website.com/vge/magic-badges/v1/manual-products

##### body-param

param | type | description                     |default
--- | --- |---------------------------------| --- |
shopName | string | Tên của shop                    | undefined
badgeUrl | number | Đường dẫn ảnh của default badge | undefined
slugs | string | tên sản phẩm dưới dạng slug     | undefined
config | string | config badges của sản phẩm      | undefined

````ts
export interface Manual {
  data: Data
  message: string
  status: string
}

export interface Data {
  items: Item[]
}

export interface Item {
  id: string
  slug: string
  date: string
}

````
## 3.Update Badges Manual

#### method:PUT

### API endpoint:

https://website.com/vge/magic-badges/v1/manual-products

##### x-www-form-urlencoded

param | type | description |default
--- | --- | ---| --- |
shopName | string | Tên của shop | undefined
status | (active/deactive) | status manual | undefined
badgeUrl | number | Đường dẫn ảnh của default badge | undefined
ids | string | id của manual badge | undefined
slugs | string | tên sản phẩm dưới dạng slug | undefined
config | string | config badges của sản phẩm | undefined

````ts
export interface Manual {
    data: Data
    message: string
    status: string
}

export interface Data {
    items: Item[]
}

export interface Item {
    id: string
    slug: string
    date: string
}
````

## 3.Update Badge Manual

#### method:PUT

### API endpoint:

https://website.com/vge/magic-badges/v1/manual-products/:id

##### x-www-form-urlencoded

param | type | description |default
--- | --- | ---| --- |
shopName | string | Tên của shop | undefined
status | (active/deactive) | status manual | undefined
badgeUrl | number | Đường dẫn ảnh của default badge | undefined
slug | string | tên sản phẩm dưới dạng slug | undefined
config | string | config badges của sản phẩm | undefined

````ts
export interface Manual {
  data: Data
  message: string
  status: string
}

export interface Data {
  items: Item
}

export interface Item {
    id: string
    slug: string
    date: string
}
````

## 4.Delete Badge Manual

### API endpoint:

https://website.com/vge/magic-badges/v1/manual-products/:id

````ts
export interface Manual {
    data: Data
    message: string
    status: string
}

export interface Data {
    id: string
}

````

## 5.Delete multi Badge Manuals

### API endpoint:

https://website.com/vge/magic-badges/v1/manual-products

##### x-www-form-urlencoded

param | type | description |default
--- | --- | ---| --- |
ids | string | list id badges | -

````ts
export interface Manual {
    data: Data
    message: string
    status: string
}

export interface Data {
    id: string
}

````
