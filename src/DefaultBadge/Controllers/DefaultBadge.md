# Badge API

## 1.Get badges

#### Method: GET

#### Endpoint: https://website.com/vge/magic-badges-wp/v1/default-badges

## Params

````ts
export interface Badges {
    /**limit Số lượng items / trang. Maximum: 30. Quá 30 sử dụng default 10*/
    limit: int
    /** page là page hiện tại default 1*/
    page: int,
    /** s là search */
    s: string,
    /** Mỗi pluck cách nhau bởi dấu phẩy. Ví dụ: title, id. Trường hợp không có pluck trả lai hết*/
    pluck?: Pluck,
    /** taxSlugs là tag của mỗi badge,ví dụ taxSlugs="on-sale,on_sale"*/
    taxSlugs?: string
    /** taxName là tên cuả taxonomy */
    taxName?: string
}

export interface Pluck {
    urlImage: string,
    id: string,
    title: string,
    date: string
}
````

### Success

```ts
export interface Products {
    data: Data[]
    message: string
    status: string
}

export interface Data {
    items: Items
    maxPage: int
}

export interface Items {
    id: string
    urlImage: string,
    'taxonomy': Taxonimy
}

export interface Taxonimy {
    slugs: string[],
    name: string
}
```

## 2.create badge

#### Method: post

#### Endpoint: https://website.com/vge/magic-badges-wp/v1/default-badges

##header
Authorization: mã application_password xác thực

## Params

````ts
export interface Badges {
    /** content object dữ liệu ảnh*/
    content: Object
    /** taxonomy của ảnh taxonomy */
    keywords ?: string
}

````

### Success

```ts
export interface Badges {
    data: Data[]
    message: string
    status: string
}

export interface Data {
    id: string
}

```
