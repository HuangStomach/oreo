<?php
namespace Gini\Controller\CGI;

class Hello extends Base\Rest
{
    public function getDefault () {
        $response = $this->error(400);
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $response);
    }
}
