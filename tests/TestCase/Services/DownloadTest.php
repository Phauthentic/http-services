<?php
declare(strict_types = 1);

namespace Phauthentic\Http\Services\Tests;

use Phauthentic\Http\Services\Download;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Download Test
 */
class DownloadTest extends TestCase
{
    protected $streamFactoryMock;
    protected $streamMock;
    protected $responseMock;
    protected $responseFactoryMock;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->createMocks();
    }

    /**
     * createMocks
     *
     * @return void
     */
    protected function createMocks(): void
    {
        $this->streamMock = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $this->streamFactoryMock = $this->getMockBuilder(StreamFactoryInterface::class)
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $this->responseFactoryMock = $this->getMockBuilder(ResponseFactoryInterface::class)
            ->getMock();

        $this->responseFactoryMock->expects($this->any())
            ->method('createResponse')
            ->willReturn($this->responseMock);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMissingFileNameException()
    {
        $download = new Download();
        $download->getResponse();
    }

    public function testExpiration()
    {
        $download = new Download($this->streamFactoryMock, $this->responseFactoryMock);
        $download = $download->expiresInDays(2)
            ->setFilename('test.jpg')
            ->setFileStream($this->streamMock)
            ->getResponse();
    }

}
