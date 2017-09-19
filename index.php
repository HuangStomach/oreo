<?php
class Server {
    protected $server;

    public function __construct () {
        $this->server = new swoole_http_server('0.0.0.0', 3015);
        $this->server->set([
            'worker_num' => 8, // worker process num
            'backlog' => 128, // listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1
        ]);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('workerStart', [$this, 'workerStart']);
    }

    public function request ($req, $res) {
        // 按照swoole的参数传递来植入gini参数
        $uri = trim($req->server['request_uri'], '/');

        $content = \Gini\CGI::request($uri, [
            'get' => $req->get, 
            'post' => $req->post,
            'files' => [], // 暂且先不考虑file
            'route' => $uri,
            'method' => $req->server['request_method'],
        ])
        ->execute()
        ->content();
        
        $res->end(J($content));
    }

    public function workerStart ($server, $work) {
        // 我只是不想输出那个模板html
        ob_start();
        require "/usr/local/share/gini/lib/cgi.php";
        ob_end_clean();
    }

    public function run () {
        $this->server->start();
    }
}

$server = new Server();
$server->run();
