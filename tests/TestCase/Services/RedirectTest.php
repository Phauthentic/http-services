<?php
declare(strict_types = 1);

namespace Phauthentic\Http\Services\Tests;

use Phauthentic\Http\Services\Redirect;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * RedirectTest
 */
class RedirectTest extends TestCase
{
    /**
     * getResponseFactoryMock
     */
    protected function getResponseFactoryMock($responseMock)
    {
        $responseFactoryMock = $this->getMockBuilder(ResponseFactoryInterface::class)
            ->getMock();

        $responseFactoryMock->expects($this->any())
            ->method('createResponse')
            ->willReturn($responseMock);

        return $responseFactoryMock;
    }

    /**
     * testTo
     *
     * @return void
     */
    public function testTo(): void
    {
        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $responseMock->expects($this->once())
            ->method('withStatus')
            ->with(301)
            ->willReturnSelf();

        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/home')
            ->willReturnSelf();

        Redirect::to($responseMock, '/home', 301);
    }

    /**
     * testCreate
     *
     * @return void
     */
    public function testCreate(): void
    {
        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $responseMock->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/home')
            ->willReturnSelf();

        $responseFactoryMock = $this->getResponseFactoryMock($responseMock);

        $redirect = new Redirect($responseFactoryMock);
        $result = $redirect->create('/home');
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
