# Oreo

> 这里是描述

### Docker

``` bash
docker run \
    --name swoole \
    -d \
    -p 4000:4000/tcp \
    -p 4001:4001/tcp \
    -p 4002:4002/tcp \
    -p 4003:4003/tcp \
    -v /data/gini-modules:/data/gini-modules:rw \
    --restart=always \
    huangstomach/gini-swoole:latest
```
 
### 功能

* 登陆
* 获取人员信息

### 接口

#### 身份

* Type: `Restful`
* Origin: `auth`
* Method: `post`
* Requset:

    ``` javascript
    {
        username: '用户名',
        password: '密码'
    }
    ```

* Response:

    ```
    ref: '学号'
    ```

* Error

    ```
    {
        error: {
            code: '错误码',
            message: '错误信息'
        }
    }
    ```

* Description: 服务于用户登录

#### 人员

* Type: `Restful`
* Origin: `user`
* Method: `get/{ref: 学号}`
* Requset:

    ``` javascript
    {
        // 目前没有支持的参数
    }
    ```
* Response:

    ``` javascript
    {
        name: '姓名',
        type: '人员类型',
        ref_no: '学号',
        phone: '电话',
        email: '电子邮箱',
        card_no: '卡号',
        payroll_no: '??',
        lab: {
            code: '课题组代码',
            name: '课题组名称'
        },
        department: {
            code: '部门编号',
            name: '部门名称'
        },
        school: {
            code: '学校代码',
            name: '学校名称'
        },
        org: {
            code: '机构代码',
            name: '机构名称'
        },
        @source: 'database'
    }
    ```

* Error

    ```
    {
        error: {
            code: '错误码',
            message: '错误信息'
        }
    }
    ```

* Description: 服务用于获取用户信息


