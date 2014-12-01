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

## 字段+N
`incrby&incrva`

在很多场景下我们经常会用到+1这种操作 , 比如喜欢数,点赞数 ;或者加或者减,在 Pow 中只需要在调用 API 的时候多加上2个参数即可

`incrby` 指定被修改的字段,字段属性必须是数字类型

`incrva` 指定被修改的值,任任意数字, incrva=2 执行加2操作,incrva= -1 ,执行减一操作

例如 http://xxx?xxxx&incrby=like_count&incrva=1
在这里我们将对 linke_count 这个字段执行+1的动作


## 返回格式
目前支持 `json` 和`xml` 这两种

使用 format=json/xml 不穿则默认 json

例如 http://xxx?token=xxxxx&format=json
则返回 json 格式

## 搜索

假设一个获取所有信息的请求接口为
http://xxx/apple?version=1&token=8ch86oMZN6p1N1/TcSr9Fw==

我们想搜索 name 含有 app 的数据 则请求如下:

http://xxx/apple/ `search`?version=1&token=8ch86oMZN6p1N1/TcSr9Fw==& `name=app`

to be more!
