<?php
namespace Gini\Controller\CGI;

class Auth extends Base\Rest
{
    public function postDefault ($ref = 0) {
        $response = $this->error(400);
        $form = $this->form();

        if ($form['username'] && $form['password']) {
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
                $ref = $client->Auth->Verify($form['username'], $form['password']);
                if (!$ref) {
                    $response = $this->error(404, '用户名或密码错误!');
                    goto output;
                }
                
                $response = $ref;
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
