<?php
namespace Swiftgift\Gift\Test\Unit;
use PHPUnit\Framework\TestCase;

class HelperDataTest extends TestCase {

    protected $helper;

    protected function createHelper($conf) {
        $h = new \Swiftgift\Gift\Helper\Data(
            $this->createMock(\Magento\Framework\App\Helper\Context::class),
            $conf,
            $this->createMock(\Swiftgift\Gift\Utils::class)
        );
        return $h;
    }

    public function test() {
        $conf = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $data_base = [
            'swiftgift/main/api_base_url'=>'url',
            'swiftgift/main/client_secret'=>'client_secret'
        ];
        $data = array_merge($data_base, []);
        $conf->method('getValue')->willReturnCallback(function($path) use (&$data) {
            return isset($data[$path]) ? $data[$path] : NULL;
        });
        $h = $this->createHelper($conf);
        $this->assertEquals($h->isCanUse(), TRUE);
        foreach ($data_base as $k=>$v) {
            $data = array_merge($data_base, [$k=>NULL]);
            $this->assertEquals($h->isCanUse(), FALSE);
        }
        $data = [];
        $this->assertEquals($h->isCanUse(), FALSE);
    }
    
}
