<?php
namespace Gini\Controller\CGI;

class User extends Base\Rest
{
    public function getDefault ($ref = 0) {
        $response = $this->error(400);
        $form = $this->form();

        if ($ref) {
            try {
                class_exists('\Gini\RPC');
                $config = \Gini\Config::get('app.rpc')['gateway'];
                $client = \Gini\IoC::construct('\Gini\RPC', $config['url']);
                // 先去gateway通过应用认证
                if (!$client->Gateway->Authorize($config['clientId'], $config['clientSecret'])) {
                    $response = $this->error(401);
                    goto output;
                }

                // 获取西南人员信息
                $user = $client->Gateway->People->GetUser($ref);
                if (!$user['email']) {
                    $response = $this->error(400, '该用户没有邮箱');
                    goto output;
                }

                // 获取yiqikong-user信息
                $config = \Gini\Config::get('app.rpc')['yiqikong-user'];
                $client = \Gini\IoC::construct('\Gini\RPC', $config['url']);
                $result = $client->YiQiKong->User->GetInfo($user['email']);
                if ($result) {
                    $user['id'] = $result['id'];
                    $user['gapper_id'] = $result['gapper_id'];
                    $response = $user;
                    goto output;
                }

                // 不是yiqikong用户则去注册
                $result = $client->YiQiKong->User->Create([
                    'name' => $user['name'],
                    'institution' => $user['school']['name'],
                    'email' => $user['email'],
                    'password' => "Swu{$user['phone']}",
                    'phone' => $user['phone'],
                ]);
                if (!$result) {
                    $response = $this->error(500, '注册用户失败');
                    goto output;
                }

                // 成功后去取yiqikong-user信息
                $result = $client->YiQiKong->User->GetInfo($user['email']);
                $user['id'] = $result['id'];
                $user['gapper_id'] = $result['gapper_id'];
                if (!$result) {
                    $response = $this->error(404, '用户未找到');
                    goto output;
                }

                // 获取到信息就去做lab链接
                $result = $client->YiQiKong->User->Connect($result['id'], 'swu');
                if (!$result) {
                    $response = $this->error(500, '与西南大学关联失败');
                    goto output;
                }
                
                $response = $user;
            }
            catch (\Gini\RPC\Exception $e) {
                $response = $this->error($e->getCode(), $e->getMessage());
                goto output;
            }
        }

        output:
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $response);
    }
}
