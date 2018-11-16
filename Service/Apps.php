<?php
namespace Service;

use Tools\Logger;

/**
 * Class Apps
 *
 * @package Service
 */
abstract class Apps implements AppsBasic {
    public const ALLOWED_MODIFIED_FIELD = [
        'name',
        'birthday',
        'height',
        'weight',
        'longitude',
        'latitude',
        'avatar',
        'description',
        'city_settled',
        'hometown',
        'role',
        'blood_type',
        'mate',
        'ethnicity',
        'passwd',
        'email',
        'payment_salt',
        'payment_code',
        'payment_salt_oversea',
        'payment_code_oversea',
    ];
    protected $params = [];

    /**
     * @var Logger
     */
    protected $logger = null;

    /***
     * @var Api
     */
    protected $api    = null;

    /**
     * @var \Service\Request
     */
    protected $request= null;
    protected $body   = '';

    /***
     * 初始化.
     */
    public function init() {
        $this->request      = Request::initRequest();
        $this->logger       = new Logger($this->request);
        $this->api          = new Api($this->request);
        $this->params       = $this->request->getParams();
        $this->body         = $this->request->getBody();

        //为了安全，我先来统一处理page和size参数
        //页数及每页条数
        $this->params['page'] = $this->params['page'] ?? 1;
        $this->params['size'] = $this->params['size'] ?? 100;
        $this->params['page'] = $this->params['page'] < 0 || $this->params['page'] >= 1000
                                    ? 1 : $this->params['page'];
        $this->params['size'] = $this->params['size'] < 0 || $this->params['size'] >= 100
                                    ? 100 : $this->params['size'];
    }

    /***
     * @param int        $code
     * @param string     $message
     * @param array|null $data
     * @return array
     */
    public function packagingHttpResponse(int $code = 200, string $message = '', ?array $data = []): array{
        return [
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ];
    }


    /**
     * 鉴权
     * 如果接口需要进行权限验证，就从这里开始好了，Service\Apps已经抽象出一个 authentication
     *
     * @return mixed
     */
    abstract function authentication();

    /***
     * Class 执行完毕之后必须调用的方法
     * 默认已实现，各个接口如需要，请自己继承后实现
     *
     * @return mixed
     */
    public function done() :void {
        $endTime= microtime(true);
        $spend  = $endTime - $this->request->getRequestTime();
        $spend  = round($spend, 2);
        $this->logger->success("Run Ok[{$spend}s]!!!");
    }

    /***
     * 必须要有一个默认的方法
     * 默认执行的ACTION，Service\Apps中已抽象出一个 execute 所有继承该类的都需要实现execute
     *
     * @return mixed
     */
    abstract function execute();
}