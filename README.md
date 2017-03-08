## 深绘开放平台SDK

```
     _____    _______  _______  _____      _____    _____    __  __                __
    / ___ \  / _____/ / _____/ / ___ \    / ___ \  / __  \  /  \ \ \      /\      / /
   / /  / / / /____  / /____  / /__/ /   / /  / / / /__/ / / /\ \ \ \    /  \    / /
  / /  / / / _____/ / _____/ / _____/   / /  / / /   ___/ / /__\ \ \ \  / /\ \  / /
 / /__/ / / /____  / /____  / /        / /__/ / / /\ \   / ______ \ \ \/ /  \ \/ /
/______/ /______/ /______/ /_/        /______/ /_/  \_\ /_/      \_\ \__/    \__/

```                    
> 该SDK用于访问深绘开放平台，使用方法：

####1. 创建客户端
```
include_once 'deepdraw.open.sdk/client/Client.php';	//sdk代码包位置根据你自己的情况而填。
use cn\deepdraw\api\rest\client\Client as Client;		//也可以不声明命名空间，在使用的时候填全路径。
$client = new Client ( $server_url, $app_key, $app_secret );
```
><small> 参数说明：</small><br />
><small> $server_url	开放平台服务器地址，填http://open.soomey.net。</small><br />
><small> $app_key	授权信息。填23618344。</small><br />
><small> $app_secret	授权信息。填d489895c2ad895fe4a4fdfc4586bedcc。</small><br />

####2. 发起请求
```
$respone = $client->get ($api_url, $querys, $headers);
print_r($respone->getBody());	//除了通过调用getBody()获得请求体，还可以调用getHeaders()、getStatusCode()分别获得响应头和状态码。
```
><small> 参数说明：</small><br />
><small> $api_url		api的url地址，具体可以参考《api参数说明》。</small><br />
><small> $querys		请求中必要的查询参数，比如分页信息的每页条目数和启始页码。有些api没有要求提供查询参数。具体可以参考《api参数说明》。</small><br />
><small> $headers	请求中必要的http头信息。有些api没有要求提供头信息。具体可以参考《api参数说明》。</small><br />
