# API 攻略




## 分页
`page&count`

在系统中 默认返回20条数据,如果想要更多的数据请通过 page 和 count 来控制即可

## 数据不输出

 `skip_field & skip_data`
 skip_field 指定字段, skip_data 指定值多值用逗号分隔

 在某些清空下,我们在请求一些接口的时候会希望API 跳过一些值输出(可能本地已经有,或者不在需要)这个时候可以使用次功能

 比如 接口返回如下结果
```
 {
   {
    id:1
    name:iphone5
   },
   {
    id:2
    name:iphone5s
   },
   {
    id:3
    name:iphone6
   },
    {
    id:4
    name:iphone6
   },
   ....
 }

```
假设我们期望在调用接口的时候不返回 id 为3,4,5的数据
那么我们只需要在调用接口的时候加上  `skip_field=id & skip_data=3,4,5` 这样就不会输出3,4,5 这三条数据了


## 字段不输出
`less`

有时候会输出很多数据,若有些数据不想用的话可以用 less=filed1,filed2 ,这样 API 就不会输出filed1 和 filed2字段
比如接口返回的时候会默认带上 id,created_at...,

如果你不希望接口输出 `id` 和`created_at`字段
那么请求的时候加上  `&less=id,created_at`  那么接口就不会输出这些字段,


to be more!
