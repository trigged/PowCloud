
#前言

##使用步骤

* 创建对应的路径（api的路径)

* 创建对应的表（api的字段）

* 创建对应的表单（api的内容）

* 对相关人员开启视图的可视权限（权限管理中设置）

* 在内容管理中查看并添加，删除，更新数据；（对应权限的人）


##缓存，DB和后台，api的对应关系

* api中的内容优先取自缓存;

* 一般情况下，DB的修改会同步redis;

* 后台会同步DB的数据并显示，且记录该条数据是否有在redis中；

* 我们手动刷新redis刷的是他的结构，redis中的数据同步由redis和DB之间的同步规则来生效；

* Api首先去读缓存中的信息，如果发现该条数据未在缓存中，会去DB中寻找数据，并且同步redis和后台的是否缓存的状态；如果后台的显示缓存的状态未同步更新，则说明是bug；

* 修改一条数据的时候，生效后同步DB，再同步redis;

##接口说明

`api` 输出结果的时候会屏蔽create_at, updated_at, deleted_at, 三个字段

cms的api定义参考文档：http://p.demo1.pptv.com/w/pptv_launcher/server_api

cms的api测试用例参考：atv后端-cms-api-testcase.xlsx
