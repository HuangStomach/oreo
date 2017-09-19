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
                $rpc = \Gini\IoC::construct('\Gini\RPC', $config['url']);
                if (!$rpc->Gateway->authorize($config['clientId'], $config['clientSecret'])) {
                    $response = $this->error(401);
                    goto output;
                }

                $user = $rpc->Gateway->People->GetUser($ref);
                $response = $user;
            }
            catch (\Gini\RPC\Exception $e) {
                $response = $this->error($e->getCode(), $e->getMessage());
            }
            goto output;
        }

        output:
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $response);
    }
}
